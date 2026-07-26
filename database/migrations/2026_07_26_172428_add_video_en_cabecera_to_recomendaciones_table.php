<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->boolean('video_en_cabecera')->default(false)->after('video_youtube');
        });
    }

    public function down(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->dropColumn('video_en_cabecera');
        });
    }
};
