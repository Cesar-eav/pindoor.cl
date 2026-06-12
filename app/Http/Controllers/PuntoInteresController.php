<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\PuntoInteres;
use App\Models\ModuloItem;
use App\Models\Panorama;
use App\Models\Post;
use App\Models\Experiencia;
use App\Models\Categoria;
use App\Models\PuntoProducto;
use App\Mail\NuevaExperienciaPropuesta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class PuntoInteresController extends Controller
{

  public function index(Request $request)
{
    try {
        $query = PuntoInteres::query()
            ->where('activo', 1)
            ->whereNotIn('id', [81,80,64,87, 115,128])
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
            ->paginate(48)
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

        $hoy = Carbon::today();

        $proximosPanoramas = Panorama::where('activo', true)
            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')->where('fecha', '>=', $hoy)
                  ->orWhere('fecha_fin', '>=', $hoy);
            })
            ->get()
            ->map(function ($p) use ($hoy) {
                $p->fecha_proxima = $p->proximaOcurrencia($hoy);
                return $p;
            })
            ->filter(fn($p) => $p->fecha_proxima !== null)
            ->sortBy('fecha_proxima')
            ->take(30)
            ->values();

        $ultimosPosts = Post::publicados()->take(10)->get();
        $ultimoPost = $ultimosPosts->first();

        $ultimasExperiencias = Experiencia::activas()->latest()->take(2)->get();

        return view('puntos.index_puntos', compact('atractivos', 'categorias', 'puntosMapData', 'panoramas', 'proximosPanoramas', 'ultimoPost', 'ultimosPosts', 'ultimasExperiencias'));

    } catch (\Exception $e) {
        \Log::error('Error en index: ' . $e->getMessage());
        return back()->with('error', 'Ocurrió un error al buscar.');
    }
}




    public function buscar(Request $request)
    {
        $categorias = Categoria::withCount(['puntosInteres' => fn($q) => $q->where('activo', 1)->where('eliminado', false)->whereNotIn('id', [81,80,64,87,115])])
            ->having('puntos_interes_count', '>', 0)
            ->orderByDesc('puntos_interes_count')
            ->get();

        $grupos = [
            'alimentacion' => ['label' => 'Comer & Beber',  'emoji' => '🍽️', 'items' => collect()],
            'alojamiento'  => ['label' => 'Dónde dormir',   'emoji' => '🛏️', 'items' => collect()],
            'cliente'      => ['label' => 'Comprar',        'emoji' => '🛍️', 'items' => collect()],
            'visitar'      => ['label' => 'Visitar',        'emoji' => '🏛️', 'items' => collect()],
        ];

        foreach ($categorias as $cat) {
            $tipo = $cat->tipo;
            if ($tipo && isset($grupos[$tipo])) {
                $grupos[$tipo]['items']->push($cat);
            } else {
                $grupos['visitar']['items']->push($cat);
            }
        }

        $grupos = array_filter($grupos, fn($g) => $g['items']->isNotEmpty());

        return view('puntos.buscar', compact('grupos'));
    }

    public function filtrarPorCategoria(string $categoria)
    {
        return redirect()->route('puntos.index', ['category' => $categoria], 301);
    }

    public function explorar(Request $request)
    {
        $query = PuntoInteres::query()
            ->where('activo', 1)
            ->whereNotIn('id', [81,80,64,87,115])
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

        $query->latest('updated_at');

        $atractivos = $query
            ->with(['categoria', 'imagenPrincipal'])
            ->paginate(20)
            ->withQueryString();

        $categorias = Categoria::withCount(['puntosInteres' => fn($q) => $q->where('activo', 1)->where('eliminado', false)])
            ->orderByDesc('puntos_interes_count')
            ->get();

        return view('puntos.explorar', compact('atractivos', 'categorias'));
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

        // Eventos de agenda de clientes → convertir a instancias Panorama para reutilizar la vista
        $tipoACat = [
            'concierto'  => 'musica',   'teatro'     => 'teatro',
            'cine'       => 'cine',     'exposicion' => 'exposicion',
            'taller'     => 'taller',   'danza'      => 'danza',
            'conferencia'=> 'conferencia',
        ];
        $eventosCliente = ModuloItem::where('modulo', 'eventos')
            ->where('activo', true)
            ->whereNotNull('fecha')
            ->whereBetween('fecha', [$hoy, $tope])
            ->whereHas('punto', fn($q) => $q->where('activo', true)->where('eliminado', false))
            ->with('punto')
            ->get()
            ->map(function (ModuloItem $item) use ($tipoACat) {
                $fake = new Panorama();
                $fake->fill([
                    'titulo'      => $item->campo('titulo', ''),
                    'ubicacion'   => $item->punto?->nombre,
                    'fecha'       => $item->fecha->format('Y-m-d'),
                    'fecha_fin'   => null,
                    'dias_semana' => null,
                    'hora'        => $item->campo('hora'),
                    'categoria'   => $tipoACat[$item->campo('tipo', 'otro')] ?? 'otros',
                    'es_gratuito' => (float)($item->campo('precio', 1)) === 0.0,
                    'enlace'      => $item->campo('url_entradas'),
                    'imagen'      => $item->imagen,
                    'activo'      => true,
                ]);
                $fake->setAttribute('id', 'ev_' . $item->id);
                $fake->setAttribute('punto_slug', $item->punto?->slug);
                $fake->setRelation('imagenes', collect());
                return $fake;
            });

        $panoramas = $panoramas->merge($eventosCliente)
            ->sortBy(fn($p) => $p->fecha->format('Y-m-d') . ($p->hora ?? '99:99'))
            ->values();

        // Exposiciones: sección propia, no en el calendario diario
        $exposiciones = $panoramas->where('categoria', 'exposicion')->values();

        $coleccion = match(true) {
            $catActiva === 'gratuito'   => $panoramas->where('es_gratuito', true)->where('categoria', '!=', 'exposicion'),
            $catActiva === 'exposicion' => collect(),
            (bool) $catActiva          => $panoramas->where('categoria', $catActiva),
            default                    => $panoramas->where('categoria', '!=', 'exposicion'),
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

        // Agrupar días por mes para navegación
        $mesesMeta  = [];
        $mesPorDia  = [];
        foreach ($porDia as $fechaStr => $_) {
            $f      = Carbon::parse($fechaStr);
            $mesKey = $f->format('Y-m');
            $mesPorDia[$fechaStr] = $mesKey;
            if (!isset($mesesMeta[$mesKey])) {
                $mesesMeta[$mesKey] = [
                    'key'     => $mesKey,
                    'label'   => mb_strtoupper($f->translatedFormat('M')),
                    'titulo'  => ucfirst($f->translatedFormat('F Y')),
                    'primero' => $fechaStr,
                ];
            }
        }

        return view('panoramas.index', compact(
            'panoramas', 'limite', 'categorias', 'catActiva',
            'porDia', 'diasMeta', 'allImages', 'startIndexMap', 'indicesPorDia',
            'exposiciones', 'mesesMeta', 'mesPorDia'
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

    public function proponerForm()
    {
        $categorias = Experiencia::CATEGORIAS;
        $niveles    = Experiencia::NIVELES;
        $dias       = Experiencia::DIAS;
        return view('experiencias.proponer', compact('categorias', 'niveles', 'dias'));
    }

    public function proponerStore(Request $request)
    {
        $request->validate([
            'titulo'         => 'required|string|max:255',
            'proveedor'      => 'required|string|max:200',
            'descripcion'    => 'required|string|max:2000',
            'categoria'      => 'required|in:' . implode(',', array_keys(Experiencia::CATEGORIAS)),
            'dias_semana'    => 'nullable|array',
            'dias_semana.*'  => 'integer|between:1,7',
            'hora'           => 'nullable|string|max:100',
            'ubicacion'      => 'nullable|string|max:255',
            'nivel'          => 'nullable|in:' . implode(',', array_keys(Experiencia::NIVELES)),
            'precio'         => 'nullable|integer|min:0',
            'duracion'       => 'nullable|string|max:50',
            'capacidad'      => 'nullable|integer|min:1',
            'enlace'         => 'nullable|url|max:500',
            'whatsapp'       => 'nullable|string|max:30',
            'email_contacto' => 'nullable|email|max:200',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date|after_or_equal:fecha_inicio',
            'imagen'         => 'nullable|image|max:4096',
        ]);

        $data = $request->only([
            'titulo', 'proveedor', 'descripcion', 'categoria',
            'hora', 'ubicacion', 'nivel', 'duracion', 'capacidad',
            'enlace', 'whatsapp', 'email_contacto', 'fecha_inicio', 'fecha_fin',
        ]);
        $data['dias_semana'] = $request->dias_semana ?? [];
        $data['es_gratuito'] = $request->boolean('es_gratuito');
        $data['precio']      = $data['es_gratuito'] ? null : $request->precio;
        $data['estado']      = 'pendiente';
        $data['activo']      = false;
        $data['orden']       = 99;

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('experiencias', 'public');
        }

        $experiencia = Experiencia::create($data);

        Mail::to(['cesar.eav@gmail.com', 'danielapazcabrera89@gmail.com'])
            ->send(new NuevaExperienciaPropuesta($experiencia));

        return redirect()->route('experiencias.proponer')
            ->with('success', '¡Gracias! Tu experiencia ha sido enviada. La revisaremos pronto y te contactaremos.');
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

    public function showExposicion($slug, ModuloItem $item)
    {
        $punto = PuntoInteres::with(['categoria', 'imagenPrincipal'])
                             ->where('slug', $slug)
                             ->where('activo', true)
                             ->where('eliminado', false)
                             ->firstOrFail();

        abort_if($item->punto_interes_id !== $punto->id || $item->modulo !== 'exposiciones', 404);

        $otras = ModuloItem::where('punto_interes_id', $punto->id)
                           ->where('modulo', 'exposiciones')
                           ->where('activo', true)
                           ->where('id', '!=', $item->id)
                           ->orderBy('orden')
                           ->limit(4)
                           ->get();

        return view('puntos.exposicion', compact('punto', 'item', 'otras'));
    }
}