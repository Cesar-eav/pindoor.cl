<?php

namespace App\Console\Commands;

use App\Models\ReservaRuta;
use App\Services\FlowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirarReservasPendientes extends Command
{
    protected $signature = 'reservas:expirar-pendientes';

    protected $description = 'Marca como expiradas las reservas de rutas pendientes de pago cuyo plazo venció, liberando su cupo';

    public function __construct(private FlowService $flow)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $pendientes = ReservaRuta::where('estado', 'pendiente')->whereNotNull('flow_token')->get();

        foreach ($pendientes as $reserva) {
            try {
                $estadoFlow = $this->flow->obtenerEstadoPago($reserva->flow_token);
                ReservaRuta::aplicarEstadoFlow($reserva->id, $estadoFlow);
            } catch (\Throwable $e) {
                Log::warning('ExpirarReservasPendientes: no se pudo refrescar estado Flow', [
                    'reserva' => $reserva->codigo_reserva,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $total = ReservaRuta::where('estado', 'pendiente')
            ->where('expira_en', '<', now())
            ->update(['estado' => 'expirada']);

        $this->info("Reservas expiradas: {$total}");

        return self::SUCCESS;
    }
}
