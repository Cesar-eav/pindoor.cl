<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PuntoInteres;
use App\Models\Recomendacion;
use App\Models\RecomendacionImagen;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecomendacionController extends Controller
{
    private const CAMPOS_TRADUCIBLES = [
        'titulo_es', 'titulo_en', 'titulo_fr',
        'resumen_es', 'resumen_en', 'resumen_fr',
        'contenido_es', 'contenido_en', 'contenido_fr',
    ];

    public function index(Request $request)
    {
        $search = $request->input('search');
        $plan   = $request->input('plan');

        $query = Recomendacion::orderBy('orden')->orderByDesc('created_at');

        if ($search) {
            $query->where(fn ($q) => $q->where('titulo', 'like', "%{$search}%")
                                      ->orWhere('negocio', 'like', "%{$search}%")
                                      ->orWhere('direccion', 'like', "%{$search}%"));
        }
        if ($plan) {
            $query->where('plan', $plan);
        }

        $recomendaciones = $query->get();
        $planes          = Recomendacion::PLANES;

        return view('admin.recomendaciones.index', compact('recomendaciones', 'planes', 'search', 'plan'));
    }

    public function create()
    {
        $puntos = PuntoInteres::where('eliminado', false)->get(['id', 'title', 'sector'])
            ->sortBy(fn ($p) => $p->title)->values();
        return view('admin.recomendaciones.create', compact('puntos'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data = $this->procesarBooleanos($request, $data);
        $data['slug'] = Recomendacion::generarSlug($data['titulo_es']);
        $data['publicado_en'] = $data['publicado'] ? now() : null;

        if ($request->hasFile('imagen_portada')) {
            $data['imagen_portada'] = ImagenComprimida::guardar($request->file('imagen_portada'), 'recomienda');
        }

        if ($request->hasFile('video_local')) {
            $data['video_local'] = $request->file('video_local')->store('recomienda/videos', 'public');
        }

        $traducciones = collect($data)->only(self::CAMPOS_TRADUCIBLES);
        $data = collect($data)->except(self::CAMPOS_TRADUCIBLES)->all();

        $recomendacion = Recomendacion::create($data);
        $this->aplicarTraducciones($recomendacion, $traducciones);
        $recomendacion->save();

        $this->guardarImagenesNuevas($request, $recomendacion, 0);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.recomendaciones.edit', $recomendacion)->with('success', 'Recomendación creada correctamente.');
        }
        return redirect()->route('admin.recomendaciones.index')->with('success', 'Recomendación creada correctamente.');
    }

    public function edit(Recomendacion $recomendacion)
    {
        $recomendacion->load('imagenes');
        $puntos = PuntoInteres::where('eliminado', false)->get(['id', 'title', 'sector'])
            ->sortBy(fn ($p) => $p->title)->values();
        return view('admin.recomendaciones.edit', compact('recomendacion', 'puntos'));
    }

    public function update(Request $request, Recomendacion $recomendacion)
    {
        $data = $this->validar($request);
        $data = $this->procesarBooleanos($request, $data);
        $data['slug'] = Recomendacion::generarSlug($data['titulo_es'], $recomendacion->id);

        if ($data['publicado'] && !$recomendacion->publicado_en) {
            $data['publicado_en'] = now();
        } elseif (!$data['publicado']) {
            $data['publicado_en'] = null;
        }

        if ($request->hasFile('imagen_portada')) {
            if ($recomendacion->imagen_portada) Storage::disk('public')->delete($recomendacion->imagen_portada);
            $data['imagen_portada'] = ImagenComprimida::guardar($request->file('imagen_portada'), 'recomienda');
        }

        if ($request->hasFile('video_local')) {
            if ($recomendacion->video_local) Storage::disk('public')->delete($recomendacion->video_local);
            $data['video_local'] = $request->file('video_local')->store('recomienda/videos', 'public');
        } elseif ($request->boolean('eliminar_video_local') && $recomendacion->video_local) {
            Storage::disk('public')->delete($recomendacion->video_local);
            $data['video_local'] = null;
        }

        $traducciones = collect($data)->only(self::CAMPOS_TRADUCIBLES);
        $data = collect($data)->except(self::CAMPOS_TRADUCIBLES)->all();

        $recomendacion->update($data);
        $this->aplicarTraducciones($recomendacion, $traducciones);
        $recomendacion->save();

        $eliminar = $request->input('eliminar_imagen', []);
        foreach ($recomendacion->imagenes as $img) {
            if (in_array((string) $img->id, array_map('strval', $eliminar))) {
                Storage::disk('public')->delete($img->ruta);
                $img->delete();
                continue;
            }
            $img->update(['posicion' => $request->integer("posicion_existente_{$img->id}") ?: null]);
        }

        $offset = (int) $recomendacion->imagenes()->max('orden');
        $this->guardarImagenesNuevas($request, $recomendacion, $offset);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.recomendaciones.edit', $recomendacion)->with('success', 'Recomendación actualizada correctamente.');
        }
        return redirect()->route('admin.recomendaciones.index')->with('success', 'Recomendación actualizada correctamente.');
    }

    public function preview(Recomendacion $recomendacion)
    {
        $recomendacion->load('imagenes');
        return view('recomienda.show', compact('recomendacion'));
    }

    public function destroy(Recomendacion $recomendacion)
    {
        $recomendacion->delete();
        return redirect()->route('admin.recomendaciones.index')->with('success', 'Recomendación eliminada.');
    }

    public function destroyImagen(RecomendacionImagen $imagen)
    {
        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Imagen eliminada.');
    }

    public function toggle(Recomendacion $recomendacion)
    {
        $recomendacion->update(['activo' => !$recomendacion->activo]);
        return back()->with('success', 'Visibilidad actualizada.');
    }

    public function togglePublicado(Recomendacion $recomendacion)
    {
        $publicado = !$recomendacion->publicado;
        $recomendacion->update([
            'publicado'    => $publicado,
            'publicado_en' => $publicado ? ($recomendacion->publicado_en ?? now()) : null,
        ]);
        return back()->with('success', $publicado ? 'Publicada.' : 'Pasada a borrador.');
    }

    public function uploadImagen(Request $request)
    {
        $request->validate(['imagen' => 'required|image|max:6144']);
        $path = ImagenComprimida::guardar($request->file('imagen'), 'recomienda/contenido');
        return response()->json(['url' => asset('storage/' . $path)]);
    }

    // Recoge slots imagen_nueva_1…20 con su posición (párrafo tras el que se inserta)
    private function guardarImagenesNuevas(Request $request, Recomendacion $recomendacion, int $ordenBase): void
    {
        for ($s = 1; $s <= 20; $s++) {
            if (!$request->hasFile("imagen_nueva_{$s}")) continue;
            $ruta = ImagenComprimida::guardar($request->file("imagen_nueva_{$s}"), 'recomienda');
            $recomendacion->imagenes()->create([
                'ruta'     => $ruta,
                'orden'    => $ordenBase + $s,
                'posicion' => $request->integer("posicion_nueva_{$s}") ?: null,
            ]);
        }
    }

    private function aplicarTraducciones(Recomendacion $recomendacion, \Illuminate\Support\Collection $t): void
    {
        $recomendacion->setTranslation('titulo',    'es', $t['titulo_es'])
                      ->setTranslation('titulo',    'en', $t['titulo_en'] ?? '')
                      ->setTranslation('titulo',    'fr', $t['titulo_fr'] ?? '')
                      ->setTranslation('resumen',   'es', $t['resumen_es'] ?? '')
                      ->setTranslation('resumen',   'en', $t['resumen_en'] ?? '')
                      ->setTranslation('resumen',   'fr', $t['resumen_fr'] ?? '')
                      ->setTranslation('contenido', 'es', $t['contenido_es'] ?? '')
                      ->setTranslation('contenido', 'en', $t['contenido_en'] ?? '')
                      ->setTranslation('contenido', 'fr', $t['contenido_fr'] ?? '');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'titulo_es'         => 'required|string|max:255',
            'titulo_en'         => 'nullable|string|max:255',
            'titulo_fr'         => 'nullable|string|max:255',
            'resumen_es'        => 'nullable|string|max:600',
            'resumen_en'        => 'nullable|string|max:600',
            'resumen_fr'        => 'nullable|string|max:600',
            'contenido_es'      => 'nullable|string',
            'contenido_en'      => 'nullable|string',
            'contenido_fr'      => 'nullable|string',
            'punto_interes_id'  => 'nullable|exists:puntosinteres,id',
            'plan'              => 'required|string|in:' . implode(',', array_keys(Recomendacion::PLANES)),
            'negocio'           => 'required|string|max:255',
            'rubro'             => 'nullable|string|max:255',
            'direccion'         => 'nullable|string|max:255',
            'video_url'         => 'nullable|url|max:500',
            'video_youtube'     => 'nullable|url|max:500',
            'video_local'       => 'nullable|mimes:mp4,mov,webm|max:50000',
            'video_en_cabecera' => 'nullable|boolean',
            'whatsapp'          => 'nullable|string|max:30',
            'enlace'            => 'nullable|url|max:500',
            'autor'             => 'nullable|string|max:255',
            'destacado_portada' => 'nullable|boolean',
            'destacado_hasta'   => 'nullable|date',
            'publicado'         => 'nullable|boolean',
            'orden'             => 'nullable|integer|min:0',
            'activo'            => 'nullable|boolean',
            'imagen_portada'    => 'nullable|image|max:25600',
        ]);
    }

    private function procesarBooleanos(Request $request, array $data): array
    {
        $data['activo']            = $request->boolean('activo', true);
        $data['destacado_portada'] = $request->boolean('destacado_portada', false);
        $data['video_en_cabecera'] = $request->boolean('video_en_cabecera', false);
        $data['orden']             = $request->input('orden', 0);

        $publicado = $request->boolean('publicado', false);
        $data['publicado'] = $publicado;

        return $data;
    }
}
