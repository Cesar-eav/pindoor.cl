<?php

namespace App\Http\Controllers;

use App\Models\PuntoProducto;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteProductosController extends Controller
{
    private function puntoAutorizado()
    {
        return Auth::user()->puntoInteres()->firstOrFail();
    }

    public function store(Request $request)
    {
        $punto = $this->puntoAutorizado();

        $data = $request->validate([
            'nombre'      => 'required|string|max:150',
            'precio'      => 'nullable|string|max:40',
            'descripcion' => 'nullable|string|max:500',
            'imagen'      => 'nullable|image|max:4096',
            'orden'       => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = ImagenComprimida::guardar($request->file('imagen'), 'productos');
        }

        $data['punto_interes_id'] = $punto->id;
        $data['orden'] = $punto->productos()->max('orden') + 1;

        PuntoProducto::create($data);

        return back()->with('success', 'Producto agregado.');
    }

    public function update(Request $request, PuntoProducto $producto)
    {
        abort_if($producto->punto_interes_id !== $this->puntoAutorizado()->id, 403);

        $data = $request->validate([
            'nombre'      => 'required|string|max:150',
            'precio'      => 'nullable|string|max:40',
            'descripcion' => 'nullable|string|max:500',
            'imagen'      => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen) Storage::disk('public')->delete($producto->imagen);
            $data['imagen'] = ImagenComprimida::guardar($request->file('imagen'), 'productos');
        }

        $producto->update($data);

        return back()->with('success', 'Producto actualizado.');
    }

    public function destroy(PuntoProducto $producto)
    {
        abort_if($producto->punto_interes_id !== $this->puntoAutorizado()->id, 403);
        $producto->delete();
        return back()->with('success', 'Producto eliminado.');
    }
}
