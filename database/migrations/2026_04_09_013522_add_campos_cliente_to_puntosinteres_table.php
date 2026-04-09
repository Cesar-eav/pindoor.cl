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
        Schema::table('puntosinteres', function (Blueprint $table) {
            // Distingue negocios/clientes de atractivos turísticos
            $table->boolean('es_cliente')->default(false)->after('activo');

            // El cliente actualiza esto cuando quiere (oferta, menú del día, promo)
            $table->text('oferta_del_dia')->nullable()->after('es_cliente');

            // Campo oculto para el buscador: el cliente describe lo que ofrece en detalle
            $table->text('descripcion_busqueda')->nullable()->after('oferta_del_dia');

            // Imagen de perfil/logo del negocio
            $table->string('imagen_perfil')->nullable()->after('descripcion_busqueda');
        });
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn(['es_cliente', 'oferta_del_dia', 'descripcion_busqueda', 'imagen_perfil']);
        });
    }
};
