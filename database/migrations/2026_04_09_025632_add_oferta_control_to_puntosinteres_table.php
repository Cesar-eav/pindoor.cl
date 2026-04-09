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
            $table->boolean('oferta_activa')->default(false)->after('oferta_del_dia');
            $table->timestamp('oferta_expira_at')->nullable()->after('oferta_activa');
        });
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn(['oferta_activa', 'oferta_expira_at']);
        });
    }
};
