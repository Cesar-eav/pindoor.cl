<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE ticketera_reservas MODIFY estado ENUM('pendiente', 'pagada', 'rechazada', 'anulada', 'expirada', 'reembolsada') NOT NULL DEFAULT 'pendiente'");

        Schema::table('ticketera_reservas', function (Blueprint $table) {
            $table->string('payer_email')->nullable()->after('payload_flow');
            $table->string('medio_pago', 50)->nullable()->after('payer_email');
            $table->unsignedInteger('monto_pagado')->nullable()->after('medio_pago');
            $table->timestamp('fecha_pago_flow')->nullable()->after('monto_pagado');
        });
    }

    public function down(): void
    {
        Schema::table('ticketera_reservas', function (Blueprint $table) {
            $table->dropColumn(['payer_email', 'medio_pago', 'monto_pagado', 'fecha_pago_flow']);
        });

        DB::statement("ALTER TABLE ticketera_reservas MODIFY estado ENUM('pendiente', 'pagada', 'rechazada', 'anulada', 'expirada') NOT NULL DEFAULT 'pendiente'");
    }
};
