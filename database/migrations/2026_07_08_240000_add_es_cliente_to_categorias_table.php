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
            $table->boolean('es_cliente')->default(false)->after('modulos_defecto');
        });

        // Categorías disponibles para negocios (clientes)
        DB::table('categorias')->whereIn('id', [2, 5, 7, 8, 10, 11, 17, 18, 19, 21])
            ->update(['es_cliente' => true]);

        // Corregir modulos_defecto corruptos (guardados con índice numérico en vez de key)
        DB::table('categorias')->where('id', 2)
            ->update(['modulos_defecto' => json_encode(['oferta_del_dia', 'menu_del_dia', 'carta'])]);
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('es_cliente');
        });
    }
};
