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
            $table->text('descripcion_busqueda_admin')->nullable()->after('descripcion_busqueda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn('descripcion_busqueda_admin');
        });
    }
};
