<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->string('plan', 20); // basica | intermedia | premium
            $table->string('negocio');
            $table->string('rubro')->nullable();
            $table->string('direccion')->nullable();
            $table->text('contenido')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('enlace', 500)->nullable();
            $table->string('autor')->nullable();
            $table->string('imagen_portada')->nullable();
            $table->boolean('destacado_portada')->default(false);
            $table->date('destacado_hasta')->nullable();
            $table->boolean('publicado')->default(false);
            $table->timestamp('publicado_en')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recomendaciones');
    }
};
