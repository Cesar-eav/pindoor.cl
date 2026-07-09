<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('puntosInteres')->orderBy('nombre')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        $categoria = null;
        return view('admin.categorias.create', compact('categoria'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:100|unique:categorias,nombre',
            'tipo'            => 'nullable|string|max:50',
            'icono'           => 'nullable|string|max:50',
            'descripcion'     => 'nullable|string|max:500',
            'modulos_defecto' => 'nullable|array',
            'modulos_defecto.*'=> 'string',
            'es_cliente'      => 'nullable|boolean',
        ]);

        $data['slug']            = Str::slug($data['nombre']);
        $data['modulos_defecto'] = $request->input('modulos_defecto', []);
        $data['es_cliente']      = $request->boolean('es_cliente');

        if (Categoria::where('slug', $data['slug'])->exists()) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una categoría con un nombre muy similar (slug duplicado).']);
        }

        try {
            Categoria::create($data);
        } catch (\Exception $e) {
            \Log::error('CategoriaController@store: ' . get_class($e) . ' — ' . $e->getMessage());
            return back()->withInput()->withErrors(['nombre' => get_class($e) . ': ' . $e->getMessage()]);
        }

        return redirect()->route('admin.categorias.create')->with('success', '«' . $data['nombre'] . '» fue creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        $categoria->loadCount('puntosInteres');
        $catalogo = \App\Models\PuntoInteres::catalogoModulos();
        return view('admin.categorias.edit', compact('categoria', 'catalogo'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:100|unique:categorias,nombre,' . $categoria->id,
            'tipo'             => 'nullable|string|max:50',
            'icono'            => 'nullable|string|max:10',
            'descripcion'      => 'nullable|string|max:500',
            'modulos_defecto'  => 'nullable|array',
            'modulos_defecto.*'=> 'string',
            'es_cliente'       => 'nullable|boolean',
        ]);

        $data['slug']            = Str::slug($data['nombre']);
        $data['modulos_defecto'] = $request->input('modulos_defecto', []);
        $data['es_cliente']      = $request->boolean('es_cliente');

        if (Categoria::where('slug', $data['slug'])->where('id', '!=', $categoria->id)->exists()) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una categoría con un nombre muy similar (slug duplicado).']);
        }

        try {
            $categoria->update($data);
        } catch (\Exception $e) {
            \Log::error('CategoriaController@update: ' . get_class($e) . ' — ' . $e->getMessage());
            return back()->withInput()->withErrors(['nombre' => get_class($e) . ': ' . $e->getMessage()]);
        }

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->puntosInteres()->count() > 0) {
            return back()->with('error', 'No se puede eliminar: tiene ' . $categoria->puntosInteres()->count() . ' puntos asignados.');
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada.');
    }
}
