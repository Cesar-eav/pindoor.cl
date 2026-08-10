<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\ArtistaEvento;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArtistaEventosController extends Controller
{
    private function verificarArtista(Artista $artista): bool
    {
        return $artista->user_id === Auth::id();
    }

    /** Crea o actualiza un evento del artista. */
    public function guardarEvento(Request $request, Artista $artista)
    {
        if (!$this->verificarArtista($artista)) {
            return redirect()->route('artista.perfil');
        }

        $request->validate([
            'titulo'       => 'required|string|max:255',
            'descripcion'  => 'nullable|string',
            'ubicacion'    => 'nullable|string|max:255',
            'tipo'         => 'required|string|in:' . implode(',', \App\Models\CategoriaEvento::slugs()),
            'fecha'        => 'required|date',
            'hora'         => 'nullable|date_format:H:i',
            'hora_fin'     => 'nullable|date_format:H:i',
            'precio'       => 'nullable|numeric|min:0',
            'precio_texto' => 'nullable|string|max:100',
            'url_entradas' => 'nullable|url|max:255',
            'destacado'    => 'boolean',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            'item_id'      => 'nullable|integer|exists:artista_eventos,id',
        ]);

        $datos = [
            'titulo'       => $request->titulo,
            'tipo'         => $request->tipo,
            'descripcion'  => $request->descripcion,
            'ubicacion'    => $request->ubicacion,
            'fecha'        => $request->fecha,
            'hora'         => $request->hora,
            'hora_fin'     => $request->hora_fin,
            'precio'       => $request->precio,
            'precio_texto' => $request->precio_texto,
            'url_entradas' => $request->url_entradas,
            'destacado'    => $request->boolean('destacado'),
        ];

        if ($request->hasFile('imagen')) {
            if ($request->filled('item_id')) {
                $item = ArtistaEvento::find($request->item_id);
                if ($item && $item->imagen) {
                    Storage::disk('public')->delete($item->imagen);
                }
            }
            $datos['imagen'] = ImagenComprimida::guardar($request->file('imagen'), 'artista-eventos');
        }

        if ($request->filled('item_id')) {
            $item = ArtistaEvento::where('id', $request->item_id)
                ->where('artista_id', $artista->id)
                ->firstOrFail();

            $item->update($datos);
        } else {
            $datos['artista_id'] = $artista->id;
            ArtistaEvento::create($datos);
        }

        return redirect()->route('artista.perfil')
            ->with('success', 'Evento guardado.');
    }

    /** Elimina un evento. */
    public function eliminarEvento(Artista $artista, ArtistaEvento $evento)
    {
        if (!$this->verificarArtista($artista) || $evento->artista_id !== $artista->id) {
            abort(403);
        }

        if ($evento->imagen) {
            Storage::disk('public')->delete($evento->imagen);
        }

        $evento->delete();

        return redirect()->route('artista.perfil')
            ->with('success', 'Evento eliminado.');
    }
}
