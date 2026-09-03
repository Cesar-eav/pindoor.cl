<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento_entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_modulo_item_id')->constrained('punto_modulo_items')->restrictOnDelete();
            $table->foreignId('punto_interes_id')->constrained('puntosinteres')->restrictOnDelete();

            $table->string('evento_titulo_al_pagar');
            $table->date('evento_fecha_al_pagar');
            $table->unsignedInteger('precio_unitario_al_pagar');

            $table->unsignedTinyInteger('cantidad_entradas');
            $table->unsignedInteger('monto_total');

            $table->string('nombre_cliente');
            $table->string('email_cliente');
            $table->string('telefono_cliente', 30);

            $table->string('codigo_entrada', 20)->unique();
            $table->string('commerce_order', 60)->unique();
            $table->string('flow_token', 100)->nullable()->index();
            $table->string('flow_order', 60)->nullable();

            $table->enum('estado', ['pendiente', 'pagada', 'rechazada', 'anulada', 'expirada'])->default('pendiente');
            $table->timestamp('pagado_en')->nullable();
            $table->timestamp('expira_en');
            $table->json('payload_flow')->nullable();
            $table->string('payer_email')->nullable();
            $table->string('medio_pago', 50)->nullable();
            $table->unsignedInteger('monto_pagado')->nullable();
            $table->timestamp('fecha_pago_flow')->nullable();
            $table->string('ip_cliente', 45)->nullable();
            $table->boolean('es_prueba')->default(false);

            $table->timestamps();

            $table->index(['punto_modulo_item_id', 'estado']);
            $table->index(['punto_interes_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_entradas');
    }
};
