<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PuntoInteres;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderByDesc('created_at')->get();
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $puntos = PuntoInteres::where('eliminado', false)
            ->with('categoria:id,nombre,icono')
            ->get(['id', 'title', 'sector', 'categoria_id'])
            ->sortBy(fn ($p) => $p->title)->values();
        return view('admin.blog.create', compact('puntos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo_es'      => 'required|string|max:255',
            'titulo_en'      => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255',
            'dynamic_block_title' => 'nullable|string|max:255',
            'resumen_es'     => 'nullable|string|max:600',
            'resumen_en'     => 'nullable|string|max:600',
            'contenido_es'   => 'nullable|string',
            'contenido_en'   => 'nullable|string',
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
        $post->imagenes       = $this->recogerImagenesNuevas($request, []);
        $post->publicado      = $publicado;
        $post->publicado_en   = $publicado ? now() : null;
        $post->setTranslation('titulo',    'es', $data['titulo_es'])
             ->setTranslation('titulo',    'en', $data['titulo_en'] ?? '')
             ->setTranslation('resumen',   'es', $data['resumen_es'] ?? '')
             ->setTranslation('resumen',   'en', $data['resumen_en'] ?? '')
             ->setTranslation('contenido', 'es', $data['contenido_es'] ?? '')
             ->setTranslation('contenido', 'en', $data['contenido_en'] ?? '');
        $post->save();

        $this->sincronizarLugares($request, $post);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.blog.edit', $post)->with('success', 'Post creado correctamente.');
        }
        return redirect()->route('admin.blog.index')
            ->with('success', 'Post creado correctamente.');
    }

    public function edit(Post $blog)
    {
        $blog->load('lugares');
        $puntos = PuntoInteres::where('eliminado', false)
            ->with('categoria:id,nombre,icono')
            ->get(['id', 'title', 'sector', 'categoria_id'])
            ->sortBy(fn ($p) => $p->title)->values();
        return view('admin.blog.edit', ['post' => $blog, 'puntos' => $puntos]);
    }

    public function update(Request $request, Post $blog)
    {
        $data = $request->validate([
            'titulo_es'      => 'required|string|max:255',
            'titulo_en'      => 'nullable|string|max:255',
            'slug'           => 'nullable|string|max:255',
            'dynamic_block_title' => 'nullable|string|max:255',
            'resumen_es'     => 'nullable|string|max:600',
            'resumen_en'     => 'nullable|string|max:600',
            'contenido_es'   => 'nullable|string',
            'contenido_en'   => 'nullable|string',
            'imagen_portada' => 'nullable|image|max:6144',
            'publicado'      => 'nullable|boolean',
        ]);

        $blog->slug                = Post::generarSlug($data['slug'] ?: $data['titulo_es'], $blog->id);
        $blog->dynamic_block_title = $data['dynamic_block_title'] ?? null;

        if ($request->hasFile('imagen_portada')) {
            if ($blog->imagen_portada) Storage::disk('public')->delete($blog->imagen_portada);
            $blog->imagen_portada = ImagenComprimida::guardar($request->file('imagen_portada'), 'blog/portadas');
        }

        // Procesar imágenes existentes
        $existentes   = $blog->imagenes ?? [];
        $eliminar     = $request->input('eliminar_imagen', []);
        $existentesOk = [];

        foreach ($existentes as $idx => $img) {
            $ruta = is_array($img) ? $img['ruta'] : $img;
            if (in_array((string) $idx, array_map('strval', $eliminar))) {
                Storage::disk('public')->delete($ruta);
                continue;
            }
            $pos = $request->integer("posicion_existente_{$idx}") ?: null;
            $existentesOk[] = ['ruta' => $ruta, 'posicion' => $pos];
        }

        $blog->imagenes = $this->recogerImagenesNuevas($request, $existentesOk);

        $publicado = (bool) ($data['publicado'] ?? false);
        $blog->publicado = $publicado;
        if ($publicado && ! $blog->publicado_en) {
            $blog->publicado_en = now();
        } elseif (! $publicado) {
            $blog->publicado_en = null;
        }

        $blog->setTranslation('titulo',    'es', $data['titulo_es'])
             ->setTranslation('titulo',    'en', $data['titulo_en'] ?? '')
             ->setTranslation('resumen',   'es', $data['resumen_es'] ?? '')
             ->setTranslation('resumen',   'en', $data['resumen_en'] ?? '')
             ->setTranslation('contenido', 'es', $data['contenido_es'] ?? '')
             ->setTranslation('contenido', 'en', $data['contenido_en'] ?? '');
        $blog->save();

        $this->sincronizarLugares($request, $blog);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.blog.edit', $blog)->with('success', 'Post actualizado.');
        }
        return redirect()->route('admin.blog.index')
            ->with('success', 'Post actualizado.');
    }

    public function preview(Post $blog)
    {
        $blog->load('lugares');
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

    // Recoge slots imagen_nueva_1…20 con sus posiciones
    private function recogerImagenesNuevas(Request $request, array $base): ?array
    {
        $result = $base;
        for ($s = 1; $s <= 20; $s++) {
            if (count($result) >= 20) break;
            if ($request->hasFile("imagen_nueva_{$s}")) {
                $ruta = ImagenComprimida::guardar($request->file("imagen_nueva_{$s}"), 'blog/galeria');
                $pos  = $request->integer("posicion_nueva_{$s}") ?: null;
                $result[] = ['ruta' => $ruta, 'posicion' => $pos];
            }
        }
        return $result ?: null;
    }
}
