<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\ModuloItem;
use App\Models\Panorama;
use App\Models\PanoramaImagen;
use App\Services\ImagenComprimida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PanoramaController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $categoria = $request->input('categoria');
        $admin = $request->input('admin');

        Log::info($admin);

        $query = Panorama::orderBy('fecha')->orderBy('hora')->orderBy('id');

        if ($search) {
            $searchLower = mb_strtolower($search);
            // titulo/ubicacion son JSON: el LIKE normal castea con collation binaria (case-sensitive)
            $query->where(fn($q) => $q->whereRaw('LOWER(titulo) LIKE ?', ["%{$searchLower}%"])
                                      ->orWhereRaw('LOWER(ubicacion) LIKE ?', ["%{$searchLower}%"]));
        }
        if ($categoria === 'gratuito') {
            $query->where('es_gratuito', true);
        } elseif ($categoria) {
            $query->where('categoria', $categoria);
        }

        if ($admin === 'cesar') {
            $query->where('created_by', 'Admin');
        } elseif ($admin === 'daniela') {
            $query->where('created_by', 'Daniela Cabrera');
        }

        $panoramas = $query->get()
            ->concat($this->eventosDeClientes($search, $categoria))
            ->sortBy(fn ($p) => ($p->fecha?->format('Y-m-d') ?? '9999-99-99') . ($p->hora ?? '99:99'))
            ->values();

        $limiteDias = (int) Configuracion::get('panoramas_limite_dias', 15);
        $categorias = \App\Models\CategoriaEvento::catalogo();
        $hoy        = Carbon::today();
        $semana     = $hoy->copy()->addDays(7);

        $hoyStr    = $hoy->toDateString();
        $semanaStr = $semana->toDateString();

        $grupos = [
            'hoy'      => $panoramas->filter(fn($p) => $p->fecha && (

                              $p->fecha->toDateString() === $hoyStr ||
                              ($p->fecha->toDateString() < $hoyStr && $p->fecha_fin &&
                              
                               $p->fecha_fin->toDateString() >= $hoyStr)
                          )),

            'semana'   => $panoramas->filter(fn($p) => $p->fecha &&
                              $p->fecha->toDateString() > $hoyStr &&
                              $p->fecha->toDateString() <= $semanaStr),
            'proximos' => $panoramas->filter(fn($p) => $p->fecha &&
                              $p->fecha->toDateString() > $semanaStr),
            'pasados'  => $panoramas->filter(fn($p) => $p->fecha &&
                              $p->fecha->toDateString() < $hoyStr &&
                              (!$p->fecha_fin || $p->fecha_fin->toDateString() < $hoyStr)),
            'sin_fecha'=> $panoramas->filter(fn($p) => !$p->fecha),
        ];

        return view('admin.panoramas.index', compact(
            'panoramas', 'limiteDias', 'grupos', 'hoy', 'categorias', 'search', 'categoria', 'admin'
        ));
    }

    /** Vista de pantalla completa con los próximos panoramas en scroll automático, pensada para grabar un reel. */
    public function reel()
    {
        $limiteDias = (int) Configuracion::get('panoramas_limite_dias', 15);
        $hoy   = Carbon::today();
        $hasta = $hoy->copy()->addDays($limiteDias);

        $panoramas = Panorama::activos($limiteDias)->orderBy('fecha')->orderBy('hora')->get()
            ->concat($this->eventosDeClientes(null, null))
            ->filter(fn ($p) => $p->activo && $p->fecha && $p->fecha->lte($hasta)
                && (($p->fecha_fin === null && $p->fecha->gte($hoy)) || ($p->fecha_fin && $p->fecha_fin->gte($hoy))))
            ->sortBy(fn ($p) => $p->fecha->format('Y-m-d') . ($p->hora ?? '99:99'))
            ->values();

        return view('admin.panoramas-reel', compact('panoramas'));
    }

    /**
     * Agenda de eventos que cada cliente gestiona desde su propio panel (punto_modulo_items,
     * modulo 'eventos'). No viven en la tabla panoramas: se arman acá como Panorama de solo
     * lectura, igual que en PuntoInteresController::panoramas() para la vista pública.
     */
    private function eventosDeClientes(?string $search, ?string $categoria)
    {
        $eventos = ModuloItem::where('modulo', 'eventos')
            ->whereNotNull('fecha')
            ->whereHas('punto', fn($q) => $q->where('eliminado', false))
            ->with('punto')
            ->get()
            ->map(function (ModuloItem $item) {
                $fake = new Panorama();
                $fake->fill([
                    'titulo'      => $item->campo('titulo', ''),
                    'ubicacion'   => $item->punto?->title,
                    'fecha'       => $item->fecha->format('Y-m-d'),
                    'fecha_fin'   => null,
                    'dias_semana' => null,
                    'hora'        => $item->campo('hora'),
                    'categoria'   => $item->campo('tipo', 'otros'),
                    'es_gratuito' => (float) ($item->campo('precio', 1)) === 0.0,
                    'enlace'      => $item->campo('url_entradas'),
                    'imagen'      => $item->imagen,
                    'activo'      => $item->activo,
                    'fuente'      => 'cliente',
                ]);
                $fake->setAttribute('id', 'mi_' . $item->id);
                $fake->exists = false;

                return $fake;
            });

        if ($search) {
            $searchLower = mb_strtolower($search);
            $eventos = $eventos->filter(fn ($p) => str_contains(mb_strtolower($p->titulo), $searchLower)
                || str_contains(mb_strtolower((string) $p->ubicacion), $searchLower));
        }

        if ($categoria === 'gratuito') {
            $eventos = $eventos->where('es_gratuito', true);
        } elseif ($categoria) {
            $eventos = $eventos->where('categoria', $categoria);
        }

        return $eventos->values();
    }

    public function configuracion(Request $request)
    {
        $request->validate(['panoramas_limite_dias' => 'required|integer|min:1|max:365']);
        Configuracion::set('panoramas_limite_dias', $request->input('panoramas_limite_dias'));
        return back()->with('success', 'Configuración guardada.');
    }

    public function ubicaciones(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = Panorama::whereNotNull('ubicacion')
            ->where('ubicacion', '!=', '')
            ->when($q, fn($query) => $query->whereRaw('LOWER(ubicacion) LIKE ?', ['%' . mb_strtolower($q) . '%']))
            ->distinct()
            ->orderBy('ubicacion')
            ->limit(8)
            ->pluck('ubicacion');

        return response()->json($results);
    }

    public function create()
    {
        return view('admin.panoramas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo_es'      => 'required|string|max:255',
            'titulo_en'      => 'nullable|string|max:255',
            'titulo_fr'      => 'nullable|string|max:255',
            'descripcion_es' => 'nullable|string|max:2000',
            'descripcion_en' => 'nullable|string|max:2000',
            'descripcion_fr' => 'nullable|string|max:2000',
            'ubicacion_es'   => 'nullable|string|max:255',
            'ubicacion_en'   => 'nullable|string|max:255',
            'ubicacion_fr'   => 'nullable|string|max:255',
            'fecha'          => 'required|date',
            'fecha_fin'      => 'nullable|date|after_or_equal:fecha',
            'dias_semana'    => 'nullable|array',
            'dias_semana.*'  => 'integer|between:1,7',
            'semana_del_mes' => 'nullable|integer|in:1,2,3,4,-1',
            'hora'           => 'nullable|string|max:100',
            'enlace'         => 'nullable|url|max:500',
            'categoria'      => 'nullable|string|in:' . implode(',', \App\Models\CategoriaEvento::slugs()),
            'orden'          => 'nullable|integer|min:0',
            'activo'         => 'nullable|boolean',
            'es_gratuito'    => 'nullable|boolean',
            'imagen'         => 'nullable|image|max:4096',
            'imagenes.*'     => 'nullable|image|max:4096',
        ]);

        $data['activo']      = $request->boolean('activo', true);
        $data['es_gratuito'] = $request->boolean('es_gratuito', false);
        $data['orden']       = $request->input('orden', 0);
        $data['fuente']      = 'manual';
        $data['dias_semana'] = $request->has('dias_semana')
            ? array_map('intval', $request->input('dias_semana'))
            : null;
        $data['semana_del_mes'] = ($request->has('dias_semana') && $request->filled('semana_del_mes'))
            ? (int) $request->input('semana_del_mes')
            : null;

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            Log::info('[Panorama][store] portada', [
                'original' => $file->getClientOriginalName(),
                'size'     => $file->getSize(),
                'mime'     => $file->getMimeType(),
                'disk_root'=> Storage::disk('public')->path(''),
            ]);
            try {
                $data['imagen'] = ImagenComprimida::guardar($file, 'panoramas');
                Log::info('[Panorama][store] portada guardada', ['ruta' => $data['imagen']]);
            } catch (\Throwable $e) {
                Log::error('[Panorama][store] error al guardar portada', ['error' => $e->getMessage()]);
                return back()->withInput()->withErrors(['imagen' => 'Error al guardar la imagen: ' . $e->getMessage()]);
            }
        }

        $data['titulo'] = [
            'es' => $data['titulo_es'],
            'en' => $data['titulo_en'] ?? '',
            'fr' => $data['titulo_fr'] ?? '',
        ];
        $data['ubicacion'] = [
            'es' => $data['ubicacion_es'] ?? '',
            'en' => $data['ubicacion_en'] ?? '',
            'fr' => $data['ubicacion_fr'] ?? '',
        ];
        $data['descripcion'] = [
            'es' => $data['descripcion_es'] ?? '',
            'en' => $data['descripcion_en'] ?? '',
            'fr' => $data['descripcion_fr'] ?? '',
        ];
        unset(
            $data['titulo_es'], $data['titulo_en'], $data['titulo_fr'],
            $data['ubicacion_es'], $data['ubicacion_en'], $data['ubicacion_fr'],
            $data['descripcion_es'], $data['descripcion_en'], $data['descripcion_fr'],
        );

        $panorama = Panorama::create($data);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $i => $file) {
                Log::info('[Panorama][store] adicional #' . $i, [
                    'original' => $file->getClientOriginalName(),
                    'size'     => $file->getSize(),
                    'mime'     => $file->getMimeType(),
                ]);
                try {
                    $ruta = ImagenComprimida::guardar($file, 'panoramas');
                    $panorama->imagenes()->create(['ruta' => $ruta, 'orden' => $i]);
                    Log::info('[Panorama][store] adicional guardada', ['ruta' => $ruta]);
                } catch (\Throwable $e) {
                    Log::error('[Panorama][store] error adicional #' . $i, ['error' => $e->getMessage()]);
                }
            }
        }

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama creado correctamente.');
    }

    public function edit(Panorama $panorama)
    {
        $panorama->load('imagenes');
        return view('admin.panoramas.edit', compact('panorama'));
    }

    public function update(Request $request, Panorama $panorama)
    {
        $data = $request->validate([
            'titulo_es'      => 'required|string|max:255',
            'titulo_en'      => 'nullable|string|max:255',
            'titulo_fr'      => 'nullable|string|max:255',
            'descripcion_es' => 'nullable|string|max:2000',
            'descripcion_en' => 'nullable|string|max:2000',
            'descripcion_fr' => 'nullable|string|max:2000',
            'ubicacion_es'   => 'nullable|string|max:255',
            'ubicacion_en'   => 'nullable|string|max:255',
            'ubicacion_fr'   => 'nullable|string|max:255',
            'fecha'          => 'required|date',
            'fecha_fin'      => 'nullable|date|after_or_equal:fecha',
            'dias_semana'    => 'nullable|array',
            'dias_semana.*'  => 'integer|between:1,7',
            'semana_del_mes' => 'nullable|integer|in:1,2,3,4,-1',
            'hora'           => 'nullable|string|max:100',
            'enlace'         => 'nullable|url|max:500',
            'categoria'      => 'nullable|string|in:' . implode(',', \App\Models\CategoriaEvento::slugs()),
            'orden'          => 'nullable|integer|min:0',
            'activo'         => 'nullable|boolean',
            'es_gratuito'    => 'nullable|boolean',
            'imagen'         => 'nullable|image|max:4096',
            'imagenes.*'     => 'nullable|image|max:4096',
        ]);

        $data['activo']      = $request->boolean('activo', true);
        $data['es_gratuito'] = $request->boolean('es_gratuito', false);
        $data['orden']       = $request->input('orden', 0);
        $data['dias_semana'] = $request->has('dias_semana')
            ? array_map('intval', $request->input('dias_semana'))
            : null;
        $data['semana_del_mes'] = ($request->has('dias_semana') && $request->filled('semana_del_mes'))
            ? (int) $request->input('semana_del_mes')
            : null;

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            Log::info('[Panorama][update] portada', [
                'panorama_id' => $panorama->id,
                'original'    => $file->getClientOriginalName(),
                'size'        => $file->getSize(),
                'mime'        => $file->getMimeType(),
                'disk_root'   => Storage::disk('public')->path(''),
            ]);
            try {
                if ($panorama->imagen) {
                    Storage::disk('public')->delete($panorama->imagen);
                }
                $data['imagen'] = ImagenComprimida::guardar($file, 'panoramas');
                Log::info('[Panorama][update] portada guardada', ['ruta' => $data['imagen']]);
            } catch (\Throwable $e) {
                Log::error('[Panorama][update] error al guardar portada', ['error' => $e->getMessage()]);
                return back()->withInput()->withErrors(['imagen' => 'Error al guardar la imagen: ' . $e->getMessage()]);
            }
        }

        unset(
            $data['titulo_es'], $data['titulo_en'], $data['titulo_fr'],
            $data['ubicacion_es'], $data['ubicacion_en'], $data['ubicacion_fr'],
            $data['descripcion_es'], $data['descripcion_en'], $data['descripcion_fr'],
        );

        $panorama->fill($data);
        $panorama->setTranslation('titulo', 'es', $request->input('titulo_es'))
                  ->setTranslation('titulo', 'en', $request->input('titulo_en') ?? '')
                  ->setTranslation('titulo', 'fr', $request->input('titulo_fr') ?? '')
                  ->setTranslation('ubicacion', 'es', $request->input('ubicacion_es') ?? '')
                  ->setTranslation('ubicacion', 'en', $request->input('ubicacion_en') ?? '')
                  ->setTranslation('ubicacion', 'fr', $request->input('ubicacion_fr') ?? '')
                  ->setTranslation('descripcion', 'es', $request->input('descripcion_es') ?? '')
                  ->setTranslation('descripcion', 'en', $request->input('descripcion_en') ?? '')
                  ->setTranslation('descripcion', 'fr', $request->input('descripcion_fr') ?? '');
        $panorama->save();

        if ($request->hasFile('imagenes')) {
            $offset = $panorama->imagenes()->max('orden') + 1;
            foreach ($request->file('imagenes') as $i => $file) {
                Log::info('[Panorama][update] adicional #' . $i, [
                    'panorama_id' => $panorama->id,
                    'original'    => $file->getClientOriginalName(),
                    'size'        => $file->getSize(),
                    'mime'        => $file->getMimeType(),
                ]);
                try {
                    $ruta = ImagenComprimida::guardar($file, 'panoramas');
                    $panorama->imagenes()->create(['ruta' => $ruta, 'orden' => $offset + $i]);
                    Log::info('[Panorama][update] adicional guardada', ['ruta' => $ruta]);
                } catch (\Throwable $e) {
                    Log::error('[Panorama][update] error adicional #' . $i, ['error' => $e->getMessage()]);
                }
            }
        }

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama actualizado correctamente.');
    }

    public function destroy(Panorama $panorama)
    {
        if ($panorama->imagen) {
            Storage::disk('public')->delete($panorama->imagen);
        }

        foreach ($panorama->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->ruta);
        }

        $panorama->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama eliminado.');
    }

    public function destroyImagen(PanoramaImagen $imagen)
    {
        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Imagen eliminada.');
    }

    public function toggle(Panorama $panorama)
    {
        $panorama->update(['activo' => ! $panorama->activo]);
        return back()->with('success', 'Visibilidad actualizada.');
    }

    public function actualizarCategoria(Request $request, Panorama $panorama)
    {
        $request->validate(['categoria' => 'required|in:' . implode(',', \App\Models\CategoriaEvento::slugs())]);
        $panorama->update(['categoria' => $request->categoria]);
        return response()->json(['ok' => true]);
    }
}
