<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiencias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('proveedor')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('ubicacion')->nullable();
            $table->string('categoria')->nullable();
            $table->boolean('es_gratuito')->default(false);
            $table->unsignedInteger('precio')->nullable();
            $table->string('duracion', 50)->nullable();
            $table->unsignedSmallInteger('capacidad')->nullable();
            $table->string('nivel', 20)->nullable();
            $table->json('dias_semana')->nullable();
            $table->string('hora', 100)->nullable();
            $table->string('enlace', 500)->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiencias');
    }
};
