<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\ImagenPunto;
use App\Models\ModuloDato;
use App\Models\PuntoInteres;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $categorias = Categoria::whereIn('slug', ['cafeterias', 'cultura', 'museos', 'picadas', 'comer', 'alojar', 'tiendas', 'artesania','centro-deportivo','bar'])
            ->orderBy('nombre')
            ->get();
        return view('cliente.onboarding', compact('categorias'));
    }

    /** Geocodifica una dirección dentro de Valparaíso. Usa Google Maps si hay API key configurada, si no cae a Nominatim (OSM). */
    public function geocodificar(Request $request)
    {
        $request->validate(['q' => 'required|string|max:255']);
        $query = $request->q . ', Valparaíso, Chile';

        $mapsKey = config('services.google.maps_key');
        if ($mapsKey) {
            $resultado = $this->geocodificarConGoogle($query, $mapsKey);
            if ($resultado) {
                return response()->json($resultado);
            }
        }

        return response()->json($this->geocodificarConNominatim($query) ?? ['encontrado' => false]);
    }

    private function geocodificarConGoogle(string $query, string $key): ?array
    {
        try {
            $response = Http::timeout(5)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $query,
                'region'  => 'cl',
                'bounds'  => '-33.15,-71.72|-32.90,-71.55',
                'key'     => $key,
            ]);
            $data = $response->json();

            if (($data['status'] ?? null) !== 'OK' || empty($data['results'])) {
                return null;
            }

            $r = $data['results'][0];
            return [
                'encontrado' => true,
                'lat'        => $r['geometry']['location']['lat'],
                'lng'        => $r['geometry']['location']['lng'],
                'direccion'  => $r['formatted_address'],
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function geocodificarConNominatim(string $query): ?array
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['User-Agent' => 'Pindoor/1.0 (https://pindoor.cl)'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q'            => $query,
                    'format'       => 'json',
                    'limit'        => 1,
                    'countrycodes' => 'cl',
                    'viewbox'      => '-71.72,-32.90,-71.55,-33.15',
                    'bounded'      => 1,
                ]);
            $resultados = $response->json();

            if (empty($resultados)) {
                return null;
            }

            return [
                'encontrado' => true,
                'lat'        => (float) $resultados[0]['lat'],
                'lng'        => (float) $resultados[0]['lon'],
                'direccion'  => $resultados[0]['display_name'],
            ];
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function crearNegocio(Request $request)
    {
        $userId = Auth::id();
        Log::info('[onboarding] inicio', [
            'user_id'       => $userId,
            'tiene_imagen'  => $request->hasFile('imagen'),
            'imagen_nombre' => $request->file('imagen')?->getClientOriginalName(),
            'imagen_mime'   => $request->file('imagen')?->getMimeType(),
            'imagen_kb'     => $request->file('imagen') ? round($request->file('imagen')->getSize() / 1024) : null,
        ]);

        try {
            $data = $request->validate([
                'title'       => ['required', 'string', 'max:255'],
                'categoria_id'=> ['required', 'exists:categorias,id'],
                'lat'         => ['required', 'numeric', 'between:-90,90'],
                'lng'         => ['required', 'numeric', 'between:-180,180'],
                'direccion'   => ['nullable', 'string', 'max:255'],
                'imagen'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:25600'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('[onboarding] validación falló', ['user_id' => $userId, 'errores' => $e->errors()]);
            throw $e;
        }

        // Slug único
        $slug = Str::slug($data['title']);
        $base = $slug;
        $i = 2;
        while (PuntoInteres::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        $punto = PuntoInteres::create([
            'user_id'            => $userId,
            'title'              => $data['title'],
            'slug'               => $slug,
            'categoria_id'       => $data['categoria_id'],
            'lat'                => $data['lat'],
            'lng'                => $data['lng'],
            'direccion'          => $data['direccion'] ?? '',
            'sector'             => '',
            'description'        => '',
            'es_cliente'         => true,
            'activo'             => false,
            'modulos_habilitados'=> PuntoInteres::modulosDefecto($data['categoria_id']),
        ]);

        Log::info('[onboarding] punto creado', ['user_id' => $userId, 'punto_id' => $punto->id, 'slug' => $slug]);

        $mensaje = 'Guardamos tu espacio. Sube al menos una foto desde tu panel para que sea visible en Pindoor.';

        if ($request->hasFile('imagen')) {
            try {
                $ruta = $this->guardarImagenComprimida($request->file('imagen'), 'puntos');
                ImagenPunto::create([
                    'punto_interes_id' => $punto->id,
                    'ruta'             => $ruta,
                    'es_principal'     => true,
                    'orden'            => 0,
                ]);
                $punto->update(['activo' => true]);
                $mensaje = '¡Tu perfil ya está activo en Pindoor!';
                Log::info('[onboarding] imagen procesada OK', ['user_id' => $userId, 'punto_id' => $punto->id, 'ruta' => $ruta]);
            } catch (\RuntimeException $e) {
                $mensaje = 'Guardamos tu espacio, pero no pudimos procesar esa foto (' . $e->getMessage() . '). Sube otra desde tu panel para que sea visible en Pindoor.';
                Log::error('[onboarding] fallo al procesar imagen', [
                    'user_id'  => $userId,
                    'punto_id' => $punto->id,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        Log::info('[onboarding] fin', ['user_id' => $userId, 'punto_id' => $punto->id, 'activo' => $punto->fresh()->activo]);

        return redirect()->route('cliente.perfil.ver', $punto)->with('success', $mensaje);
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
        return $this->mostrarPerfil($punto);
    }

    public function editarPerfil(PuntoInteres $punto)
    {
        return $this->mostrarPerfil($punto);
    }

    private function mostrarPerfil(PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);
        $punto->load('moduloDatos', 'moduloItems', 'categoria', 'imagenes');
        $modulos         = $punto->modulos_habilitados ?? [];
        $datoCarta       = $punto->dato('carta');
        $datoAlojamiento = $punto->dato('alojamiento');
        $categorias      = Categoria::whereIn('slug', [
            'cafeterias', 'cultura', 'museos', 'picadas', 'comer',
            'alojar', 'tiendas', 'artesania', 'centro-deportivo', 'bar'
        ])->orderBy('nombre')->get();
        return view('cliente.perfil', compact('punto', 'modulos', 'datoCarta', 'datoAlojamiento', 'categorias'));
    }

    public function actualizarPerfil(Request $request, PuntoInteres $punto)
    {
        $this->autorizarPunto($punto);
        $modulos = $punto->modulos_habilitados ?? [];

        $request->validate([
            'description'         => 'sometimes|nullable|string',
            'horario'             => 'nullable|string|max:255',
            'enlace'              => 'nullable|url|max:255',
            'video_url'           => 'nullable|url|max:255',
            'tags'                => 'nullable|string',
            'descripcion_busqueda'=> 'nullable|string',
            'imagen_perfil'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:25600',
            'categoria_id'        => 'sometimes|nullable|exists:categorias,id',
            // Alimentación
            'carta'               => 'nullable|string',
            'carta_pdf'           => 'nullable|file|mimes:pdf|max:30720',
            // Alojamiento
            'precio_desde'        => 'nullable|string|max:100',
            'check_in'            => 'nullable|string|max:20',
            'check_out'           => 'nullable|string|max:20',
            'habitaciones'        => 'nullable|string',
            'servicios_incluidos' => 'nullable|array',
            'politicas'           => 'nullable|string',
        ]);

        // ── Campos universales en puntosinteres ──────────────────────────────
        $datosPunto = array_filter([
            'description'          => $request->has('description') ? $request->description : null,
            'horario'              => $request->has('horario') ? $request->horario : null,
            'sector'               => $request->has('sector') ? $request->sector : null,
            'direccion'            => $request->has('direccion') ? $request->direccion : null,
            'enlace'               => $request->has('enlace') ? $request->enlace : null,
            'video_url'            => $request->has('video_url') ? $request->video_url : null,
            'tags'                 => $request->has('tags')
                                        ? ($request->tags ? array_map('trim', explode(',', $request->tags)) : [])
                                        : null,
            'descripcion_busqueda' => $request->has('descripcion_busqueda') ? $request->descripcion_busqueda : null,
            'categoria_id'         => $request->has('categoria_id') ? $request->categoria_id : null,
        ], fn($v) => $v !== null);

        if ($request->hasFile('imagen_perfil')) {
            try {
                $ruta = $this->guardarImagenComprimida($request->file('imagen_perfil'), 'perfiles');
                if ($punto->imagen_perfil) {
                    Storage::disk('public')->delete($punto->imagen_perfil);
                }
                $datosPunto['imagen_perfil'] = $ruta;
            } catch (\RuntimeException $e) {
                Log::error('[logo] fallo al procesar imagen', [
                    'user_id'  => Auth::id(),
                    'punto_id' => $punto->id,
                    'error'    => $e->getMessage(),
                ]);
                return back()->withErrors(['imagen_perfil' => $e->getMessage()]);
            }
        } elseif (!$punto->imagen_perfil && auth()->user()->imagen_logo) {
            $datosPunto['imagen_perfil'] = auth()->user()->imagen_logo;
        }

        $punto->update($datosPunto);

        // ── Módulo: carta ────────────────────────────────────────────────────
        if (in_array('carta', $modulos) &&
            ($request->has('carta') || $request->hasFile('carta_pdf') || $request->boolean('eliminar_carta_pdf'))) {

            $registro   = $punto->moduloDatos()->firstOrNew(['modulo' => 'carta']);
            $datosCarta = $registro->datos ?? [];

            if ($request->has('carta')) {
                $datosCarta['texto'] = $request->carta;
            }

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

            $registro->fill(['datos' => $datosCarta, 'actualizado_en' => now()])->save();
        }

        // ── Módulo: alojamiento (habitaciones, servicios, politicas) ─────────
        $modulosAlojamiento = ['habitaciones', 'servicios', 'politicas'];
        if (array_intersect($modulosAlojamiento, $modulos) &&
            $request->hasAny(['precio_desde', 'check_in', 'check_out', 'habitaciones', 'servicios_incluidos', 'politicas'])) {

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

        return redirect()->route('cliente.perfil.ver', $punto)
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
        $esPrimera   = $actual === 0;

        if ($disponibles <= 0) {
            return back()->with('error', 'Has alcanzado el límite de 10 fotos.');
        }

        $request->validate([
            'imagenes'   => 'required|array|max:10',
            'imagenes.*' => 'image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $archivos = array_slice($request->file('imagenes'), 0, $disponibles);
        $orden    = ($punto->imagenes()->max('orden') ?? 0) + 1;

        $fallidas         = 0;
        $subidas          = 0;
        $yaAsignoPrincipal = false;
        foreach ($archivos as $archivo) {
            try {
                ImagenPunto::create([
                    'punto_interes_id' => $punto->id,
                    'ruta'             => $this->guardarImagenComprimida($archivo, 'puntos'),
                    'es_principal'     => $esPrimera && !$yaAsignoPrincipal,
                    'orden'            => $orden++,
                ]);
                $subidas++;
                $yaAsignoPrincipal = $esPrimera || $yaAsignoPrincipal;
            } catch (\RuntimeException $e) {
                $fallidas++;
                Log::error('[galeria] fallo al procesar imagen', [
                    'user_id'  => Auth::id(),
                    'punto_id' => $punto->id,
                    'archivo'  => $archivo->getClientOriginalName(),
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        // Primera foto que sube el negocio: la convierte en visible en Pindoor.
        $seActivo = false;
        if ($esPrimera && $subidas > 0 && !$punto->activo) {
            $punto->update(['activo' => true]);
            $seActivo = true;
        }

        $omitidas = count($request->file('imagenes')) - $subidas - $fallidas;
        $msg      = $subidas === 1 ? '1 foto añadida.' : "{$subidas} fotos añadidas.";
        if ($fallidas > 0) {
            $msg .= " {$fallidas} no se pudo procesar (formato no compatible, prueba con JPG o PNG).";
        }
        if ($omitidas > 0) {
            $msg .= " ({$omitidas} omitida(s): límite de 10 alcanzado.)";
        }
        if ($seActivo) {
            $msg = '¡Tu ficha ya es visible en Pindoor! ' . $msg;
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

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function guardarImagenComprimida($archivo, string $carpeta, int $maxWidth = 1600, int $calidad = 80): string
    {
        if (!extension_loaded('gd')) {
            throw new \RuntimeException('El servidor no tiene la extensión GD de PHP habilitada, no se pueden procesar imágenes.');
        }

        $img = imagecreatefromstring(file_get_contents($archivo->getPathname()));

        if ($img === false) {
            // GD no pudo decodificar el archivo — típico con fotos HEIC de iPhone,
            // que "pasan" la validación 'image' de Laravel pero GD no las soporta.
            throw new \RuntimeException('No pudimos procesar esa imagen. Prueba con un formato JPG o PNG.');
        }

        $w = imagesx($img);
        $h = imagesy($img);

        if ($w > $maxWidth) {
            $nuevoH = (int) round($h * $maxWidth / $w);
            $redim  = imagecreatetruecolor($maxWidth, $nuevoH);
            imagealphablending($redim, false);
            imagesavealpha($redim, true);
            imagecopyresampled($redim, $img, 0, 0, 0, 0, $maxWidth, $nuevoH, $w, $h);
            imagedestroy($img);
            $img = $redim;
        }

        ob_start();
        imagewebp($img, null, $calidad);
        $webp = ob_get_clean();
        imagedestroy($img);

        $ruta = $carpeta . '/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($ruta, $webp);

        return $ruta;
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
