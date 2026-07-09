<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->json('modulos_defecto')->nullable()->after('tipo');
        });

        // Migrar los valores hardcodeados actuales a BD
        $defaults = [
            2  => ['oferta_del_dia', 'menu_del_dia', 'carta'],
            8  => ['oferta_del_dia', 'menu_del_dia', 'carta'],
            10 => ['oferta_del_dia', 'menu_del_dia', 'carta'],
            11 => ['oferta_del_dia', 'habitaciones', 'servicios', 'politicas'],
            7  => ['oferta_del_dia', 'entradas', 'exposiciones'],
            5  => ['oferta_del_dia', 'agenda'],
        ];

        foreach ($defaults as $id => $modulos) {
            DB::table('categorias')->where('id', $id)->update([
                'modulos_defecto' => json_encode($modulos),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('modulos_defecto');
        });
    }
};
