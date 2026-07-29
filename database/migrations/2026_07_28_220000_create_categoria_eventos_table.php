<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria_eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('slug', 50)->unique();
            $table->string('emoji', 20)->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        $categorias = [
            ['slug' => 'arte',        'nombre' => 'Arte',        'emoji' => '🎨'],
            ['slug' => 'musica',      'nombre' => 'Música',      'emoji' => '🎵'],
            ['slug' => 'cine',        'nombre' => 'Cine',        'emoji' => '🎬'],
            ['slug' => 'conferencia', 'nombre' => 'Conferencia', 'emoji' => '🎓'],
            ['slug' => 'danza',       'nombre' => 'Danza',       'emoji' => '💃'],
            ['slug' => 'exposicion',  'nombre' => 'Exposición',  'emoji' => '🧐'],
            ['slug' => 'feria',       'nombre' => 'Feria',       'emoji' => '🛍️'],
            ['slug' => 'gastronomia', 'nombre' => 'Gastronomía', 'emoji' => '🍽️'],
            ['slug' => 'infantil',    'nombre' => 'Infantil',    'emoji' => '🧸'],
            ['slug' => 'literatura',  'nombre' => 'Literatura',  'emoji' => '📚'],
            ['slug' => 'standup',     'nombre' => 'Stand Up',    'emoji' => '🎤'],
            ['slug' => 'teatro',      'nombre' => 'Teatro',      'emoji' => '🎭'],
            ['slug' => 'tour',        'nombre' => 'Tour',        'emoji' => '🗺️'],
            ['slug' => 'taller',      'nombre' => 'Taller',      'emoji' => '🛠️'],
            ['slug' => 'otros',       'nombre' => 'Otros',       'emoji' => '🫘'],
        ];

        foreach ($categorias as $i => $cat) {
            DB::table('categoria_eventos')->insert([
                'nombre'     => $cat['nombre'],
                'slug'       => $cat['slug'],
                'emoji'      => $cat['emoji'],
                'orden'      => ($i + 1) * 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria_eventos');
    }
};
