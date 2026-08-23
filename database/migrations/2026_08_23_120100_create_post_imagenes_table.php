<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Mismo cambio que revival_imagenes: la galería de Blog/Guías pasa de un
    // array JSON de tamaño fijo (posts.imagenes) a una tabla relacional, igual
    // que Recomienda (recomendacion_imagenes).
    public function up(): void
    {
        Schema::create('post_imagenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->string('ruta');
            $table->unsignedSmallInteger('orden')->default(0);
            $table->unsignedSmallInteger('posicion')->nullable();
            $table->timestamps();
        });

        foreach (DB::table('posts')->select('id', 'imagenes')->get() as $post) {
            $imagenes = json_decode($post->imagenes ?? '[]', true) ?: [];
            foreach (array_values($imagenes) as $orden => $img) {
                $ruta = is_array($img) ? ($img['ruta'] ?? null) : $img;
                if (!$ruta) continue;
                DB::table('post_imagenes')->insert([
                    'post_id'    => $post->id,
                    'ruta'       => $ruta,
                    'orden'      => $orden,
                    'posicion'   => is_array($img) ? ($img['posicion'] ?? null) : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('imagenes');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('imagenes')->nullable();
        });

        foreach (DB::table('post_imagenes')->orderBy('post_id')->orderBy('orden')->get() as $img) {
            $post = DB::table('posts')->where('id', $img->post_id)->first();
            $actuales = $post && $post->imagenes ? json_decode($post->imagenes, true) : [];
            $actuales[] = ['ruta' => $img->ruta, 'posicion' => $img->posicion];
            DB::table('posts')->where('id', $img->post_id)->update(['imagenes' => json_encode($actuales)]);
        }

        Schema::dropIfExists('post_imagenes');
    }
};
