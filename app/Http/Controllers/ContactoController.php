<?php

namespace App\Http\Controllers;

use App\Mail\NuevoContacto;
use App\Models\LeadContacto;
use App\Models\PuntoInteres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function index()
    {
        $atractivos = PuntoInteres::query()
            ->where('activo', 1)
            //->whereIn('id', [64, 80, 81, 87,74])
            ->whereIn('id', [64, 80, 81, 87,115])
            ->where('eliminado', false)
            ->get();

        return view('contacto.index', compact('atractivos'));
    }

    public function store(Request $request)
    {
        $tipo = $request->input('tipo');

        $rules = [
            'tipo'     => 'required|in:cliente,artista',
            'nombre'   => 'required|string|max:120',
            'email'    => 'required|email|max:160',
            'telefono' => 'nullable|string|max:25',
            'ciudad'   => 'nullable|string|max:100',
            'mensaje'  => 'nullable|string|max:1200',
        ];

        if ($tipo === 'cliente') {
            $rules['tipo_local']   = 'required|string|max:60';
            $rules['nombre_local'] = 'required|string|max:150';
        } else {
            $rules['especialidad'] = 'required|string|max:100';
        }

        $request->validate($rules);

        $lead = LeadContacto::create([
            'tipo'           => $tipo,
            'nombre'         => $request->nombre,
            'email'          => $request->email,
            'telefono'       => $request->telefono,
            'tipo_negocio'   => $request->tipo_local,   // campo del form → columna BD
            'nombre_negocio' => $request->nombre_local,
            'especialidad'   => $request->especialidad,
            'ciudad'         => $request->ciudad,
            'mensaje'        => $request->mensaje,
        ]);

        Mail::to(['soporte@pindoor.cl', 'cesar.eav@gmail.com'])->send(new NuevoContacto($lead));

        return back()->with('success', '¡Mensaje enviado! Te contactaremos pronto.');
    }
}
