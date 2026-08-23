<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Revival;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RevivalController extends Controller
{
    public function index()
    {
        $revivals = Revival::orderByDesc('created_at')->get();
        return view('admin.revival.index', compact('revivals'));
    }

    public function create()
    {
        return view('admin.revival.create');
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $slug = Revival::generarSlug($data['slug'] ?? '' ?: $data['titulo_es']);

        if ($request->hasFile('imagen_portada')) {
            $portada = ImagenComprimida::guardar($request->file('imagen_portada'), 'revival/portadas');
        }

        $publicado = (bool) ($data['publicado'] ?? false);

        $revival = new Revival();
        $revival->slug          = $slug;
        $revival->autor         = $data['autor'] ?? null;
        $revival->video_url     = $data['video_url'] ?? null;
        $revival->imagen_portada = $portada ?? null;
        $revival->publicado     = $publicado;
        $revival->publicado_en  = $publicado ? now() : null;
        $revival->setTranslation('titulo',    'es', $data['titulo_es'])
                 ->setTranslation('titulo',    'en', $data['titulo_en'] ?? '')
                 ->setTranslation('titulo',    'fr', $data['titulo_fr'] ?? '')
                 ->setTranslation('contenido', 'es', $data['contenido_es'])
                 ->setTranslation('contenido', 'en', $data['contenido_en'] ?? '')
                 ->setTranslation('contenido', 'fr', $data['contenido_fr'] ?? '');
        $revival->save();

        $this->guardarGaleria($request, $revival, []);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.revival.edit', $revival)->with('success', 'Re-vival creado correctamente.');
        }
        return redirect()->route('admin.revival.index')
            ->with('success', 'Re-vival creado correctamente.');
    }

    public function edit(Revival $revival)
    {
        $revival->load('imagenes');
        return view('admin.revival.edit', compact('revival'));
    }

    public function update(Request $request, Revival $revival)
    {
        $data = $this->validar($request);

        $revival->slug      = Revival::generarSlug($data['slug'] ?? '' ?: $data['titulo_es'], $revival->id);
        $revival->autor     = $data['autor'] ?? null;
        $revival->video_url = $data['video_url'] ?? null;

        if ($request->hasFile('imagen_portada')) {
            if ($revival->imagen_portada) Storage::disk('public')->delete($revival->imagen_portada);
            $revival->imagen_portada = ImagenComprimida::guardar($request->file('imagen_portada'), 'revival/portadas');
        }

        // Elimina las marcadas; el resto queda disponible para guardarGaleria(),
        // que decide el orden final según el drag-and-drop del form.
        $eliminar = array_map('strval', $request->input('eliminar_imagen', []));
        $existentesPorId = [];
        foreach ($revival->imagenes as $img) {
            if (in_array((string) $img->id, $eliminar)) {
                Storage::disk('public')->delete($img->ruta);
                $img->delete();
                continue;
            }
            $existentesPorId[$img->id] = $img;
        }

        $this->guardarGaleria($request, $revival, $existentesPorId);

        $publicado = (bool) ($data['publicado'] ?? false);
        $revival->publicado = $publicado;
        if ($publicado && ! $revival->publicado_en) {
            $revival->publicado_en = now();
        } elseif (! $publicado) {
            $revival->publicado_en = null;
        }

        $revival->setTranslation('titulo',    'es', $data['titulo_es'])
                 ->setTranslation('titulo',    'en', $data['titulo_en'] ?? '')
                 ->setTranslation('titulo',    'fr', $data['titulo_fr'] ?? '')
                 ->setTranslation('contenido', 'es', $data['contenido_es'])
                 ->setTranslation('contenido', 'en', $data['contenido_en'] ?? '')
                 ->setTranslation('contenido', 'fr', $data['contenido_fr'] ?? '');
        $revival->save();

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.revival.edit', $revival)->with('success', 'Re-vival actualizado.');
        }
        return redirect()->route('admin.revival.index')
            ->with('success', 'Re-vival actualizado.');
    }

    public function preview(Revival $revival)
    {
        $revival->load('imagenes');
        return view('revival.show', compact('revival'));
    }

    public function destroy(Revival $revival)
    {
        $revival->delete();
        return back()->with('success', 'Re-vival eliminado.');
    }

    public function uploadImagen(Request $request)
    {
        $request->validate(['imagen' => 'required|image|max:6144']);
        $path = ImagenComprimida::guardar($request->file('imagen'), 'revival/contenido');
        return response()->json(['url' => asset('storage/' . $path)]);
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'titulo_es'      => 'required|string|max:255',
            'titulo_en'      => 'nullable|string|max:255',
            'titulo_fr'      => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255',
            'autor'          => 'nullable|string|max:255',
            'video_url'      => 'nullable|url|max:500',
            'contenido_es'   => 'required|string',
            'contenido_en'   => 'nullable|string',
            'contenido_fr'   => 'nullable|string',
            'imagen_portada' => 'nullable|image|max:6144',
            'publicado'      => 'nullable|boolean',
        ]);
    }

    /**
     * Sube las imágenes nuevas (slots imagen_nueva_1…20) y fija el orden final
     * de TODA la galería (existentes conservadas + nuevas) según el arrastre
     * hecho en el form — el campo orden[] trae la secuencia final de tokens
     * "existente:{id}" / "nueva:{slot}".
     *
     * @param array<int, \App\Models\RevivalImagen> $existentesPorId Id real => modelo, ya sin las marcadas para eliminar.
     */
    private function guardarGaleria(Request $request, Revival $revival, array $existentesPorId): void
    {
        $nuevasPorSlot = [];
        for ($s = 1; $s <= 20; $s++) {
            if ($request->hasFile("imagen_nueva_{$s}")) {
                $ruta = ImagenComprimida::guardar($request->file("imagen_nueva_{$s}"), 'revival/galeria');
                $pos  = $request->integer("posicion_nueva_{$s}") ?: null;
                $nuevasPorSlot[$s] = ['ruta' => $ruta, 'posicion' => $pos];
            }
        }

        $orden = 0;
        foreach ($request->input('galeria_orden', []) as $token) {
            [$tipo, $key] = array_pad(explode(':', $token, 2), 2, null);
            $key = (int) $key;
            if ($tipo === 'existente' && isset($existentesPorId[$key])) {
                $existentesPorId[$key]->update([
                    'orden'    => $orden++,
                    'posicion' => $request->integer("posicion_existente_{$key}") ?: null,
                ]);
                unset($existentesPorId[$key]);
            } elseif ($tipo === 'nueva' && isset($nuevasPorSlot[$key])) {
                $revival->imagenes()->create([
                    'ruta'     => $nuevasPorSlot[$key]['ruta'],
                    'posicion' => $nuevasPorSlot[$key]['posicion'],
                    'orden'    => $orden++,
                ]);
                unset($nuevasPorSlot[$key]);
            }
        }

        // Respaldo por si orden[] no llegó (JS deshabilitado).
        foreach ($existentesPorId as $key => $img) {
            $img->update(['orden' => $orden++, 'posicion' => $request->integer("posicion_existente_{$key}") ?: null]);
        }
        foreach ($nuevasPorSlot as $img) {
            $revival->imagenes()->create(['ruta' => $img['ruta'], 'posicion' => $img['posicion'], 'orden' => $orden++]);
        }
    }
}
