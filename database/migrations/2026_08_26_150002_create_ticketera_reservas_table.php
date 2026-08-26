<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticketera_reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_operador_turistico_id')->constrained('ruta_operador_turistico')->restrictOnDelete();
            $table->foreignId('ruta_operador_horario_id')->constrained('ruta_operador_horarios')->restrictOnDelete();
            $table->date('fecha_visita');
            $table->string('nombre_cliente');
            $table->string('email_cliente');
            $table->string('telefono_cliente', 30);
            $table->unsignedTinyInteger('cantidad_adultos');
            $table->unsignedTinyInteger('cantidad_ninos')->default(0);
            $table->unsignedInteger('precio_unitario_adulto');
            $table->unsignedInteger('precio_unitario_nino');
            $table->unsignedInteger('precio_total');
            $table->string('codigo_reserva', 20)->unique();
            $table->string('commerce_order', 60)->unique();
            $table->string('flow_token', 100)->nullable()->index();
            $table->string('flow_order', 60)->nullable();
            $table->enum('estado', ['pendiente', 'pagada', 'rechazada', 'anulada', 'expirada'])->default('pendiente');
            $table->timestamp('pagado_en')->nullable();
            $table->timestamp('expira_en');
            $table->json('payload_flow')->nullable();
            $table->string('ip_cliente', 45)->nullable();
            $table->timestamps();

            $table->index(['ruta_operador_horario_id', 'fecha_visita', 'estado'], 'ticketera_reservas_horario_fecha_estado_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticketera_reservas');
    }
};
