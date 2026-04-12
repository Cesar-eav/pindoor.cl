<?php

namespace App\Http\Controllers;

use App\Models\LeadPublicita;
use Illuminate\Http\Request;

class PublicitaController extends Controller
{
    public function index()
    {
        return view('publicita.index');
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
