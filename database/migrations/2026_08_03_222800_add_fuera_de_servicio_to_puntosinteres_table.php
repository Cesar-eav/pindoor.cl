<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->boolean('fuera_de_servicio')->default(false)->after('activo');
            $table->text('fuera_de_servicio_motivo')->nullable()->after('fuera_de_servicio');
        });
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn(['fuera_de_servicio', 'fuera_de_servicio_motivo']);
        });
    }
};
