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
        Schema::create('compartidos', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);
            $table->string('canal', 20); // whatsapp | nativo | copiar
            $table->timestamps();

            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compartidos');
    }
};
