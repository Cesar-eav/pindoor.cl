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
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->date('vigente_hasta')->nullable()->after('plan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recomendaciones', function (Blueprint $table) {
            $table->dropColumn('vigente_hasta');
        });
    }
};
