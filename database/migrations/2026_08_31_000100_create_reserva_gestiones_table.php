<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reserva_gestiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticketera_reserva_id')->constrained('ticketera_reservas')->cascadeOnDelete();
            $table->enum('tipo', ['reembolso', 'reagendamiento', 'nota']);
            $table->string('estado_anterior', 20)->nullable();
            $table->string('estado_nuevo', 20)->nullable();
            $table->foreignId('horario_anterior_id')->nullable()->constrained('ruta_operador_horarios')->nullOnDelete();
            $table->date('fecha_anterior')->nullable();
            $table->foreignId('horario_nuevo_id')->nullable()->constrained('ruta_operador_horarios')->nullOnDelete();
            $table->date('fecha_nueva')->nullable();
            $table->text('motivo')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['ticketera_reserva_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reserva_gestiones');
    }
};
