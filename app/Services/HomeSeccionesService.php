<?php

namespace App\Services;

use App\Models\Configuracion;
use App\Models\Post;
use App\Models\PuntoInteres;
use App\Models\Ruta;

/**
 * Centraliza el orden de las secciones del home y las queries de Destacados/Guías/Rutas,
 * que antes vivían duplicadas a mano en PuntoInteresController y AtractivosGrid.
 */
class HomeSeccionesService
{
    const ORDEN_DEFAULT = ['panoramas', 'destacados', 'recomienda', 'guias', 'rutas'];

    public static function ordenSecciones(): array
    {
        $crudo = (string) Configuracion::get('home_orden_secciones', '');

        $partes = collect(explode(',', $crudo))
            ->map(fn ($clave) => trim($clave))
            ->filter(fn ($clave) => in_array($clave, self::ORDEN_DEFAULT, true))
            ->unique()
            ->values()
            ->all();

        // Si falta alguna clave (config vacía, corrupta, o un valor viejo con menos de 5
        // claves), se completa al final con las que falten en el orden por defecto — nunca
        // debe desaparecer una sección del home por un valor inválido.
        $faltantes = array_values(array_diff(self::ORDEN_DEFAULT, $partes));

        return array_merge($partes, $faltantes);
    }

    public static function destacados()
    {
        return PuntoInteres::publico()->destacadosHome()
            ->with(['categoria', 'imagenPrincipal'])
            ->take(10)
            ->get();
    }

    public static function guias()
    {
        return Post::ordenHome()->take(10)->get();
    }

    public static function rutas()
    {
        return Ruta::ordenHome()->with('puntos')->take(10)->get();
    }
}
