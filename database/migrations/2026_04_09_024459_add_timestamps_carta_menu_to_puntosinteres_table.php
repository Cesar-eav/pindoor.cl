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
            $table->timestamp('menu_del_dia_updated_at')->nullable()->after('menu_del_dia');
            $table->timestamp('carta_updated_at')->nullable()->after('carta_pdf');
        });
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn(['menu_del_dia_updated_at', 'carta_updated_at']);
        });
    }
};
