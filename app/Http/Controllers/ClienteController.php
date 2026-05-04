<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ImagenPunto;
use App\Models\ModuloDato;
use App\Models\PuntoInteres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClienteController extends Controller
{
    /** Aborta con 403 si el punto no pertenece al usuario autenticado. */
    private function autorizarPunto(PuntoInteres $punto): void
    {
        abort_if((int) $punto->user_id !== Auth::id(), 403);
    }

    // ─── Alta propia ───────────────────────────────────────────────────────────

    public function onboarding()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('cliente.onboarding', compact('categorias'));
    }

    public function crearNegocio(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'categoria_id'=> ['required', 'exists:categorias,id'],
            'lat'         => ['required', 'numeric', 'between:-90,90'],
            'lng'         => ['required', 'numeric', 'between:-180,180'],
            'imagen'      => ['required', 'image', 'max:5120'],
        ]);

        // Slug único
        $slug = Str::slug($data['title']);
        $base = $slug;
        $i = 2;
        while (PuntoInteres::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        $punto = PuntoInteres::create([
            'user_id'            => Auth::id(),
            'title'              => $data['title'],
            'slug'               => $slug,
            'categoria_id'       => $data['categoria_id'],
            'lat'                => $data['lat'],
            'lng'                => $data['lng'],
            'sector'             => '',
            'description'        => '',
            'es_cliente'         => true,
            'activo'             => true,
            'modulos_habilitados'=> PuntoInteres::modulosDefecto($data['categoria_id']),
        ]);

        $ruta = $request->file('imagen')->store('puntos', 'public');

        ImagenPunto::create([
            'punto_interes_id' => $punto->id,
            'ruta'             => $ruta,
            'es_principal'     => true,
            'orden'            => 0,
        ]);

        return redirect()->route('cliente.perfil.ver', $punto)
            ->with('success', '¡Tu perfil ya está activo en Pindoor!');
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
        $punto->load('moduloDatos', 'moduloItems', 'categoria', 'imagenes');
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

    // ─── Galería de imágenes ───────────────────────────────────────────────────

    public function subirImagen(Request $request, PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);

        $actual      = $punto->imagenes()->count();
        $disponibles = 10 - $actual;

        if ($disponibles <= 0) {
            return back()->with('error', 'Has alcanzado el límite de 10 fotos.');
        }

        $request->validate([
            'imagenes'   => 'required|array|max:10',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $archivos = array_slice($request->file('imagenes'), 0, $disponibles);
        $orden    = ($punto->imagenes()->max('orden') ?? 0) + 1;

        foreach ($archivos as $archivo) {
            ImagenPunto::create([
                'punto_interes_id' => $punto->id,
                'ruta'             => $archivo->store('puntos', 'public'),
                'es_principal'     => false,
                'orden'            => $orden++,
            ]);
        }

        $subidas  = count($archivos);
        $omitidas = count($request->file('imagenes')) - $subidas;
        $msg      = $subidas === 1 ? '1 foto añadida.' : "{$subidas} fotos añadidas.";
        if ($omitidas > 0) {
            $msg .= " ({$omitidas} omitida(s): límite de 10 alcanzado.)";
        }

        return back()->with('success', $msg);
    }

    public function eliminarImagen(PuntoInteres $punto, ImagenPunto $imagen)
    {
        $this->autorizarPunto($punto);
        abort_if($imagen->punto_interes_id !== $punto->id, 403);

        Storage::disk('public')->delete($imagen->ruta);
        $wasPrincipal = $imagen->es_principal;
        $imagen->delete();

        if ($wasPrincipal) {
            $punto->imagenes()->orderBy('orden')->first()?->update(['es_principal' => true]);
        }

        return back()->with('success', 'Foto eliminada.');
    }

    // ─── Actualizaciones rápidas ───────────────────────────────────────────────

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
