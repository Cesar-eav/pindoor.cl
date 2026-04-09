<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    /**
     * Obtiene el PuntoInteres del cliente autenticado o aborta 404.
     */
    private function miPunto()
    {
        return Auth::user()->puntoInteres()->where('eliminado', false)->first();
    }

    /**
     * Dashboard del cliente: muestra su perfil y oferta del día.
     */
    public function perfil()
    {
        $punto = $this->miPunto();

        if (!$punto) {
            return view('cliente.sin-negocio');
        }

        return view('cliente.perfil', compact('punto'));
    }

    /**
     * Formulario completo de edición del perfil.
     */
    public function editarPerfil()
    {
        $punto = $this->miPunto();

        if (!$punto) {
            return redirect()->route('cliente.perfil');
        }

        return view('cliente.perfil-editar', compact('punto'));
    }

    /**
     * Guarda los cambios del formulario de edición completa.
     * El cliente puede editar: descripcion, horario, enlace, tags,
     * descripcion_busqueda e imagen_perfil. No puede cambiar título ni ubicación.
     */
    public function actualizarPerfil(Request $request)
    {
        $punto = $this->miPunto();

        $request->validate([
            'description'         => 'required|string',
            'horario'             => 'nullable|string|max:255',
            'enlace'              => 'nullable|url|max:255',
            'tags'                => 'nullable|string',
            'descripcion_busqueda'=> 'nullable|string',
            'imagen_perfil'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'description'          => $request->description,
            'horario'              => $request->horario,
            'enlace'               => $request->enlace,
            'tags'                 => $request->tags
                                        ? array_map('trim', explode(',', $request->tags))
                                        : [],
            'descripcion_busqueda' => $request->descripcion_busqueda,
        ];

        if ($request->hasFile('imagen_perfil')) {
            // Eliminar imagen anterior si existe
            if ($punto->imagen_perfil) {
                Storage::disk('public')->delete($punto->imagen_perfil);
            }
            $data['imagen_perfil'] = $request->file('imagen_perfil')->store('perfiles', 'public');
        }

        $punto->update($data);

        return redirect()->route('cliente.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualización rápida de la oferta del día (desde el dashboard).
     */
    public function actualizarOferta(Request $request)
    {
        $punto = $this->miPunto();

        if (!$punto) {
            return redirect()->route('cliente.perfil');
        }

        $request->validate([
            'oferta_del_dia' => 'nullable|string|max:1000',
        ]);

        $punto->update(['oferta_del_dia' => $request->oferta_del_dia]);

        return redirect()->route('cliente.perfil')
            ->with('success', 'Oferta del día actualizada.');
    }
}
