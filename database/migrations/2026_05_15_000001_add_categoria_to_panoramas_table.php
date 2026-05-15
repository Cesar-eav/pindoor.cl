<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->string('categoria', 50)->nullable()->after('hora');
            $table->boolean('es_gratuito')->default(false)->after('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->dropColumn(['categoria', 'es_gratuito']);
        });
    }
};
