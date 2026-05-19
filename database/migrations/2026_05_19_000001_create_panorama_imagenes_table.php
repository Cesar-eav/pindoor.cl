<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panorama_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panorama_id')->constrained('panoramas')->onDelete('cascade');
            $table->string('ruta');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panorama_imagenes');
    }
};
