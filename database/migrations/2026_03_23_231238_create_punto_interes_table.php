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
    Schema::create('puntosinteres', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        // Identificación
        $table->string('title');
        $table->string('slug')->unique();
        $table->string('category'); // restaurante, cafe, hostal, atractivo
        
        // Ubicación Valpiana
        $table->string('sector'); // Cerro Alegre, Concepción, Plan, etc.
        $table->string('direccion')->nullable();
        $table->decimal('lat', 10, 8)->nullable();
        $table->decimal('lng', 11, 8)->nullable();
        $table->string('ciudad')->default('Valparaíso');

        // Contenido y RRSS
        $table->longText('description');
        $table->json('tags')->nullable();
        $table->string('video_url')->nullable(); // Para YouTube
        $table->string('enlace')->nullable(); // Web o Instagram
        
        // Info Adicional
        $table->string('horario')->nullable();
        $table->string('autor')->nullable(); 

        $table->boolean('activo')->default(true);    // Para pausar un local temporalmente
        $table->boolean('eliminado')->default(false); // Nuestro "borrado lógico"
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntosinteres');
    }
};
