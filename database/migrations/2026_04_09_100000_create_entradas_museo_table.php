<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entradas_museo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_interes_id')->constrained('puntosinteres')->onDelete('cascade');
            $table->string('label');                          // "Adulto", "Niño menor de 12", etc.
            $table->decimal('precio', 10, 2)->default(0);    // 0 = entrada gratuita
            $table->string('descripcion')->nullable();        // "Gratis los domingos"
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas_museo');
    }
};
