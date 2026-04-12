<?php

namespace App\Http\Controllers;

use App\Models\PuntoInteres;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PuntoInteresController extends Controller
{

  public function index(Request $request)
{
    try {
        $query = PuntoInteres::query();

        // --- Filtros existentes ---
        if ($request->filled('category')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
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

        // --- NUEVO: Filtro por GPS ---
        if ($request->filled(['lat', 'lng', 'rango'])) {
            $lat = $request->lat;
            $lng = $request->lng;
            $rango = $request->rango;

            // 1. Calculamos la distancia y la filtramos
            $query->whereRaw(
                "ST_Distance_Sphere(POINT(lng, lat), POINT(?, ?)) <= ?", 
                [$lng, $lat, $rango]
            );

            // 2. Agregamos la distancia como un campo para mostrarla en la vista
            $query->selectRaw("*, ST_Distance_Sphere(POINT(lng, lat), POINT(?, ?)) as distancia", [$lng, $lat]);
            
            // 3. Ordenamos por el más cercano (en lugar de Random)
            $query->orderBy('distancia', 'asc');
        } else {
            $query;
        }

        $atractivos = $query
        ->where('activo', 1)
        ->with(['categoria', 'imagenPrincipal'])->latest('id')->paginate(60)->withQueryString();
        $categorias = Categoria::all();

        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('labrujula.partials.atractivos-container', compact('atractivos'))->render();
        }

        // Datos ligeros para el mapa (todos los puntos activos con coordenadas)
        $puntosMapData = PuntoInteres::where('activo', 1)
            ->where('eliminado', false)
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->with(['categoria', 'imagenPrincipal'])
            ->get()
            ->map(fn($p) => [
                'id'       => $p->id,
                'title'    => $p->title,
                'slug'     => $p->slug,
                'lat'      => (float) $p->lat,
                'lng'      => (float) $p->lng,
                'sector'   => $p->sector,
                'categoria'=> $p->categoria?->nombre,
                'imagen'   => $p->imagenPrincipal ? asset('storage/' . $p->imagenPrincipal->ruta) : null,
                'es_cliente' => (bool) $p->es_cliente,
            ]);

        return view('puntos.index_puntos', compact('atractivos', 'categorias', 'puntosMapData'));
        
    } catch (\Exception $e) {
        Log::error('Error en index: ' . $e->getMessage());
        return back()->with('error', 'Ocurrió un error al buscar.');
    }
}




    /**
     * Muestra el listado de locales que pertenecen al cliente logueado.
     */
    public function misPuntos()
    {
        // Solo traemos los puntos del usuario actual que NO estén eliminados
        $puntos = PuntoInteres::where('user_id', Auth::id())
                              ->where('eliminado', false)
                              ->latest()
                              ->get();

        return view('cliente.index', compact('puntos'));
    }

    /**
     * Muestra el formulario para crear un nuevo punto.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('cliente.create', compact('categorias'));
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

    public function panoramas()
    {
        return view('panoramas.index');
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

        return view('puntos.show', compact('punto'));
    }
}