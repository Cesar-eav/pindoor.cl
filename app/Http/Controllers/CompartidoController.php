<?php

namespace App\Http\Controllers;

use App\Models\Compartido;
use Illuminate\Http\Request;

class CompartidoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'url'   => 'required|string|max:500',
            'canal' => 'required|string|in:whatsapp,nativo,copiar',
        ]);

        // Solo registra URLs del propio sitio — evita usar el endpoint para loguear basura externa.
        if (parse_url($data['url'], PHP_URL_HOST) !== $request->getHost()) {
            abort(422);
        }

        Compartido::create($data);

        return response()->json(['ok' => true]);
    }
}
