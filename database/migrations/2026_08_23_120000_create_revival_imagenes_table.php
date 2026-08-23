<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Reemplaza la galería de Revival (antes un array JSON de tamaño fijo en
    // revivals.imagenes) por una tabla relacional — mismo esquema que ya usa
    // Recomienda (recomendacion_imagenes) — para poder borrar/reordenar
    // imágenes individualmente sin el límite artificial de 20 slots del form.
    public function up(): void
    {
        Schema::create('revival_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revival_id')->constrained('revivals')->cascadeOnDelete();
            $table->string('ruta');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->unsignedSmallInteger('posicion')->nullable();
            $table->timestamps();
        });

        foreach (DB::table('revivals')->select('id', 'imagenes')->get() as $revival) {
            $imagenes = json_decode($revival->imagenes ?? '[]', true) ?: [];
            foreach (array_values($imagenes) as $orden => $img) {
                $ruta = is_array($img) ? ($img['ruta'] ?? null) : $img;
                if (!$ruta) continue;
                DB::table('revival_imagenes')->insert([
                    'revival_id' => $revival->id,
                    'ruta'       => $ruta,
                    'orden'      => $orden,
                    'posicion'   => is_array($img) ? ($img['posicion'] ?? null) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('revivals', function (Blueprint $table) {
            $table->dropColumn('imagenes');
        });
    }

    public function down(): void
    {
        Schema::table('revivals', function (Blueprint $table) {
            $table->json('imagenes')->nullable();
        });

        foreach (DB::table('revival_imagenes')->orderBy('revival_id')->orderBy('orden')->get() as $img) {
            $revival = DB::table('revivals')->where('id', $img->revival_id)->first();
            $actuales = $revival && $revival->imagenes ? json_decode($revival->imagenes, true) : [];
            $actuales[] = ['ruta' => $img->ruta, 'posicion' => $img->posicion];
            DB::table('revivals')->where('id', $img->revival_id)->update(['imagenes' => json_encode($actuales)]);
        }

        Schema::dropIfExists('revival_imagenes');
    }
};
