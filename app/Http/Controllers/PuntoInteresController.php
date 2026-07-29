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
use App\Models\Artista;
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
            ->sinExcluidos()
            ->where('eliminado', false);

        if ($request->filled('category')) {
            $query->whereHas('categoria', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $searchLower = mb_strtolower($search);
            $query->where(function($q) use ($search, $searchLower) {
                // title/tags son JSON: el LIKE normal castea con collation binaria (case-sensitive)
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"])
                  ->orWhere('descripcion_busqueda', 'like', "%{$search}%")
                  ->orWhereRaw('LOWER(tags) LIKE ?', ["%{$searchLower}%"]);
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
        $artistas  = collect();
        if ($request->filled('search')) {
            $s = $request->search;
            $sLower = mb_strtolower($s);
            $panoramas = Panorama::where('activo', true)
                ->whereNotNull('dias_semana')
                ->where('fecha', '>=', now()->toDateString())
                // titulo/ubicacion son JSON: el LIKE normal es case-sensitive
                ->where(fn($q) => $q->whereRaw('LOWER(titulo) LIKE ?', ["%{$sLower}%"])
                                    ->orWhereRaw('LOWER(ubicacion) LIKE ?', ["%{$sLower}%"]))
                ->orderBy('fecha')
                ->limit(6)
                ->get();

            $artistas = Artista::where('activo', true)
                ->where(fn($q) => $q->where('nombre', 'like', "%{$s}%")
                                    ->orWhere('descripcion', 'like', "%{$s}%")
                                    ->orWhere('disciplina', 'like', "%{$s}%"))
                ->limit(6)
                ->get();
        }

        $hoy = Carbon::today();

        // Eventos de agenda de clientes → convertir a instancias Panorama para incluirlos junto a los del admin
        $eventosCliente = ModuloItem::where('modulo', 'eventos')
            ->where('activo', true)
            ->where('fecha', '>=', $hoy)
            ->whereHas('punto', fn($q) => $q->where('activo', true)->where('eliminado', false))
            ->with('punto')
            ->get()
            ->map(fn (ModuloItem $item) => $item->comoPanorama());

        $proximosPanoramas = Panorama::where('activo', true)
                    ->whereNull('dias_semana')

            ->where(function ($q) use ($hoy) {
                $q->whereNull('fecha_fin')->where('fecha', '>=', $hoy)
                  ->orWhere('fecha_fin', '>=', $hoy);
            })
            ->get()
            ->concat($eventosCliente)
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

        $ultimasExperiencias = Experiencia::activas()->latest()->take(10)->get();

        return view('puntos.index_puntos', compact('atractivos', 'categorias', 'puntosMapData', 'panoramas', 'proximosPanoramas', 'ultimoPost', 'ultimosPosts', 'ultimasExperiencias', 'artistas'));

    } catch (\Exception $e) {
        \Log::error('Error en index: ' . $e->getMessage());
        return back()->with('error', 'Ocurrió un error al buscar.');
    }
}




    public function buscar(Request $request)
    {
        $categorias = Categoria::withCount(['puntosInteres' => fn($q) => $q->where('activo', 1)->where('eliminado', false)->sinExcluidos()])
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
            ->sinExcluidos()
            ->where('eliminado', false);

        if ($request->filled('category')) {
            $query->whereHas('categoria', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $searchLower = mb_strtolower($search);
            $query->where(function($q) use ($search, $searchLower) {
                // title/tags son JSON: el LIKE normal castea con collation binaria (case-sensitive)
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"])
                  ->orWhere('descripcion_busqueda', 'like', "%{$search}%")
                  ->orWhereRaw('LOWER(tags) LIKE ?', ["%{$searchLower}%"]);
            });
        }

        $query->latest('updated_at');

        $atractivos = $query
            ->with(['categoria', 'imagenPrincipal'])
            ->paginate(20)
            ->withQueryString();

        $categorias = Categoria::withCount(['puntosInteres' => fn($q) => $q
        ->where('activo', 1)
        ->where('eliminado', false)
        ->sinExcluidos()
        ])
            ->orderByDesc('puntos_interes_count')
            ->get();

        $artistas = collect();
        if ($request->filled('search')) {
            $s = $request->search;
            $artistas = Artista::where('activo', true)
                ->where(fn($q) => $q->where('nombre', 'like', "%{$s}%")
                                    ->orWhere('descripcion', 'like', "%{$s}%")
                                    ->orWhere('disciplina', 'like', "%{$s}%"))
                ->limit(6)
                ->get();
        }

        return view('puntos.explorar', compact('atractivos', 'categorias', 'artistas'));
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
        $puntoRelacionado = null;

        return view('panoramas.show', compact('panorama', 'puntoRelacionado'));
    }

    /**
     * Muestra un evento de la agenda de un cliente (punto_modulo_items, módulo 'eventos')
     * con la misma vista que un panorama subido manualmente. A diferencia de showPanorama(),
     * aquí sí conocemos el punto exacto (viene de la URL), sin necesidad de adivinarlo.
     */
    public function showEvento(string $slug, ModuloItem $item)
    {
        $punto = PuntoInteres::where('slug', $slug)
            ->where('activo', true)
            ->where('eliminado', false)
            ->firstOrFail();

        abort_if($item->punto_interes_id !== $punto->id || $item->modulo !== 'eventos' || !$item->activo, 404);

        $item->setRelation('punto', $punto);
        $panorama = $item->comoPanorama();
        $puntoRelacionado = $punto;

        return view('panoramas.show', compact('panorama', 'puntoRelacionado'));
    }

    public function panoramas(Request $request)
    {
        $limite    = (int) Configuracion::get('panoramas_limite_dias', 15);
        $panoramas = Panorama::activos($limite)->reorder()->orderBy('fecha')->orderBy('hora')->with('imagenes')->get();

        $hoy       = Carbon::today();
        $manana    = Carbon::tomorrow();
        $tope      = $hoy->copy()->addDays($limite);
        $categorias = \App\Models\CategoriaEvento::catalogo();
        $catActiva  = $request->input('categoria');

        // Eventos de agenda de clientes → convertir a instancias Panorama para reutilizar la vista
        $eventosCliente = ModuloItem::where('modulo', 'eventos')
            ->where('activo', true)
            ->whereNotNull('fecha')
            ->whereBetween('fecha', [$hoy, $tope])
            ->whereHas('punto', fn($q) => $q->where('activo', true)->where('eliminado', false))
            ->with('punto')
            ->get()
            ->map(fn (ModuloItem $item) => $item->comoPanorama());

        $panoramas = $panoramas->concat($eventosCliente)
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
            $f      = Carbon::parse($fechaStr)->locale('es');
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
            $fechaLabel = $p->fecha?->locale('es')->translatedFormat('l j \d\e F \d\e Y');
            if ($p->fecha_fin && $p->fecha_fin->gt($p->fecha)) {
                $fechaLabel = $p->fecha->locale('es')->translatedFormat('j \d\e F')
                            . ' al '
                            . $p->fecha_fin->locale('es')->translatedFormat('j \d\e F \d\e Y');
            }
            $info = [
                'titulo'      => $p->titulo,
                'ubicacion'   => $p->ubicacion,
                'descripcion' => $p->getTranslation('descripcion', app()->getLocale(), false),
                'fecha'       => $fechaLabel,
                'hora'        => $p->hora,
                'enlace'      => $p->enlace,
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
            $f      = Carbon::parse($fechaStr)->locale('es');
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
                ->sinExcluidos()
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