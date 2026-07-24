<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // El importador de Passline usaba fecha_termino como fecha_fin, pero ese campo
        // solo indica que el show cruza medianoche (no que dure varios días).
        DB::table('panoramas')
            ->where('fuente', 'passline')
            ->update(['fecha_fin' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Limpieza de datos: no reversible al valor original.
    }
};
