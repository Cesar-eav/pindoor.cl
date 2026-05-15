<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Panorama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PanoramaController extends Controller
{
    public function index()
    {
        $panoramas = Panorama::orderBy('orden')->orderBy('id')->get();
        return view('admin.panoramas.index', compact('panoramas'));
    }

    public function create()
    {
        return view('admin.panoramas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'ubicacion'   => 'nullable|string|max:255',
            'fecha'       => 'required|date',
            'fecha_fin'   => 'nullable|date|after_or_equal:fecha',
            'hora'        => 'nullable|string|max:20',
            'categoria'   => 'nullable|string|in:' . implode(',', array_keys(\App\Models\Panorama::CATEGORIAS)),
            'orden'       => 'nullable|integer|min:0',
            'activo'      => 'nullable|boolean',
            'es_gratuito' => 'nullable|boolean',
            'imagen'      => 'nullable|image|max:4096',
        ]);

        $data['activo']      = $request->boolean('activo', true);
        $data['es_gratuito'] = $request->boolean('es_gratuito', false);
        $data['orden']       = $request->input('orden', 0);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('panoramas', 'public');
        }

        Panorama::create($data);

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama creado correctamente.');
    }

    public function edit(Panorama $panorama)
    {
        return view('admin.panoramas.edit', compact('panorama'));
    }

    public function update(Request $request, Panorama $panorama)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'ubicacion'   => 'nullable|string|max:255',
            'fecha'       => 'required|date',
            'fecha_fin'   => 'nullable|date|after_or_equal:fecha',
            'hora'        => 'nullable|string|max:20',
            'categoria'   => 'nullable|string|in:' . implode(',', array_keys(\App\Models\Panorama::CATEGORIAS)),
            'orden'       => 'nullable|integer|min:0',
            'activo'      => 'nullable|boolean',
            'es_gratuito' => 'nullable|boolean',
            'imagen'      => 'nullable|image|max:4096',
        ]);

        $data['activo']      = $request->boolean('activo', true);
        $data['es_gratuito'] = $request->boolean('es_gratuito', false);
        $data['orden']       = $request->input('orden', 0);

        if ($request->hasFile('imagen')) {
            if ($panorama->imagen) {
                Storage::disk('public')->delete($panorama->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('panoramas', 'public');
        }

        $panorama->update($data);

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama actualizado correctamente.');
    }

    public function destroy(Panorama $panorama)
    {
        if ($panorama->imagen) {
            Storage::disk('public')->delete($panorama->imagen);
        }

        $panorama->delete();

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama eliminado.');
    }

    public function toggle(Panorama $panorama)
    {
        $panorama->update(['activo' => ! $panorama->activo]);
        return back()->with('success', 'Visibilidad actualizada.');
    }
}
