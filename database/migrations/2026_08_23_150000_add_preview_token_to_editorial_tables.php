<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    // Token de vista previa compartible con el cliente — reemplaza el hack de
    // exponer la ruta de preview admin sin protección. El link muere solo
    // cuando el registro pasa a estar en vivo (ver HasPreviewToken).
    public function up(): void
    {
        foreach (['revivals', 'posts', 'recomendaciones'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->string('preview_token', 40)->nullable()->unique()->after('slug');
            });
        }

        foreach (['revivals', 'posts', 'recomendaciones'] as $tabla) {
            foreach (DB::table($tabla)->select('id')->get() as $fila) {
                DB::table($tabla)->where('id', $fila->id)->update(['preview_token' => Str::random(40)]);
            }
        }
    }

    public function down(): void
    {
        foreach (['revivals', 'posts', 'recomendaciones'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) {
                $table->dropColumn('preview_token');
            });
        }
    }
};
