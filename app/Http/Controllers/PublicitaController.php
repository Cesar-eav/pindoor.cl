<?php

namespace App\Http\Controllers;

use App\Models\LeadPublicita;
use App\Models\PuntoInteres;
use Illuminate\Http\Request;

class PublicitaController extends Controller
{
    public function index()
    {

        $atractivos = PuntoInteres::query()
        ->where('activo', 1)
        ->whereIn('id', PuntoInteres::idsExcluidos())
        ->where('eliminado', false)
        ->get();

        // Permite ver la ficha directa de estos puntos de ejemplo solo durante esta sesión,
        // habiendo pasado por /registro — en cualquier otro lugar público siguen excluidos.
        session(['demo_ficha_ok' => true]);

        return view('publicita.index', compact('atractivos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|max:150',
            'telefono' => 'nullable|string|max:20',
            'negocio'  => 'required|string|max:150',
            'mensaje'  => 'nullable|string|max:1000',
        ]);

        LeadPublicita::create($request->only('nombre', 'email', 'telefono', 'negocio', 'mensaje'));

        return back()->with('success', '¡Mensaje enviado! Te contactaremos pronto.');
    }
}
