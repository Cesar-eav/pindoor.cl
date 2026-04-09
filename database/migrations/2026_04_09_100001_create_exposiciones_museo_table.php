<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exposiciones_museo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_interes_id')->constrained('puntosinteres')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['permanente', 'temporal'])->default('temporal');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('imagen')->nullable();             // ruta en storage
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exposiciones_museo');
    }
};
