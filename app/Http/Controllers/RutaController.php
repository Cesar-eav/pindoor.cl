<?php

namespace App\Http\Controllers;

use App\Models\Ruta;

class RutaController extends Controller
{
    public function index()
    {
        $rutas = Ruta::publicadas()->with('puntos')->get();
        return view('rutas.index', compact('rutas'));
    }

    public function show(string $slug)
    {
        $ruta = Ruta::where('slug', $slug)
                    ->where('publicado', true)
                    ->with(['puntos.categoria', 'puntos.imagenPrincipal', 'operadores'])
                    ->firstOrFail();

        return view('rutas.show', compact('ruta'));
    }
}
