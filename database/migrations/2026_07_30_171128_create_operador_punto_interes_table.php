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
        Schema::create('operador_punto_interes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operador_turistico_id')->constrained('operadores_turisticos')->cascadeOnDelete();
            $table->foreignId('punto_interes_id')->constrained('puntosinteres')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operador_punto_interes');
    }
};
