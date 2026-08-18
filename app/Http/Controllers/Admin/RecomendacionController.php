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
        $data['slug'] = Recomendacion::generarSlug($data['titulo']);
        $data['publicado_en'] = $data['publicado'] ? now() : null;

        if ($request->hasFile('imagen_portada')) {
            $data['imagen_portada'] = ImagenComprimida::guardar($request->file('imagen_portada'), 'recomienda');
        }

        $recomendacion = Recomendacion::create($data);

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
        $data['slug'] = Recomendacion::generarSlug($data['titulo'], $recomendacion->id);

        if ($data['publicado'] && !$recomendacion->publicado_en) {
            $data['publicado_en'] = now();
        } elseif (!$data['publicado']) {
            $data['publicado_en'] = null;
        }

        if ($request->hasFile('imagen_portada')) {
            if ($recomendacion->imagen_portada) Storage::disk('public')->delete($recomendacion->imagen_portada);
            $data['imagen_portada'] = ImagenComprimida::guardar($request->file('imagen_portada'), 'recomienda');
        }

        $recomendacion->update($data);

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

    private function validar(Request $request): array
    {
        return $request->validate([
            'titulo'            => 'required|string|max:255',
            'resumen'           => 'nullable|string|max:600',
            'punto_interes_id'  => 'nullable|exists:puntosinteres,id',
            'plan'              => 'required|string|in:' . implode(',', array_keys(Recomendacion::PLANES)),
            'negocio'           => 'required|string|max:255',
            'rubro'             => 'nullable|string|max:255',
            'direccion'         => 'nullable|string|max:255',
            'contenido'         => 'nullable|string',
            'video_url'         => 'nullable|url|max:500',
            'video_youtube'     => 'nullable|url|max:500',
            'video_en_cabecera' => 'nullable|boolean',
            'whatsapp'          => 'nullable|string|max:30',
            'enlace'            => 'nullable|url|max:500',
            'autor'             => 'nullable|string|max:255',
            'destacado_portada' => 'nullable|boolean',
            'destacado_hasta'   => 'nullable|date',
            'publicado'         => 'nullable|boolean',
            'orden'             => 'nullable|integer|min:0',
            'activo'            => 'nullable|boolean',
            'imagen_portada'    => 'nullable|image|max:4096',
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
