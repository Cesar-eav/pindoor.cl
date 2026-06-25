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
        Schema::table('artistas', function (Blueprint $table) {
            $table->string('enlace_youtube_video')->nullable()->after('enlace_youtube');
        });
    }

    public function down(): void
    {
        Schema::table('artistas', function (Blueprint $table) {
            $table->dropColumn('enlace_youtube_video');
        });
    }
};
