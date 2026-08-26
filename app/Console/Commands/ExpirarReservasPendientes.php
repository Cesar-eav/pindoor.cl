<?php

namespace App\Console\Commands;

use App\Models\ReservaRuta;
use Illuminate\Console\Command;

class ExpirarReservasPendientes extends Command
{
    protected $signature = 'reservas:expirar-pendientes';

    protected $description = 'Marca como expiradas las reservas de rutas pendientes de pago cuyo plazo venció, liberando su cupo';

    public function handle(): int
    {
        $total = ReservaRuta::where('estado', 'pendiente')
            ->where('expira_en', '<', now())
            ->update(['estado' => 'expirada']);

        $this->info("Reservas expiradas: {$total}");

        return self::SUCCESS;
    }
}
