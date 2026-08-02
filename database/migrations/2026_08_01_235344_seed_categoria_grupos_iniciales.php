<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Grupos iniciales y las categorías (por slug) que agrupan.
     * Definido junto al usuario: Picadas va en "Comida y tragos", no en "Cultura".
     */
    private function mapa(): array
    {
        return [
            [
                'nombre' => 'Atractivos turísticos', 'slug' => 'atractivos-turisticos',
                'icono' => 'mountain-sun', 'orden' => 1,
                'categorias' => [
                    'miradores', 'street-art', 'monumentos', 'naturaleza', 'arquitectura',
                    'estatuas', 'patrimonio_inmaterial', 'plazas', 'iglesias', 'ascensores',
                ],
            ],
            [
                'nombre' => 'Comida y tragos', 'slug' => 'comida-y-tragos',
                'icono' => 'utensils', 'orden' => 2,
                'categorias' => ['cafeterias', 'picadas', 'comer', 'bar'],
            ],
            [
                'nombre' => 'Cultura', 'slug' => 'cultura',
                'icono' => 'landmark', 'orden' => 3,
                'categorias' => ['cultura', 'museos', 'agrupacion-cultural'],
            ],
            [
                'nombre' => 'Otros / Servicios', 'slug' => 'otros-servicios',
                'icono' => 'ellipsis', 'orden' => 4,
                'categorias' => ['alojar', 'tiendas', 'artesania', 'centro-deportivo', 'centro-cumpleaneros'],
            ],
        ];
    }

    public function up(): void
    {
        foreach ($this->mapa() as $grupo) {
            $grupoId = DB::table('categoria_grupos')->insertGetId([
                'nombre'     => $grupo['nombre'],
                'slug'       => $grupo['slug'],
                'icono'      => $grupo['icono'],
                'orden'      => $grupo['orden'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('categorias')
                ->whereIn('slug', $grupo['categorias'])
                ->update(['grupo_id' => $grupoId]);
        }
    }

    public function down(): void
    {
        DB::table('categorias')->update(['grupo_id' => null]);

        foreach ($this->mapa() as $grupo) {
            DB::table('categoria_grupos')->where('slug', $grupo['slug'])->delete();
        }
    }
};
