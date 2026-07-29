<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->string('estado_aprobacion')->nullable()->after('activo');
            $table->string('contacto_whatsapp')->nullable()->after('estado_aprobacion');
        });
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn(['estado_aprobacion', 'contacto_whatsapp']);
        });
    }
};
