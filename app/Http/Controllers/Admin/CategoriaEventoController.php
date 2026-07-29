<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaEvento;
use App\Models\ModuloItem;
use App\Models\Panorama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CategoriaEventoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaEvento::orderBy('orden')->orderBy('nombre')->get();
        $categorias->each(fn ($c) => $c->usos = $c->contarUsos());

        return view('admin.categoria-eventos.index', compact('categorias'));
    }

    public function create()
    {
        $categoria = null;

        return view('admin.categoria-eventos.create', compact('categoria'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:categoria_eventos,nombre',
            'emoji'  => 'nullable|string|max:20',
            'orden'  => 'nullable|integer|min:0',
        ]);

        $data['slug'] = Str::slug($data['nombre']);

        if (CategoriaEvento::where('slug', $data['slug'])->exists()) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una categoría con un nombre muy similar (slug duplicado).']);
        }

        $data['orden'] = $data['orden'] ?? ((CategoriaEvento::max('orden') ?? 0) + 10);

        CategoriaEvento::create($data);

        return redirect()->route('admin.categoria-eventos.index')->with('success', '«' . $data['nombre'] . '» fue creada correctamente.');
    }

    public function edit(CategoriaEvento $categoriaEvento)
    {
        $categoriaEvento->usos = $categoriaEvento->contarUsos();

        return view('admin.categoria-eventos.edit', ['categoria' => $categoriaEvento]);
    }

    public function update(Request $request, CategoriaEvento $categoriaEvento)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100|unique:categoria_eventos,nombre,' . $categoriaEvento->id,
            'emoji'  => 'nullable|string|max:20',
            'orden'  => 'nullable|integer|min:0',
        ]);

        $nuevoSlug = Str::slug($data['nombre']);

        if (CategoriaEvento::where('slug', $nuevoSlug)->where('id', '!=', $categoriaEvento->id)->exists()) {
            return back()->withInput()->withErrors(['nombre' => 'Ya existe una categoría con un nombre muy similar (slug duplicado).']);
        }

        $slugAnterior = $categoriaEvento->slug;
        $data['slug'] = $nuevoSlug;
        $categoriaEvento->update($data);

        if ($slugAnterior !== $nuevoSlug) {
            $panoramasActualizados = Panorama::where('categoria', $slugAnterior)->update(['categoria' => $nuevoSlug]);

            $itemsActualizados = 0;
            ModuloItem::where('modulo', 'eventos')->where('datos->tipo', $slugAnterior)->get()
                ->each(function (ModuloItem $item) use ($nuevoSlug, &$itemsActualizados) {
                    $item->update(['datos' => array_merge($item->datos, ['tipo' => $nuevoSlug])]);
                    $itemsActualizados++;
                });

            Log::info("CategoriaEvento renombrada: slug '{$slugAnterior}' -> '{$nuevoSlug}' ({$panoramasActualizados} panoramas, {$itemsActualizados} eventos de cliente actualizados)");
        }

        return redirect()->route('admin.categoria-eventos.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy(CategoriaEvento $categoriaEvento)
    {
        $usos = $categoriaEvento->contarUsos();

        if ($usos > 0) {
            return back()->with('error', 'No se puede eliminar: está en uso en ' . $usos . ' evento(s)/panorama(s).');
        }

        $categoriaEvento->delete();

        return redirect()->route('admin.categoria-eventos.index')->with('success', 'Categoría eliminada.');
    }
}
