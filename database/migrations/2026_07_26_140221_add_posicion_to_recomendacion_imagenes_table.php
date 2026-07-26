<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recomendacion_imagenes', function (Blueprint $table) {
            $table->unsignedSmallInteger('posicion')->nullable()->after('orden');
        });
    }

    public function down(): void
    {
        Schema::table('recomendacion_imagenes', function (Blueprint $table) {
            $table->dropColumn('posicion');
        });
    }
};
