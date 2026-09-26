<?php

use App\Models\Categoria;
use App\Models\PuntoInteres;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Categoria::all()->each(function (Categoria $categoria) {
            $modulos = $categoria->modulos_defecto ?? [];
            if (!in_array('estadisticas', $modulos)) {
                $categoria->update(['modulos_defecto' => [...$modulos, 'estadisticas']]);
            }
        });

        PuntoInteres::where('es_cliente', true)->get()->each(function (PuntoInteres $punto) {
            $modulos = $punto->modulos_habilitados ?? [];
            if (!in_array('estadisticas', $modulos)) {
                $punto->update(['modulos_habilitados' => [...$modulos, 'estadisticas']]);
            }
        });
    }

    public function down(): void
    {
        // no-op: no revertimos backfill de datos
    }
};
