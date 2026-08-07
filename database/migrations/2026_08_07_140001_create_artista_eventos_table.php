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
        Schema::create('artista_eventos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artista_id')->constrained('artistas')->onDelete('cascade');
            $table->string('titulo');
            $table->string('tipo', 50)->default('otros');
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->string('hora', 5)->nullable();
            $table->string('hora_fin', 5)->nullable();
            $table->decimal('precio', 10, 0)->nullable();
            $table->string('precio_texto')->nullable();
            $table->string('url_entradas')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->boolean('destacado')->default(false);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->index(['artista_id', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artista_eventos');
    }
};
