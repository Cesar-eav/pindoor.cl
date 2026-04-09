<?php

namespace App\Http\Controllers;

use App\Models\ModuloItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteEventosController extends Controller
{
    private function miPunto()
    {
        return Auth::user()->puntoInteres()->where('eliminado', false)->first();
    }

    /** Dashboard del centro cultural: gestión de agenda de eventos. */
    public function index()
    {
        $punto = $this->miPunto();

        if (!$punto || !$punto->esCultura()) {
            return redirect()->route('cliente.perfil');
        }

        $punto->load('moduloItems');

        $eventos = ModuloItem::where('punto_interes_id', $punto->id)
            ->where('modulo', 'eventos')
            ->orderBy('fecha')
            ->orderByRaw("JSON_EXTRACT(datos, '$.hora')")
            ->get();

        $tiposEvento = ModuloItem::catalogoTiposEvento();

        return view('cliente.eventos', compact('punto', 'eventos', 'tiposEvento'));
    }

    /** Crea o actualiza un evento en la agenda. */
    public function guardarEvento(Request $request)
    {
        $punto = $this->miPunto();

        if (!$punto || !$punto->esCultura()) {
            return redirect()->route('cliente.perfil');
        }

        $request->validate([
            'titulo'             => 'required|string|max:255',
            'descripcion'        => 'nullable|string',
            'tipo'               => 'required|string',
            'fecha'              => 'required|date',
            'hora'               => 'nullable|date_format:H:i',
            'hora_fin'           => 'nullable|date_format:H:i',
            'precio'             => 'nullable|numeric|min:0',
            'precio_texto'       => 'nullable|string|max:100',
            'url_entradas'       => 'nullable|url|max:255',
            'destacado'          => 'boolean',
            'imagen'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'item_id'            => 'nullable|integer|exists:punto_modulo_items,id',
        ]);

        $datos = [
            'titulo'       => $request->titulo,
            'descripcion'  => $request->descripcion,
            'tipo'         => $request->tipo,
            'hora'         => $request->hora,
            'hora_fin'     => $request->hora_fin,
            'precio'       => $request->precio,
            'precio_texto' => $request->precio_texto,
            'url_entradas' => $request->url_entradas,
        ];

        $rutaImagen = null;

        if ($request->hasFile('imagen')) {
            if ($request->filled('item_id')) {
                $item = ModuloItem::find($request->item_id);
                if ($item && $item->imagen) {
                    Storage::disk('public')->delete($item->imagen);
                }
            }
            $rutaImagen = $request->file('imagen')->store('eventos', 'public');
        }

        if ($request->filled('item_id')) {
            $item = ModuloItem::where('id', $request->item_id)
                ->where('punto_interes_id', $punto->id)
                ->where('modulo', 'eventos')
                ->firstOrFail();

            $item->datos     = $datos;
            $item->fecha     = $request->fecha;
            $item->destacado = $request->boolean('destacado');
            if ($rutaImagen) {
                $item->imagen = $rutaImagen;
            }
            $item->save();
        } else {
            ModuloItem::create([
                'punto_interes_id' => $punto->id,
                'modulo'           => 'eventos',
                'datos'            => $datos,
                'imagen'           => $rutaImagen,
                'activo'           => true,
                'destacado'        => $request->boolean('destacado'),
                'fecha'            => $request->fecha,
            ]);
        }

        return redirect()->route('cliente.eventos')
            ->with('success', 'Evento guardado en la agenda.');
    }

    /** Elimina un evento. */
    public function eliminarEvento(ModuloItem $evento)
    {
        $punto = $this->miPunto();

        if (!$punto || $evento->punto_interes_id !== $punto->id || $evento->modulo !== 'eventos') {
            abort(403);
        }

        if ($evento->imagen) {
            Storage::disk('public')->delete($evento->imagen);
        }

        $evento->delete();

        return redirect()->route('cliente.eventos')
            ->with('success', 'Evento eliminado.');
    }
}
