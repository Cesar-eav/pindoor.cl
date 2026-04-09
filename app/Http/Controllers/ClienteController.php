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

        $modulos = $punto->modulos_habilitados ?? [];

        return view('cliente.perfil', compact('punto', 'modulos'));
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

        $modulos = $punto->modulos_habilitados ?? [];

        return view('cliente.perfil-editar', compact('punto', 'modulos'));
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
            'video_url'           => 'nullable|url|max:255',
            'tags'                => 'nullable|string',
            'descripcion_busqueda'=> 'nullable|string',
            'imagen_perfil'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // Alimentación
            'carta'               => 'nullable|string',
            'carta_pdf'           => 'nullable|file|mimes:pdf|max:5120',
            // Alojamiento
            'precio_desde'        => 'nullable|string|max:100',
            'check_in'            => 'nullable|string|max:20',
            'check_out'           => 'nullable|string|max:20',
            'tipos_habitacion'    => 'nullable|string',
            'servicios_incluidos' => 'nullable|array',
            'politicas'           => 'nullable|string',
        ]);

        $data = [
            'description'          => $request->description,
            'horario'              => $request->horario,
            'enlace'               => $request->enlace,
            'video_url'            => $request->video_url,
            'tags'                 => $request->tags
                                        ? array_map('trim', explode(',', $request->tags))
                                        : [],
            'descripcion_busqueda' => $request->descripcion_busqueda,
            'carta'                => $request->carta,
            // Alojamiento
            'precio_desde'         => $request->precio_desde,
            'check_in'             => $request->check_in,
            'check_out'            => $request->check_out,
            'tipos_habitacion'     => $request->tipos_habitacion,
            'servicios_incluidos'  => $request->servicios_incluidos ?? [],
            'politicas'            => $request->politicas,
        ];

        if ($request->hasFile('imagen_perfil')) {
            if ($punto->imagen_perfil) {
                Storage::disk('public')->delete($punto->imagen_perfil);
            }
            $data['imagen_perfil'] = $request->file('imagen_perfil')->store('perfiles', 'public');
        }

        if ($request->boolean('eliminar_carta_pdf') && $punto->carta_pdf) {
            Storage::disk('public')->delete($punto->carta_pdf);
            $data['carta_pdf'] = null;
        }

        if ($request->hasFile('carta_pdf')) {
            if ($punto->carta_pdf) {
                Storage::disk('public')->delete($punto->carta_pdf);
            }
            $data['carta_pdf'] = $request->file('carta_pdf')->store('cartas', 'public');
        }

        if (array_key_exists('carta', $data) && ($data['carta'] !== $punto->carta || isset($data['carta_pdf']))) {
            $data['carta_updated_at'] = now();
        }

        $punto->update($data);

        return redirect()->route('cliente.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Actualización rápida de la oferta del día (desde el dashboard).
     */
    public function actualizarMenu(Request $request)
    {
        $punto = $this->miPunto();

        if (!$punto) {
            return redirect()->route('cliente.perfil');
        }

        $request->validate([
            'menu_del_dia' => 'nullable|string|max:2000',
        ]);

        $punto->update([
            'menu_del_dia'            => $request->menu_del_dia,
            'menu_del_dia_updated_at' => $request->menu_del_dia ? now() : null,
        ]);

        return redirect()->route('cliente.perfil')
            ->with('success', 'Menú del día actualizado.');
    }

    public function actualizarOferta(Request $request)
    {
        $punto = $this->miPunto();

        if (!$punto) {
            return redirect()->route('cliente.perfil');
        }

        $request->validate([
            'oferta_del_dia' => 'nullable|string|max:1000',
            'oferta_activa'  => 'boolean',
            'duracion_dias'  => 'nullable|integer|min:1|max:30',
        ]);

        $activa = $request->boolean('oferta_activa');

        $expira = null;
        if ($activa && $request->filled('duracion_dias')) {
            $expira = now()->addDays((int) $request->duracion_dias);
        }

        $punto->update([
            'oferta_del_dia'  => $request->oferta_del_dia,
            'oferta_activa'   => $activa,
            'oferta_expira_at' => $activa ? $expira : null,
        ]);

        return redirect()->route('cliente.perfil')
            ->with('success', $activa ? 'Oferta activada.' : 'Oferta desactivada.');
    }
}
