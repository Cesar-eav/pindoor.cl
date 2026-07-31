<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\PuntoInteres;

class SitemapController extends Controller
{
    public function index()
    {
        $puntos = PuntoInteres::publico()
            ->whereNotNull('slug')
            ->orderBy('updated_at', 'desc')
            ->get(['slug', 'updated_at']);

        $categorias = Categoria::withCount(['puntosInteres' => fn ($q) => $q->publico()])
            ->having('puntos_interes_count', '>', 0)
            ->get(['slug']);

        return response()
            ->view('sitemap', compact('puntos', 'categorias'))
            ->header('Content-Type', 'application/xml');
    }
}
