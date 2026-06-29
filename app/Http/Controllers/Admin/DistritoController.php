<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Distrito;
use Illuminate\Http\Request;

class DistritoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->has('json')) {
            return response()->json(
                Distrito::orderBy('orden')->orderBy('id')->get(['id','nombre','color','coordenadas','activo'])
            );
        }
        return view('admin.distritos.editor');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:100',
            'color'       => 'required|string|max:20',
            'coordenadas' => 'nullable|array',
            'activo'      => 'boolean',
        ]);

        $distrito = Distrito::create([
            'nombre'      => $data['nombre'],
            'color'       => $data['color'],
            'coordenadas' => $data['coordenadas'] ?? null,
            'activo'      => $data['activo'] ?? true,
            'orden'       => Distrito::max('orden') + 1,
        ]);

        return response()->json($distrito);
    }

    public function update(Request $request, Distrito $distrito)
    {
        $data = $request->validate([
            'nombre'      => 'sometimes|string|max:100',
            'color'       => 'sometimes|string|max:20',
            'coordenadas' => 'nullable|array',
            'activo'      => 'sometimes|boolean',
        ]);

        $distrito->update($data);

        return response()->json($distrito->fresh());
    }

    public function destroy(Distrito $distrito)
    {
        $distrito->delete();
        return response()->json(['ok' => true]);
    }

    public function json()
    {
        return response()->json(
            Distrito::where('activo', true)->orderBy('orden')->get(['id','nombre','color','coordenadas'])
        );
    }
}
