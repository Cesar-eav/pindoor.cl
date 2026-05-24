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
        Schema::create('punto_productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('punto_interes_id');
            $table->foreign('punto_interes_id')->references('id')->on('puntosinteres')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->string('precio', 40)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punto_productos');
    }
};
