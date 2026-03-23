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
    Schema::create('imagenes_punto', function (Blueprint $table) {
        $table->id();
        // Relación: A qué lugar pertenece esta foto
        $table->foreignId('punto_interes_id')->constrained('puntosinteres')->onDelete('cascade');
        
        $table->string('ruta'); // Aquí guardaremos el nombre del archivo (ej: 'fotos/cafepuerto.jpg')
        $table->boolean('es_principal')->default(false); // Para marcar cuál sale en el listado principal
        
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_punto');
    }
};
