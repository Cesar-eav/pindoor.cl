<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $aprobacionActiva = (bool) Configuracion::get('aprobacion_negocios_activa', false);

        return view('admin.configuracion.index', compact('aprobacionActiva'));
    }

    public function actualizar(Request $request)
    {
        Configuracion::set('aprobacion_negocios_activa', $request->boolean('aprobacion_negocios_activa') ? '1' : '0');

        return back()->with('success', 'Configuración guardada.');
    }
}
