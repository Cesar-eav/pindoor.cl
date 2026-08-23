<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artista_miembros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artista_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['artista_id', 'user_id']);
        });

        DB::table('artistas')->select('id', 'user_id')->orderBy('id')->get()->each(function ($artista) {
            DB::table('artista_miembros')->insert([
                'artista_id' => $artista->id,
                'user_id'    => $artista->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artista_miembros');
    }
};
