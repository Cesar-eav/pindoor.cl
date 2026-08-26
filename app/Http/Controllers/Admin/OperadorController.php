<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperadorTuristico;
use App\Models\User;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OperadorController extends Controller
{
    public function index()
    {
        $operadores = OperadorTuristico::with('usuario')->latest()->paginate(25);
        return view('admin.operadores.index', compact('operadores'));
    }

    public function create()
    {
        $operador = null;
        return view('admin.operadores.create', compact('operador'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $slug = OperadorTuristico::slugUnico($data['nombre']);

        $user = User::create([
            'name'              => $data['nombre'],
            'email'             => $slug . '@operadores.pindoor.cl',
            'password'          => Hash::make(Str::random(40)),
            'type'              => 'operador',
            'email_verified_at' => now(),
        ]);

        $data['user_id'] = $user->id;
        $data['slug']    = $slug;

        if ($request->hasFile('imagen')) {
            $data['imagen_perfil'] = ImagenComprimida::guardar($request->file('imagen'), 'operadores');
        }

        $operador = OperadorTuristico::create($data);

        return redirect()->route('admin.operadores.edit', $operador)->with('success', 'Operador creado correctamente.');
    }

    public function edit(OperadorTuristico $operador)
    {
        return view('admin.operadores.edit', compact('operador'));
    }

    public function update(Request $request, OperadorTuristico $operador)
    {
        $data = $this->validar($request);

        if ($request->hasFile('imagen')) {
            if ($operador->imagen_perfil) {
                Storage::disk('public')->delete($operador->imagen_perfil);
            }
            $data['imagen_perfil'] = ImagenComprimida::guardar($request->file('imagen'), 'operadores');
        }

        $operador->update($data);

        return redirect()->route('admin.operadores.edit', $operador)->with('success', 'Operador actualizado correctamente.');
    }

    public function toggle(OperadorTuristico $operador)
    {
        $operador->update(['activo' => !$operador->activo]);
        return back()->with('success', 'Estado del operador actualizado.');
    }

    public function destroy(OperadorTuristico $operador)
    {
        if ($operador->rutas()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: el operador tiene rutas asignadas.');
        }

        if ($operador->imagen_perfil) {
            Storage::disk('public')->delete($operador->imagen_perfil);
        }

        $operador->delete();

        return redirect()->route('admin.operadores.index')->with('success', 'Operador eliminado.');
    }

    private function validar(Request $request): array
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string|max:2000',
            'ciudad'           => 'nullable|string|max:100',
            'email_contacto'   => 'nullable|email|max:255',
            'telefono'         => 'nullable|string|max:30',
            'enlace_web'       => 'nullable|url|max:500',
            'enlace_instagram' => 'nullable|url|max:500',
            'enlace_facebook'  => 'nullable|url|max:500',
            'enlace_whatsapp'  => 'nullable|url|max:500',
            'imagen'           => 'nullable|image|max:4096',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        return $data;
    }
}
