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
        Schema::table('panoramas', function (Blueprint $table) {
            $table->string('fuente')->nullable()->after('enlace');
            $table->string('fuente_id')->nullable()->after('fuente');
            $table->unique(['fuente', 'fuente_id']);
        });
    }

    public function down(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->dropUnique(['fuente', 'fuente_id']);
            $table->dropColumn(['fuente', 'fuente_id']);
        });
    }
};
