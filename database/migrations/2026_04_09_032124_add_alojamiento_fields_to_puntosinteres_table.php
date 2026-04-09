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
            // Precios y horarios de alojamiento
            $table->string('precio_desde')->nullable()->after('carta_updated_at');
            $table->string('check_in')->nullable()->after('precio_desde');
            $table->string('check_out')->nullable()->after('check_in');

            // Tipos de habitación (texto libre)
            $table->text('tipos_habitacion')->nullable()->after('check_out');

            // Servicios incluidos (array de slugs: wifi, desayuno, spa...)
            $table->json('servicios_incluidos')->nullable()->after('tipos_habitacion');

            // Políticas de cancelación, mascotas, check-in tardío, etc.
            $table->text('politicas')->nullable()->after('servicios_incluidos');
        });
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn([
                'precio_desde', 'check_in', 'check_out',
                'tipos_habitacion', 'servicios_incluidos', 'politicas',
            ]);
        });
    }
};
