<?php

namespace App\Console\Commands;

use App\Models\PuntoInteres;
use Illuminate\Console\Command;

class NormalizarTags extends Command
{
    protected $signature   = 'tags:normalizar';
    protected $description = 'Re-guarda los tags de puntosinteres para que los acentos queden en UTF-8 directo (no escapados como ó), corrigiendo falsos positivos en la búsqueda por palabra completa';

    public function handle(): int
    {
        $total = PuntoInteres::whereNotNull('tags')->count();
        $this->info("Puntos con tags: {$total}");

        $actualizados = 0;
        PuntoInteres::whereNotNull('tags')->chunkById(100, function ($puntos) use (&$actualizados) {
            foreach ($puntos as $punto) {
                $punto->tags = $punto->tags; // dispara el set() con JSON_UNESCAPED_UNICODE
                $punto->saveQuietly();
                $actualizados++;
            }
        });

        $this->info("Actualizados: {$actualizados}");

        return self::SUCCESS;
    }
}
