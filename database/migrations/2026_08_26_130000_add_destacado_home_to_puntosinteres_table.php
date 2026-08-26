<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->boolean('destacado_home')->default(false)->after('es_cliente');
            $table->unsignedInteger('orden_destacado')->default(0)->after('destacado_home');
        });

        // Backfill: los clientes activos actuales quedan destacados de entrada, con el
        // mismo orden que ya se ve hoy en portada (latest updated_at) — así el home no
        // cambia visualmente al desplegar, hasta que el admin cure manualmente.
        DB::table('puntosinteres')
            ->where('es_cliente', true)
            ->where('eliminado', false)
            ->orderByDesc('updated_at')
            ->pluck('id')
            ->each(function ($id, $i) {
                DB::table('puntosinteres')->where('id', $id)->update([
                    'destacado_home' => true,
                    'orden_destacado' => $i,
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('puntosinteres', function (Blueprint $table) {
            $table->dropColumn(['destacado_home', 'orden_destacado']);
        });
    }
};
