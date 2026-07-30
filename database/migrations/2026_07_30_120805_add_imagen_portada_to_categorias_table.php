<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('imagen_portada')->nullable()->after('icono');
            $table->boolean('mostrar_nombre_en_imagen')->default(true)->after('imagen_portada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn(['imagen_portada', 'mostrar_nombre_en_imagen']);
        });
    }
};
