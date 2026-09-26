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
        Schema::create('eventos_ficha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_interes_id')->constrained('puntosinteres')->cascadeOnDelete();
            $table->string('tipo', 20); // visita | como_llegar | whatsapp
            $table->timestamps();

            $table->index(['punto_interes_id', 'tipo', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos_ficha');
    }
};
