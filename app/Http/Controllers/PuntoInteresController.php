<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\PuntoInteres;
use App\Models\Panorama;
use App\Models\Experiencia;
use App\Models\Categoria;
use App\Models\PuntoProducto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PuntoInteresController extends Controller
{

  public function index(Request $request)
{
    try {
        $query = PuntoInteres::query()
            ->where('activo', 1)
            ->whereNotIn('id', [81,80,64,87, 115])
            ->where('eliminado', false);

        if ($request->filled('category')) {
            $query->whereHas('categoria', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('descripcion_busqueda', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // GPS: ordenar por cercanía, sin filtro de radio
        $usoGps = $request->filled('lat') && $request->filled('lng');
        if ($usoGps) {
            $lat = (float) $request->lat;
            $lng = (float) $request->lng;
            $query->whereNotNull('lat')->whereNotNull('lng')
                  ->selectRaw('*, ST_Distance_Sphere(POINT(lng, lat), POINT(?, ?)) as distancia', [$lng, $lat])
                  ->orderBy('distancia', 'asc');
        } else {
            $query->latest('updated_at');
        }

        $atractivos = $query
            ->with(['categoria', 'imagenPrincipal'])
            ->paginate(45)
            ->withQueryString();

        $categorias = Categoria::withCount(['puntosInteres' => fn($q) => $q->where('activo', 1)->where('eliminado', false)])
            ->orderByDesc('puntos_interes_count')
            ->get();

        $puntosMapData = PuntoInteres::where('activo', 1)
            ->where('eliminado', false)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->with(['categoria', 'imagenPrincipal'])
            ->get()
            ->map(fn($p) => [
                'id'        => $p->id,
                'title'     => $p->title,
                'slug'      => $p->slug,
                'lat'       => (float) $p->lat,
                'lng'       => (float) $p->lng,
                'sector'    => $p->sector,
                'categoria'      => $p->categoria?->nombre,
                'categoria_id'   => $p->categoria_id,
                'categoria_slug' => $p->categoria?->slug,
                'icono'          => $p->categoria?->icono,
                'imagen'       => $p->imagenPrincipal ? asset('storage/' . $p->imagenPrincipal->ruta) : null,
                'es_cliente'   => (bool) $p->es_cliente,
            ]);

        $panoramas = collect();
        if ($request->filled('search')) {
            $s = $request->search;
            $panoramas = Panorama::where('activo', true)
                ->where('fecha', '>=', now()->toDateString())
                ->where(fn($q) => $q->where('titulo', 'like', "%{$s}%")
                                    ->orWhere('ubicacion', 'like', "%{$s}%"))
                ->orderBy('fecha')
                ->limit(6)
                ->get();
        }

        return view('puntos.index_puntos', compact('atractivos', 'categorias', 'puntosMapData', 'panoramas'));

    } catch (\Exception $e) {
        \Log::error('Error en index: ' . $e->getMessage());
        return back()->with('error', 'Ocurrió un error al buscar.');
    }
}




    /**
     * Guarda el nuevo punto de interés en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validar la entrada
        $request->validate([
            'title'       => 'required|string|max:255',
            'sector'      => 'required|string',
            'description' => 'required|string',
            'lat'         => 'nullable|numeric',
            'lng'         => 'nullable|numeric',
        ]);

        // 2. Crear el registro
        PuntoInteres::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . rand(100, 999), // Evita duplicados de slug
            'sector'      => $request->sector,
            'description' => $request->description,
            'direccion'   => $request->direccion,
            'autor'       => $request->autor, // <--- GUARDAR
            'tags'        => $request->tags,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'video_url'   => $request->video_url,
            'horario'     => $request->horario,
            'activo'      => true,    // Por defecto nace visible
            'eliminado'   => false,   // Por defecto no está borrado
        ]);

        // Procesar Imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                // Guardar archivo físicamente
                $path = $file->store('puntos', 'public');

                // Guardar en base de datos
                $punto->imagenes()->create([
                    'ruta' => $path,
                    'es_principal' => $request->ordered_images[$index]['is_main'] == 1,
                ]);
            }
        }

        // 3. Redirigir con mensaje de éxito
        return redirect()->route('cliente.mis-puntos')
                         ->with('success', '¡Local registrado correctamente en Pindoor!');
    }

    /**
     * "Borrado lógico": No eliminamos de la DB, solo marcamos como eliminado.
     */
    public function destroy($id)
    {
        $punto = PuntoInteres::where('user_id', Auth::id())->findOrFail($id);
        
        $punto->update([
            'eliminado' => true,
            'activo'    => false
        ]);

        return redirect()->route('cliente.mis-puntos')
                         ->with('success', 'El local ha sido retirado del mapa.');
    }

    public function showPanorama(\App\Models\Panorama $panorama)
    {
        abort_if(!$panorama->activo, 404);
        $panorama->load('imagenes');
        return view('panoramas.show', compact('panorama'));
    }

    public function panoramas(Request $request)
    {
        $limite    = (int) Configuracion::get('panoramas_limite_dias', 15);
        $panoramas = Panorama::activos($limite)->reorder()->orderBy('fecha')->orderBy('hora')->with('imagenes')->get();

        $hoy       = Carbon::today();
        $manana    = Carbon::tomorrow();
        $tope      = $hoy->copy()->addDays($limite);
        $categorias = Panorama::CATEGORIAS;
        $catActiva  = $request->input('categoria');

        $coleccion = match(true) {
            $catActiva === 'gratuito' => $panoramas->where('es_gratuito', true),
            (bool) $catActiva        => $panoramas->where('categoria', $catActiva),
            default                  => $panoramas,
        };

        // Expandir panoramas multi-día
        $porDia = collect();
        foreach ($coleccion as $p) {
            $inicio = $p->fecha->copy();
            $fin    = ($p->fecha_fin && $p->fecha_fin->gt($inicio))
                        ? ($p->fecha_fin->lt($tope) ? $p->fecha_fin : $tope)
                        : $inicio->copy();
            $desde  = $inicio->lt($hoy) ? $hoy->copy() : $inicio->copy();
            $diasRecurrentes = !empty($p->dias_semana) ? $p->dias_semana : null;
            for ($dia = $desde->copy(); $dia->lte($fin); $dia->addDay()) {
                if ($diasRecurrentes && !in_array($dia->isoWeekday(), $diasRecurrentes)) {
                    continue;
                }
                $key = $dia->toDateString();
                if (!$porDia->has($key)) $porDia[$key] = collect();
                $porDia[$key]->push($p);
            }
        }
        $porDia = $porDia->sortKeys();

        // Datos de display por día (etiquetas, títulos)
        $diasMeta = [];
        foreach ($porDia as $fechaStr => $_) {
            $f      = Carbon::parse($fechaStr);
            $esHoy  = $f->isSameDay($hoy);
            $esMana = $f->isSameDay($manana);
            $diasMeta[$fechaStr] = [
                'esHoy'  => $esHoy,
                'esMana' => $esMana,
                'label'  => $esHoy ? 'HOY' : ($esMana ? 'MAÑANA' : mb_strtoupper($f->translatedFormat('D'))),
                'num'    => $f->format('j'),
                'mes'    => mb_strtoupper($f->translatedFormat('M')),
                'titulo' => $esHoy  ? 'Hoy · '     . $f->translatedFormat('l j \d\e F')
                          : ($esMana ? 'Mañana · ' . $f->translatedFormat('l j \d\e F')
                          : ucfirst($f->translatedFormat('l j \d\e F'))),
            ];
        }

        // Lista de imágenes para el lightbox
        $allImages     = collect();
        $startIndexMap = [];
        foreach ($coleccion->values() as $p) {
            $startIndexMap[$p->id] = $allImages->count();
            $fechaLabel = $p->fecha?->translatedFormat('l j \d\e F \d\e Y');
            if ($p->fecha_fin && $p->fecha_fin->gt($p->fecha)) {
                $fechaLabel = $p->fecha->translatedFormat('j \d\e F')
                            . ' al '
                            . $p->fecha_fin->translatedFormat('j \d\e F \d\e Y');
            }
            $info = [
                'titulo'    => $p->titulo,
                'ubicacion' => $p->ubicacion,
                'fecha'     => $fechaLabel,
                'hora'      => $p->hora,
                'enlace'    => $p->enlace,
            ];
            if ($p->imagen) {
                $allImages->push(array_merge($info, ['src' => asset('storage/' . $p->imagen)]));
            }
            foreach ($p->imagenes as $img) {
                $allImages->push(array_merge($info, ['src' => asset('storage/' . $img->ruta)]));
            }
            if (!$p->imagen && $p->imagenes->isEmpty()) {
                $allImages->push(array_merge($info, ['src' => null]));
            }
        }

        $indicesPorDia = [];
        foreach ($porDia as $fechaStr => $grupo) {
            $indicesPorDia[$fechaStr] = $startIndexMap[$grupo->first()->id] ?? 0;
        }

        return view('panoramas.index', compact(
            'panoramas', 'limite', 'categorias', 'catActiva',
            'porDia', 'diasMeta', 'allImages', 'startIndexMap', 'indicesPorDia'
        ));
    }

    /**
     * (Opcional) Vista pública para el turista
     */
    public function show($slug)
    {
        $punto = PuntoInteres::with(['categoria', 'imagenes', 'moduloDatos', 'moduloItems'])
                             ->where('slug', $slug)
                             ->where('activo', true)
                             ->where('eliminado', false)
                             ->firstOrFail();

        $cercanos = collect();
        if ($punto->lat && $punto->lng) {
            $cercanos = PuntoInteres::where('activo', true)
                ->where('eliminado', false)
                ->whereNotIn('id', [81,80,64,87])
                ->where('id', '!=', $punto->id)
                ->whereNotNull('lat')
                ->whereNotNull('lng')
                ->selectRaw(
                    '*, ST_Distance_Sphere(POINT(lng, lat), POINT(?, ?)) as distancia_m',
                    [$punto->lng, $punto->lat]
                )
                ->with(['categoria', 'imagenPrincipal'])
                ->orderBy('distancia_m')
                ->limit(8)
                ->get();
        }

        return view('puntos.show', compact('punto', 'cercanos'));
    }

    public function experiencias(Request $request)
    {
        $catActiva    = $request->input('categoria');
        $experiencias = Experiencia::activas()->with('imagenes')->get();
        $categorias   = Experiencia::CATEGORIAS;

        $coleccion = match(true) {
            $catActiva === 'gratuito' => $experiencias->where('es_gratuito', true),
            (bool) $catActiva        => $experiencias->where('categoria', $catActiva),
            default                  => $experiencias,
        };

        $allImages     = collect();
        $startIndexMap = [];
        foreach ($coleccion->values() as $e) {
            $startIndexMap[$e->id] = $allImages->count();
            $info = [
                'titulo'     => $e->titulo,
                'proveedor'  => $e->proveedor,
                'ubicacion'  => $e->ubicacion,
                'precio'     => $e->precio_formateado,
                'enlace'     => $e->enlace,
                'whatsapp'   => $e->whatsapp_url,
            ];
            if ($e->imagen) {
                $allImages->push(array_merge($info, ['src' => asset('storage/' . $e->imagen)]));
            }
            foreach ($e->imagenes as $img) {
                $allImages->push(array_merge($info, ['src' => asset('storage/' . $img->ruta)]));
            }
            if (!$e->imagen && $e->imagenes->isEmpty()) {
                $allImages->push(array_merge($info, ['src' => null]));
            }
        }

        return view('experiencias.index', compact(
            'experiencias', 'coleccion', 'categorias', 'catActiva', 'allImages', 'startIndexMap'
        ));
    }

    public function showExperiencia(Experiencia $experiencia)
    {
        abort_if(!$experiencia->activo, 404);
        $experiencia->load('imagenes');
        return view('experiencias.show', compact('experiencia'));
    }

    public function showProducto($slug, PuntoProducto $producto)
    {
        $punto = PuntoInteres::with(['categoria', 'imagenes', 'productos'])
                             ->where('slug', $slug)
                             ->where('activo', true)
                             ->where('eliminado', false)
                             ->firstOrFail();

        abort_if($producto->punto_interes_id !== $punto->id, 404);

        return view('puntos.producto', compact('punto', 'producto'));
    }
}