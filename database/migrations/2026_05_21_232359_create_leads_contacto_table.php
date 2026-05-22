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
        Schema::create('leads_contacto', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['cliente', 'artista']);
            $table->string('nombre', 120);
            $table->string('email', 160);
            $table->string('telefono', 25)->nullable();
            // Clientes
            $table->string('tipo_negocio', 60)->nullable();
            $table->string('nombre_negocio', 150)->nullable();
            // Artistas
            $table->string('especialidad', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->text('mensaje')->nullable();
            $table->boolean('contactado')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads_contacto');
    }
};
