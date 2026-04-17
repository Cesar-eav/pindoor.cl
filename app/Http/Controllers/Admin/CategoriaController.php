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
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:categorias,nombre',
            'tipo'        => 'nullable|string|max:50',
            'icono'       => 'nullable|string|max:10',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $data['slug'] = Str::slug($data['nombre']);

        Categoria::create($data);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100|unique:categorias,nombre,' . $categoria->id,
            'tipo'        => 'nullable|string|max:50',
            'icono'       => 'nullable|string|max:10',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $data['slug'] = Str::slug($data['nombre']);

        $categoria->update($data);

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
