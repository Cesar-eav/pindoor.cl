<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Post;
use App\Models\PuntoInteres;
use App\Models\Recomendacion;
use App\Models\Ruta;

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

        $posts = Post::publicados()->whereNotNull('slug')->get(['slug', 'publicado_en']);

        $rutas = Ruta::publicadas()->whereNotNull('slug')->get(['slug', 'publicado_en']);

        $recomendaciones = Recomendacion::publicadas()->whereNotNull('slug')->get(['slug', 'publicado_en']);

        return response()
            ->view('sitemap', compact('puntos', 'categorias', 'posts', 'rutas', 'recomendaciones'))
            ->header('Content-Type', 'application/xml');
    }
}
