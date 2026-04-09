<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Carta del negocio (solo clientes de alimentación)
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->text('carta')->nullable()->after('descripcion_busqueda');
            $table->string('carta_pdf')->nullable()->after('carta');
        });

        // Tipo de categoría: alimentacion | alojamiento | null (atractivo/general)
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('tipo')->nullable()->after('slug');
        });

        // Seeder inline: marcar categorías existentes
        DB::table('categorias')->whereIn('slug', ['cafeterias', 'picadas', 'comer'])
            ->update(['tipo' => 'alimentacion']);

        DB::table('categorias')->where('slug', 'alojar')
            ->update(['tipo' => 'alojamiento']);
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn(['carta', 'carta_pdf']);
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
