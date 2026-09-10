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
        Schema::table('operadores_turisticos', function (Blueprint $table) {
            $table->boolean('es_pindoor')->default(false)->after('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operadores_turisticos', function (Blueprint $table) {
            $table->dropColumn('es_pindoor');
        });
    }
};
