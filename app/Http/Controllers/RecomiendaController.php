<?php

namespace App\Http\Controllers;

use App\Models\Recomendacion;

class RecomiendaController extends Controller
{
    public function show(string $slug)
    {
        $recomendacion = Recomendacion::where('slug', $slug)
            ->where('activo', true)
            ->with('imagenes')
            ->firstOrFail();

        return view('recomienda.show', compact('recomendacion'));
    }
}
