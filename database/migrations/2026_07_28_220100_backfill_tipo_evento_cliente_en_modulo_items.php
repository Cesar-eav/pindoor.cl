<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $mapa = [
        'concierto' => 'musica',
        'otro'      => 'otros',
    ];

    public function up(): void
    {
        foreach ($this->mapa as $viejo => $nuevo) {
            DB::table('punto_modulo_items')
                ->where('modulo', 'eventos')
                ->where('datos->tipo', $viejo)
                ->update(['datos' => DB::raw("JSON_SET(datos, '$.tipo', '{$nuevo}')")]);
        }
    }

    public function down(): void
    {
        foreach ($this->mapa as $viejo => $nuevo) {
            DB::table('punto_modulo_items')
                ->where('modulo', 'eventos')
                ->where('datos->tipo', $nuevo)
                ->update(['datos' => DB::raw("JSON_SET(datos, '$.tipo', '{$viejo}')")]);
        }
    }
};
