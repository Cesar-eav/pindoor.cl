<?php

namespace App\Http\Controllers;

use App\Models\ModuloDato;
use App\Models\PuntoInteres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    /** Aborta con 403 si el punto no pertenece al usuario autenticado. */
    private function autorizarPunto(PuntoInteres $punto): void
    {
        abort_if((int) $punto->user_id !== Auth::id(), 403);
    }

    // ─── Dashboard ─────────────────────────────────────────────────────────────

    public function perfil()
    {
        $puntos = Auth::user()->puntoInteres()
            ->where('eliminado', false)
            ->with('categoria')
            ->latest()
            ->get();

        if ($puntos->isEmpty()) {
            return view('cliente.sin-negocio');
        }

        if ($puntos->count() === 1) {
            return redirect()->route('cliente.perfil.ver', $puntos->first());
        }

        return view('cliente.mis-negocios', compact('puntos'));
    }

    public function verPerfil(PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);
        $punto->load('moduloDatos', 'moduloItems', 'categoria');
        $modulos = $punto->modulos_habilitados ?? [];
        return view('cliente.perfil', compact('punto', 'modulos'));
    }

    // ─── Edición del perfil ────────────────────────────────────────────────────

    public function editarPerfil(PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);

        $punto->load('moduloDatos', 'categoria');
        $modulos          = $punto->modulos_habilitados ?? [];
        $datoCarta        = $punto->dato('carta');
        $datoAlojamiento  = $punto->dato('alojamiento');

        return view('cliente.perfil-editar', compact('punto', 'modulos', 'datoCarta', 'datoAlojamiento'));
    }

    public function actualizarPerfil(Request $request, PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);
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

        return redirect()->route('cliente.perfil.editar', $punto)
            ->with('success', 'Perfil actualizado correctamente.');
    }

    // ─── Actualizaciones rápidas ───────────────────────────────────────────────

    public function actualizarMenu(Request $request, PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);

        $request->validate(['menu_del_dia' => 'nullable|string|max:2000']);

        $punto->moduloDatos()->updateOrCreate(
            ['modulo' => 'menu_del_dia'],
            [
                'datos'         => ['texto' => $request->menu_del_dia ?? ''],
                'actualizado_en'=> $request->filled('menu_del_dia') ? now() : null,
            ]
        );

        return redirect()->route('cliente.perfil.ver', $punto)
            ->with('success', 'Menú del día actualizado.');
    }

    public function actualizarAviso(Request $request, PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);
        $request->validate(['aviso' => 'nullable|string|max:2000']);

        $punto->moduloDatos()->updateOrCreate(
            ['modulo' => 'avisos'],
            [
                'datos'          => ['texto' => $request->aviso ?? ''],
                'actualizado_en' => $request->filled('aviso') ? now() : null,
            ]
        );

        return redirect()->route('cliente.perfil.ver', $punto)
            ->with('success', 'Aviso actualizado.');
    }

    public function actualizarPromocion(Request $request, PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);
        $request->validate(['promocion' => 'nullable|string|max:2000']);

        $punto->moduloDatos()->updateOrCreate(
            ['modulo' => 'promociones'],
            [
                'datos'          => ['texto' => $request->promocion ?? ''],
                'actualizado_en' => $request->filled('promocion') ? now() : null,
            ]
        );

        return redirect()->route('cliente.perfil.ver', $punto)
            ->with('success', 'Promoción actualizada.');
    }

    public function actualizarOferta(Request $request, PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);

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

        return redirect()->route('cliente.perfil.ver', $punto)
            ->with('success', $activa ? 'Oferta activada.' : 'Oferta desactivada.');
    }
}
