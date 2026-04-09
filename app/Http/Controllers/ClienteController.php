<?php

namespace App\Http\Controllers;

use App\Models\ModuloDato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    /** Obtiene el PuntoInteres del cliente autenticado o null si no existe. */
    private function miPunto()
    {
        return Auth::user()->puntoInteres()->where('eliminado', false)->first();
    }

    // ─── Dashboard ─────────────────────────────────────────────────────────────

    public function perfil()
    {
        $punto = $this->miPunto();

        if (!$punto) {
            return view('cliente.sin-negocio');
        }

        $punto->load('moduloDatos', 'moduloItems', 'categoria');
        $modulos = $punto->modulos_habilitados ?? [];

        return view('cliente.perfil', compact('punto', 'modulos'));
    }

    // ─── Edición del perfil ────────────────────────────────────────────────────

    public function editarPerfil()
    {
        $punto = $this->miPunto();

        if (!$punto) {
            return redirect()->route('cliente.perfil');
        }

        $punto->load('moduloDatos', 'categoria');
        $modulos          = $punto->modulos_habilitados ?? [];
        $datoCarta        = $punto->dato('carta');
        $datoAlojamiento  = $punto->dato('alojamiento');

        return view('cliente.perfil-editar', compact('punto', 'modulos', 'datoCarta', 'datoAlojamiento'));
    }

    public function actualizarPerfil(Request $request)
    {
        $punto = $this->miPunto();
        $modulos = $punto->modulos_habilitados ?? [];

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
            'habitaciones'        => 'nullable|string',
            'servicios_incluidos' => 'nullable|array',
            'politicas'           => 'nullable|string',
        ]);

        // ── Campos universales en puntosinteres ──────────────────────────────
        $datosPunto = [
            'description'          => $request->description,
            'horario'              => $request->horario,
            'enlace'               => $request->enlace,
            'video_url'            => $request->video_url,
            'tags'                 => $request->tags
                                        ? array_map('trim', explode(',', $request->tags))
                                        : [],
            'descripcion_busqueda' => $request->descripcion_busqueda,
        ];

        if ($request->hasFile('imagen_perfil')) {
            if ($punto->imagen_perfil) {
                Storage::disk('public')->delete($punto->imagen_perfil);
            }
            $datosPunto['imagen_perfil'] = $request->file('imagen_perfil')->store('perfiles', 'public');
        }

        $punto->update($datosPunto);

        // ── Módulo: carta ────────────────────────────────────────────────────
        if (in_array('carta', $modulos)) {
            $registro = $punto->moduloDatos()->firstOrNew(['modulo' => 'carta']);
            $datosCarta = $registro->datos ?? [];

            $datosCarta['texto'] = $request->carta;

            if ($request->boolean('eliminar_carta_pdf') && !empty($datosCarta['pdf_ruta'])) {
                Storage::disk('public')->delete($datosCarta['pdf_ruta']);
                $datosCarta['pdf_ruta'] = null;
            }

            if ($request->hasFile('carta_pdf')) {
                if (!empty($datosCarta['pdf_ruta'])) {
                    Storage::disk('public')->delete($datosCarta['pdf_ruta']);
                }
                $datosCarta['pdf_ruta'] = $request->file('carta_pdf')->store('cartas', 'public');
            }

            $registro->fill([
                'datos'         => $datosCarta,
                'actualizado_en'=> now(),
            ])->save();
        }

        // ── Módulo: alojamiento (habitaciones, servicios, politicas) ─────────
        $modulosAlojamiento = ['habitaciones', 'servicios', 'politicas'];
        if (array_intersect($modulosAlojamiento, $modulos)) {
            $punto->moduloDatos()->updateOrCreate(
                ['modulo' => 'alojamiento'],
                [
                    'datos' => [
                        'precio_desde' => $request->precio_desde,
                        'entrada'      => $request->check_in,
                        'salida'       => $request->check_out,
                        'habitaciones' => $request->habitaciones,
                        'servicios'    => $request->servicios_incluidos ?? [],
                        'politicas'    => $request->politicas,
                    ],
                ]
            );
        }

        return redirect()->route('cliente.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    // ─── Actualizaciones rápidas ───────────────────────────────────────────────

    public function actualizarMenu(Request $request)
    {
        $punto = $this->miPunto();

        if (!$punto) {
            return redirect()->route('cliente.perfil');
        }

        $request->validate(['menu_del_dia' => 'nullable|string|max:2000']);

        $punto->moduloDatos()->updateOrCreate(
            ['modulo' => 'menu_del_dia'],
            [
                'datos'         => ['texto' => $request->menu_del_dia ?? ''],
                'actualizado_en'=> $request->filled('menu_del_dia') ? now() : null,
            ]
        );

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

        $activa  = $request->boolean('oferta_activa');
        $expira  = null;

        if ($activa && $request->filled('duracion_dias')) {
            $expira = now()->addDays((int) $request->duracion_dias);
        }

        $punto->update([
            'oferta_del_dia'   => $request->oferta_del_dia,
            'oferta_activa'    => $activa,
            'oferta_expira_at' => $activa ? $expira : null,
        ]);

        return redirect()->route('cliente.perfil')
            ->with('success', $activa ? 'Oferta activada.' : 'Oferta desactivada.');
    }
}
