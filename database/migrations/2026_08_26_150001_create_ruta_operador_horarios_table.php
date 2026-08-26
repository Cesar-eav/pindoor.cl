<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta_operador_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_operador_turistico_id')->constrained('ruta_operador_turistico')->cascadeOnDelete();
            $table->enum('tipo', ['semanal', 'fecha']);
            $table->json('dias_semana')->nullable();
            $table->date('fecha')->nullable();
            $table->time('hora');
            $table->unsignedInteger('cupo_maximo');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['ruta_operador_turistico_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_operador_horarios');
    }
};
