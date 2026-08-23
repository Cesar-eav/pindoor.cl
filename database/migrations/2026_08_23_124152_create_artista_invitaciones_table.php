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
        Schema::create('artista_invitaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artista_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('token', 40)->unique();
            $table->foreignId('invitado_por_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('aceptada_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artista_invitaciones');
    }
};
