<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PuntoInteres;
use App\Models\Ruta;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('orden_home')->orderByDesc('created_at')->get();
        return view('admin.blog.index', compact('posts'));
    }

    public function reordenar(Request $request)
    {
        collect($request->input('orden', []))->values()->each(
            fn ($id, $i) => Post::where('id', $id)->update(['orden_home' => $i])
        );

        return back()->with('success', 'Orden actualizado.');
    }

    public function create()
    {
        $puntos = PuntoInteres::where('eliminado', false)
            ->with('categoria:id,nombre,icono')
            ->get(['id', 'title', 'sector', 'categoria_id'])
            ->sortBy(fn ($p) => $p->title)->values();
        $rutas = Ruta::all()->sortBy(fn ($r) => $r->titulo)->values();
        return view('admin.blog.create', compact('puntos', 'rutas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo_es'      => 'required|string|max:255',
            'titulo_en'      => 'nullable|string|max:255',
            'titulo_fr'      => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255',
            'dynamic_block_title' => 'nullable|string|max:255',
            'resumen_es'     => 'nullable|string|max:600',
            'resumen_en'     => 'nullable|string|max:600',
            'resumen_fr'     => 'nullable|string|max:600',
            'contenido_es'   => 'nullable|string',
            'contenido_en'   => 'nullable|string',
            'contenido_fr'   => 'nullable|string',
            'imagen_portada' => 'nullable|image|max:6144',
            'publicado'      => 'nullable|boolean',
        ]);

        $slug = Post::generarSlug($data['slug'] ?: $data['titulo_es']);

        if ($request->hasFile('imagen_portada')) {
            $portada = ImagenComprimida::guardar($request->file('imagen_portada'), 'blog/portadas');
        }

        $publicado = (bool) ($data['publicado'] ?? false);

        $post = new Post();
        $post->slug                 = $slug;
        $post->dynamic_block_title  = $data['dynamic_block_title'] ?? null;
        $post->imagen_portada = $portada ?? null;
        $post->publicado      = $publicado;
        $post->publicado_en   = $publicado ? now() : null;
        $post->setTranslation('titulo',    'es', $data['titulo_es'])
             ->setTranslation('titulo',    'en', $data['titulo_en'] ?? '')
             ->setTranslation('titulo',    'fr', $data['titulo_fr'] ?? '')
             ->setTranslation('resumen',   'es', $data['resumen_es'] ?? '')
             ->setTranslation('resumen',   'en', $data['resumen_en'] ?? '')
             ->setTranslation('resumen',   'fr', $data['resumen_fr'] ?? '')
             ->setTranslation('contenido', 'es', $data['contenido_es'] ?? '')
             ->setTranslation('contenido', 'en', $data['contenido_en'] ?? '')
             ->setTranslation('contenido', 'fr', $data['contenido_fr'] ?? '');
        $post->save();

        $this->guardarGaleria($request, $post, []);
        $this->sincronizarLugares($request, $post);
        $this->sincronizarRutas($request, $post);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.blog.edit', $post)->with('success', 'Post creado correctamente.');
        }
        return redirect()->route('admin.blog.index')
            ->with('success', 'Post creado correctamente.');
    }

    public function edit(Post $blog)
    {
        $blog->load('lugares', 'rutas', 'imagenes');
        $puntos = PuntoInteres::where('eliminado', false)
            ->with('categoria:id,nombre,icono')
            ->get(['id', 'title', 'sector', 'categoria_id'])
            ->sortBy(fn ($p) => $p->title)->values();
        $rutas = Ruta::all()->sortBy(fn ($r) => $r->titulo)->values();
        return view('admin.blog.edit', ['post' => $blog, 'puntos' => $puntos, 'rutas' => $rutas]);
    }

    public function update(Request $request, Post $blog)
    {
        $data = $request->validate([
            'titulo_es'      => 'required|string|max:255',
            'titulo_en'      => 'nullable|string|max:255',
            'titulo_fr'      => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255',
            'dynamic_block_title' => 'nullable|string|max:255',
            'resumen_es'     => 'nullable|string|max:600',
            'resumen_en'     => 'nullable|string|max:600',
            'resumen_fr'     => 'nullable|string|max:600',
            'contenido_es'   => 'nullable|string',
            'contenido_en'   => 'nullable|string',
            'contenido_fr'   => 'nullable|string',
            'imagen_portada' => 'nullable|image|max:6144',
            'publicado'      => 'nullable|boolean',
        ]);

        $blog->slug                = Post::generarSlug($data['slug'] ?: $data['titulo_es'], $blog->id);
        $blog->dynamic_block_title = $data['dynamic_block_title'] ?? null;

        if ($request->hasFile('imagen_portada')) {
            if ($blog->imagen_portada) Storage::disk('public')->delete($blog->imagen_portada);
            $blog->imagen_portada = ImagenComprimida::guardar($request->file('imagen_portada'), 'blog/portadas');
        }

        // Elimina las marcadas; el resto queda disponible para guardarGaleria(),
        // que decide el orden final según el drag-and-drop del form.
        $eliminar = array_map('strval', $request->input('eliminar_imagen', []));
        $existentesPorId = [];
        foreach ($blog->imagenes as $img) {
            if (in_array((string) $img->id, $eliminar)) {
                Storage::disk('public')->delete($img->ruta);
                $img->delete();
                continue;
            }
            $existentesPorId[$img->id] = $img;
        }

        $this->guardarGaleria($request, $blog, $existentesPorId);

        $publicado = (bool) ($data['publicado'] ?? false);
        $blog->publicado = $publicado;
        if ($publicado && ! $blog->publicado_en) {
            $blog->publicado_en = now();
        } elseif (! $publicado) {
            $blog->publicado_en = null;
        }

        $blog->setTranslation('titulo',    'es', $data['titulo_es'])
             ->setTranslation('titulo',    'en', $data['titulo_en'] ?? '')
             ->setTranslation('titulo',    'fr', $data['titulo_fr'] ?? '')
             ->setTranslation('resumen',   'es', $data['resumen_es'] ?? '')
             ->setTranslation('resumen',   'en', $data['resumen_en'] ?? '')
             ->setTranslation('resumen',   'fr', $data['resumen_fr'] ?? '')
             ->setTranslation('contenido', 'es', $data['contenido_es'] ?? '')
             ->setTranslation('contenido', 'en', $data['contenido_en'] ?? '')
             ->setTranslation('contenido', 'fr', $data['contenido_fr'] ?? '');
        $blog->save();

        $this->sincronizarLugares($request, $blog);
        $this->sincronizarRutas($request, $blog);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.blog.edit', $blog)->with('success', 'Post actualizado.');
        }
        return redirect()->route('admin.blog.index')
            ->with('success', 'Post actualizado.');
    }

    public function preview(Post $blog)
    {
        $blog->load('lugares', 'rutas', 'imagenes');
        return view('blog.show', ['post' => $blog]);
    }

    public function destroy(Post $blog)
    {
        $blog->delete();
        return back()->with('success', 'Post eliminado.');
    }

    public function uploadImagen(Request $request)
    {
        $request->validate(['imagen' => 'required|image|max:6144']);
        $path = ImagenComprimida::guardar($request->file('imagen'), 'blog/contenido');
        return response()->json(['url' => asset('storage/' . $path)]);
    }

    // Guarda qué lugares (PuntoInteres) se mencionan en el post, en el orden elegido
    private function sincronizarLugares(Request $request, Post $post): void
    {
        $ids = $request->input('lugares', []);
        $post->lugares()->sync(
            collect($ids)->filter()->values()
                ->mapWithKeys(fn ($id, $i) => [(int) $id => ['orden' => $i]])
                ->all()
        );
    }

    private function sincronizarRutas(Request $request, Post $post): void
    {
        $ids = $request->input('rutas', []);
        $post->rutas()->sync(collect($ids)->filter()->values()->all());
    }

    /**
     * Sube las imágenes nuevas (slots imagen_nueva_1…20) y fija el orden final
     * de TODA la galería (existentes conservadas + nuevas) según el arrastre
     * hecho en el form — el campo orden[] trae la secuencia final de tokens
     * "existente:{id}" / "nueva:{slot}".
     *
     * @param array<int, \App\Models\PostImagen> $existentesPorId Id real => modelo, ya sin las marcadas para eliminar.
     */
    private function guardarGaleria(Request $request, Post $post, array $existentesPorId): void
    {
        $nuevasPorSlot = [];
        for ($s = 1; $s <= 20; $s++) {
            if ($request->hasFile("imagen_nueva_{$s}")) {
                $ruta = ImagenComprimida::guardar($request->file("imagen_nueva_{$s}"), 'blog/galeria');
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
                $post->imagenes()->create([
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
            $post->imagenes()->create(['ruta' => $img['ruta'], 'posicion' => $img['posicion'], 'orden' => $orden++]);
        }
    }
}
