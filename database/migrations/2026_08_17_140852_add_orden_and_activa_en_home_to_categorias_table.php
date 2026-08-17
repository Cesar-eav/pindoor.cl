<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->unsignedInteger('orden')->default(0)->after('grupo_id');
            $table->boolean('activa_en_home')->default(true)->after('orden');
        });

        // Backfill: preservar el orden actual (por cantidad de puntos, desc) como punto de
        // partida, para que activar esta columna no reordene visualmente la home de golpe.
        DB::table('categorias')
            ->leftJoinSub(
                DB::table('puntosinteres')->selectRaw('categoria_id, count(*) as total')->groupBy('categoria_id'),
                'conteo',
                'categorias.id', '=', 'conteo.categoria_id'
            )
            ->orderByDesc('conteo.total')
            ->pluck('categorias.id')
            ->each(function ($id, $i) {
                DB::table('categorias')->where('id', $id)->update(['orden' => $i]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn(['orden', 'activa_en_home']);
        });
    }
};
