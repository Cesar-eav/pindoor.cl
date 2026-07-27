<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\PuntoInteres;

class SitemapController extends Controller
{
    public function index()
    {
        $puntos = PuntoInteres::where('activo', true)
            ->where('eliminado', false)
            ->whereNotNull('slug')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        $categorias = Categoria::withCount(['puntosInteres' => fn ($q) => $q->where('activo', 1)->where('eliminado', false)])
            ->having('puntos_interes_count', '>', 0)
            ->get(['slug']);

        return response()
            ->view('sitemap', compact('puntos', 'categorias'))
            ->header('Content-Type', 'application/xml');
    }
}
