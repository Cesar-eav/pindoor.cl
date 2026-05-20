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
        Schema::create('artistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('disciplina');
            $table->text('descripcion')->nullable();
            $table->string('imagen_perfil')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('email_contacto')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('enlace_web')->nullable();
            $table->string('enlace_instagram')->nullable();
            $table->string('enlace_facebook')->nullable();
            $table->string('enlace_spotify')->nullable();
            $table->string('enlace_youtube')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artistas');
    }
};
