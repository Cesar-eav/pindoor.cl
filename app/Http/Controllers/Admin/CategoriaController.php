<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'nombre'                   => 'required|string|max:100|unique:categorias,nombre',
            'tipo'                     => 'nullable|string|max:50',
            'icono'                    => 'nullable|string|max:50',
            'imagen_portada'           => 'nullable|image|max:4096',
            'mostrar_nombre_en_imagen' => 'nullable|boolean',
            'descripcion'              => 'nullable|string|max:500',
            'modulos_defecto'          => 'nullable|array',
            'modulos_defecto.*'        => 'string',
            'es_cliente'               => 'nullable|boolean',
            'disponible_para_operadores' => 'nullable|boolean',
        ]);

        $data['slug']            = Str::slug($data['nombre']);
        $data['modulos_defecto'] = $request->input('modulos_defecto', []);
        $data['es_cliente']      = $request->boolean('es_cliente');
        $data['mostrar_nombre_en_imagen'] = $request->boolean('mostrar_nombre_en_imagen', true);
        $data['disponible_para_operadores'] = $request->boolean('disponible_para_operadores');

        if ($request->hasFile('imagen_portada')) {
            $data['imagen_portada'] = ImagenComprimida::guardar($request->file('imagen_portada'), 'categorias');
        }

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
            'nombre'                   => 'required|string|max:100|unique:categorias,nombre,' . $categoria->id,
            'tipo'                     => 'nullable|string|max:50',
            'icono'                    => 'nullable|string|max:50',
            'imagen_portada'           => 'nullable|image|max:4096',
            'mostrar_nombre_en_imagen' => 'nullable|boolean',
            'descripcion'              => 'nullable|string|max:500',
            'modulos_defecto'          => 'nullable|array',
            'modulos_defecto.*'        => 'string',
            'es_cliente'               => 'nullable|boolean',
            'disponible_para_operadores' => 'nullable|boolean',
        ]);

        $data['slug']            = Str::slug($data['nombre']);
        $data['modulos_defecto'] = $request->input('modulos_defecto', []);
        $data['es_cliente']      = $request->boolean('es_cliente');
        $data['mostrar_nombre_en_imagen'] = $request->boolean('mostrar_nombre_en_imagen', true);
        $data['disponible_para_operadores'] = $request->boolean('disponible_para_operadores');

        if ($request->hasFile('imagen_portada')) {
            if ($categoria->imagen_portada) {
                Storage::disk('public')->delete($categoria->imagen_portada);
            }
            $data['imagen_portada'] = ImagenComprimida::guardar($request->file('imagen_portada'), 'categorias');
        }

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

        if ($categoria->imagen_portada) {
            Storage::disk('public')->delete($categoria->imagen_portada);
        }

        $categoria->delete();

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría eliminada.');
    }
}
