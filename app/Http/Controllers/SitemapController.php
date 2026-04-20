<?php

namespace App\Http\Controllers;

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

        return response()
            ->view('sitemap', compact('puntos'))
            ->header('Content-Type', 'application/xml');
    }
}
