<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use Illuminate\Support\Str;


class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $categorias = [
                ['nombre' => 'Miradores', 'icono' => 'camera'],
                ['nombre' => 'Cafeterías', 'icono' => 'coffee'],
                ['nombre' => 'Street Art', 'icono' => 'paint-brush'],
                ['nombre' => 'Monumentos', 'icono' => 'monument'],
                ['nombre' => 'Cultura', 'icono' => 'theater-masks'],
                ['nombre' => 'Naturaleza', 'icono' => 'leaf'],
                ['nombre' => 'Museos', 'icono' => 'landmark'],
                ['nombre' => 'Picadas', 'icono' => 'utensils'], 
                ['nombre' => 'Arquitectura', 'icono' => 'archway'],
                ['nombre' => 'Comer', 'icono' => 'hamburger'],
                ['nombre' => 'Alojar', 'icono' => 'bed'],
        ];

    foreach ($categorias as $cat) {
                Categoria::updateOrCreate(
                    ['slug' => Str::slug($cat['nombre'])], // Evita duplicados si corres el seeder de nuevo
                    [
                        'nombre' => $cat['nombre'],
                        'icono' => $cat['icono'],
                        'descripcion' => 'Explora los mejores puntos de ' . $cat['nombre'] . ' en Valparaíso.',
                    ]
                );
            }
        }
}
