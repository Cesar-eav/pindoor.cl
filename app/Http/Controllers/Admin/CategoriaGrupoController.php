<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\CategoriaGrupo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaGrupoController extends Controller
{
    public function index()
    {
        $grupos = CategoriaGrupo::withCount('categorias')->orderBy('orden')->get();
        return view('admin.categoria-grupos.index', compact('grupos'));
    }

    public function create()
    {
        $grupo = null;
        $categorias = Categoria::with('grupo')->orderBy('nombre')->get();
        $seleccionadas = [];
        return view('admin.categoria-grupos.form', compact('grupo', 'categorias', 'seleccionadas'));
    }

    public function store(Request $request)
    {
        $data = $this->validarDatos($request);

        $grupo = CategoriaGrupo::create([
            'nombre' => $data['nombre'],
            'slug'   => Str::slug($data['nombre']),
            'icono'  => $data['icono'] ?? null,
            'orden'  => $data['orden'] ?? 0,
        ]);

        Categoria::whereIn('id', $data['categorias'] ?? [])->update(['grupo_id' => $grupo->id]);

        return redirect()->route('admin.categoria-grupos.index')->with('success', "Grupo «{$grupo->nombre}» creado.");
    }

    public function edit(CategoriaGrupo $categoriaGrupo)
    {
        $grupo = $categoriaGrupo;
        $categorias = Categoria::with('grupo')->orderBy('nombre')->get();
        $seleccionadas = $grupo->categorias()->pluck('id')->all();
        return view('admin.categoria-grupos.form', compact('grupo', 'categorias', 'seleccionadas'));
    }

    public function update(Request $request, CategoriaGrupo $categoriaGrupo)
    {
        $data = $this->validarDatos($request, $categoriaGrupo->id);

        $categoriaGrupo->update([
            'nombre' => $data['nombre'],
            'slug'   => Str::slug($data['nombre']),
            'icono'  => $data['icono'] ?? null,
            'orden'  => $data['orden'] ?? 0,
        ]);

        $seleccionadas = $data['categorias'] ?? [];

        // Saca del grupo las que ya no estén marcadas, asigna las que sí
        Categoria::where('grupo_id', $categoriaGrupo->id)
            ->whereNotIn('id', $seleccionadas)
            ->update(['grupo_id' => null]);

        Categoria::whereIn('id', $seleccionadas)->update(['grupo_id' => $categoriaGrupo->id]);

        return redirect()->route('admin.categoria-grupos.index')->with('success', "Grupo «{$categoriaGrupo->nombre}» actualizado.");
    }

    public function destroy(CategoriaGrupo $categoriaGrupo)
    {
        Categoria::where('grupo_id', $categoriaGrupo->id)->update(['grupo_id' => null]);
        $categoriaGrupo->delete();

        return redirect()->route('admin.categoria-grupos.index')->with('success', 'Grupo eliminado. Sus categorías quedaron sin grupo.');
    }

    private function validarDatos(Request $request, ?int $ignorarId = null): array
    {
        return $request->validate([
            'nombre'          => 'required|string|max:100|unique:categoria_grupos,nombre,' . $ignorarId,
            'icono'           => 'nullable|string|max:50',
            'orden'           => 'nullable|integer|min:0',
            'categorias'      => 'nullable|array',
            'categorias.*'    => 'exists:categorias,id',
        ]);
    }
}
