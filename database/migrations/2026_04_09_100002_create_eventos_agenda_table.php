<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_agenda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_interes_id')->constrained('puntosinteres')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('tipo')->default('otro');          // teatro, cine, concierto, exposicion, taller, otro
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->time('hora_fin')->nullable();
            $table->decimal('precio', 10, 2)->nullable();     // null = precio a consultar
            $table->string('precio_descripcion')->nullable(); // "Desde $5.000", "Entrada liberada"
            $table->string('imagen')->nullable();             // ruta en storage
            $table->boolean('activo')->default(true);
            $table->boolean('destacado')->default(false);
            $table->string('url_entradas')->nullable();       // link externo de compra
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_agenda');
    }
};
