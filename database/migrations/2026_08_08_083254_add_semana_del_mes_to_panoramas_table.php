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
            // 1-4 = primera..cuarta semana del mes; -1 = última. Null = repite todas las semanas (comportamiento actual).
            $table->tinyInteger('semana_del_mes')->nullable()->after('dias_semana');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->dropColumn('semana_del_mes');
        });
    }
};
