<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruta_operador_turistico', function (Blueprint $table) {
            $table->boolean('ticketing_activo')->default(false)->after('operador_turistico_id');
            $table->unsignedInteger('precio_individual')->nullable()->after('ticketing_activo');
            $table->unsignedInteger('precio_grupo_adulto')->nullable()->after('precio_individual');
            $table->unsignedInteger('precio_nino')->nullable()->after('precio_grupo_adulto');
            $table->unsignedTinyInteger('edad_maxima_nino')->default(14)->after('precio_nino');
            $table->text('notas_operador')->nullable()->after('edad_maxima_nino');
        });
    }

    public function down(): void
    {
        Schema::table('ruta_operador_turistico', function (Blueprint $table) {
            $table->dropColumn([
                'ticketing_activo',
                'precio_individual',
                'precio_grupo_adulto',
                'precio_nino',
                'edad_maxima_nino',
                'notas_operador',
            ]);
        });
    }
};
