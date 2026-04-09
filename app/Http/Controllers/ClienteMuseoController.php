<?php

namespace App\Http\Controllers;

use App\Models\ModuloItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteMuseoController extends Controller
{
    private function miPunto()
    {
        return Auth::user()->puntoInteres()->where('eliminado', false)->first();
    }

    /** Dashboard del museo: gestión de entradas y exposiciones. */
    public function index()
    {
        $punto = $this->miPunto();

        if (!$punto || !$punto->esMuseo()) {
            return redirect()->route('cliente.perfil');
        }

        $punto->load('moduloDatos', 'moduloItems');

        $entradas     = $punto->items('entradas');
        $exposiciones = $punto->items('exposiciones');

        return view('cliente.museo', compact('punto', 'entradas', 'exposiciones'));
    }

    /**
     * Reemplaza el set completo de tarifas de entrada.
     * Borra las existentes y recrea desde el formulario.
     */
    public function guardarEntradas(Request $request)
    {
        $punto = $this->miPunto();

        if (!$punto || !$punto->esMuseo()) {
            return redirect()->route('cliente.perfil');
        }

        $request->validate([
            'entradas'               => 'nullable|array',
            'entradas.*.etiqueta'    => 'required|string|max:100',
            'entradas.*.precio'      => 'required|numeric|min:0',
            'entradas.*.nota'        => 'nullable|string|max:255',
        ]);

        ModuloItem::where('punto_interes_id', $punto->id)
            ->where('modulo', 'entradas')
            ->delete();

        foreach ($request->input('entradas', []) as $orden => $fila) {
            ModuloItem::create([
                'punto_interes_id' => $punto->id,
                'modulo'           => 'entradas',
                'datos'            => [
                    'etiqueta' => $fila['etiqueta'],
                    'precio'   => $fila['precio'],
                    'nota'     => $fila['nota'] ?? null,
                ],
                'activo' => true,
                'orden'  => $orden,
            ]);
        }

        return redirect()->route('cliente.museo')
            ->with('success', 'Tarifas de entrada actualizadas.');
    }

    /** Crea o actualiza una exposición. */
    public function guardarExposicion(Request $request)
    {
        $punto = $this->miPunto();

        if (!$punto || !$punto->esMuseo()) {
            return redirect()->route('cliente.perfil');
        }

        $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'tipo'         => 'required|in:permanente,temporal',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'item_id'      => 'nullable|integer|exists:punto_modulo_items,id',
        ]);

        $datos = [
            'titulo'       => $request->titulo,
            'descripcion'  => $request->descripcion,
            'tipo'         => $request->tipo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->tipo === 'temporal' ? $request->fecha_fin : null,
        ];

        $rutaImagen = null;

        if ($request->hasFile('imagen')) {
            if ($request->filled('item_id')) {
                $item = ModuloItem::find($request->item_id);
                if ($item && $item->imagen) {
                    Storage::disk('public')->delete($item->imagen);
                }
            }
            $rutaImagen = $request->file('imagen')->store('exposiciones', 'public');
        }

        if ($request->filled('item_id')) {
            $item = ModuloItem::where('id', $request->item_id)
                ->where('punto_interes_id', $punto->id)
                ->where('modulo', 'exposiciones')
                ->firstOrFail();

            $item->datos = $datos;
            if ($rutaImagen) {
                $item->imagen = $rutaImagen;
            }
            $item->save();
        } else {
            $ordenSiguiente = ModuloItem::where('punto_interes_id', $punto->id)
                ->where('modulo', 'exposiciones')
                ->max('orden') + 1;

            ModuloItem::create([
                'punto_interes_id' => $punto->id,
                'modulo'           => 'exposiciones',
                'datos'            => $datos,
                'imagen'           => $rutaImagen,
                'activo'           => true,
                'orden'            => $ordenSiguiente,
            ]);
        }

        return redirect()->route('cliente.museo')
            ->with('success', 'Exposición guardada.');
    }

    /** Elimina una exposición. */
    public function eliminarExposicion(ModuloItem $exposicion)
    {
        $punto = $this->miPunto();

        if (!$punto || $exposicion->punto_interes_id !== $punto->id || $exposicion->modulo !== 'exposiciones') {
            abort(403);
        }

        if ($exposicion->imagen) {
            Storage::disk('public')->delete($exposicion->imagen);
        }

        $exposicion->delete();

        return redirect()->route('cliente.museo')
            ->with('success', 'Exposición eliminada.');
    }
}
