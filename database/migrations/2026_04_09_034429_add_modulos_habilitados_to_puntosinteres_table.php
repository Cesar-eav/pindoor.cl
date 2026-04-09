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
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->json('modulos_habilitados')->nullable()->after('es_cliente');
        });
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn('modulos_habilitados');
        });
    }
};
