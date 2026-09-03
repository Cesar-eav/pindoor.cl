<?php

namespace App\Console\Commands;

use App\Models\EventoEntrada;
use App\Services\FlowService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpirarEntradasPendientes extends Command
{
    protected $signature = 'entradas:expirar-pendientes';

    protected $description = 'Marca como expiradas las entradas de eventos pendientes de pago cuyo plazo venció, liberando su cupo';

    public function __construct(private FlowService $flow)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $pendientes = EventoEntrada::where('estado', 'pendiente')->whereNotNull('flow_token')->get();

        foreach ($pendientes as $entrada) {
            try {
                $estadoFlow = $this->flow->obtenerEstadoPago($entrada->flow_token);
                EventoEntrada::aplicarEstadoFlow($entrada->id, $estadoFlow);
            } catch (\Throwable $e) {
                Log::warning('ExpirarEntradasPendientes: no se pudo refrescar estado Flow', [
                    'entrada' => $entrada->codigo_entrada,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $total = EventoEntrada::where('estado', 'pendiente')
            ->where('expira_en', '<', now())
            ->update(['estado' => 'expirada']);

        $this->info("Entradas expiradas: {$total}");

        return self::SUCCESS;
    }
}
