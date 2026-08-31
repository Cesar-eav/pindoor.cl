<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruta_operador_bloqueos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_operador_turistico_id')->constrained('ruta_operador_turistico')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('motivo')->nullable();
            $table->timestamps();

            $table->unique(['ruta_operador_turistico_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_operador_bloqueos');
    }
};
