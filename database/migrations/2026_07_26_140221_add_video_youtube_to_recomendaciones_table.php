<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->string('video_youtube', 500)->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->dropColumn('video_youtube');
        });
    }
};
