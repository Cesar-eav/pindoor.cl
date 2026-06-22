<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
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
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'         => 'required|string|max:255',
            'slug'           => 'nullable|string|max:255',
            'resumen'        => 'nullable|string|max:600',
            'contenido'      => 'nullable|string',
            'imagen_portada' => 'nullable|image|max:6144',
            'publicado'      => 'nullable|boolean',
        ]);

        $data['slug'] = Post::generarSlug($data['slug'] ?: $data['titulo']);

        if ($request->hasFile('imagen_portada')) {
            $data['imagen_portada'] = $request->file('imagen_portada')
                ->store('blog/portadas', 'public');
        }

        $data['imagenes'] = $this->recogerImagenesNuevas($request, []);

        $publicado = (bool) ($data['publicado'] ?? false);
        $data['publicado']    = $publicado;
        $data['publicado_en'] = $publicado ? now() : null;

        Post::create($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Post creado correctamente.');
    }

    public function edit(Post $blog)
    {
        return view('admin.blog.edit', ['post' => $blog]);
    }

    public function update(Request $request, Post $blog)
    {
        $data = $request->validate([
            'titulo'         => 'required|string|max:255',
            'slug'           => 'nullable|string|max:255',
            'resumen'        => 'nullable|string|max:600',
            'contenido'      => 'nullable|string',
            'imagen_portada' => 'nullable|image|max:6144',
            'publicado'      => 'nullable|boolean',
        ]);

        $data['slug'] = Post::generarSlug($data['slug'] ?: $data['titulo'], $blog->id);

        if ($request->hasFile('imagen_portada')) {
            if ($blog->imagen_portada) Storage::disk('public')->delete($blog->imagen_portada);
            $data['imagen_portada'] = $request->file('imagen_portada')
                ->store('blog/portadas', 'public');
        } else {
            unset($data['imagen_portada']);
        }

        // Procesar imágenes existentes: borrar las marcadas, actualizar posiciones
        $existentes  = $blog->imagenes ?? [];
        $eliminar    = $request->input('eliminar_imagen', []);
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

        $data['imagenes'] = $this->recogerImagenesNuevas($request, $existentesOk);

        $publicado = (bool) ($data['publicado'] ?? false);
        $data['publicado'] = $publicado;
        if ($publicado && ! $blog->publicado_en) {
            $data['publicado_en'] = now();
        } elseif (! $publicado) {
            $data['publicado_en'] = null;
        }

        $blog->update($data);

        return redirect()->route('admin.blog.index')
            ->with('success', 'Post actualizado.');
    }

    public function destroy(Post $blog)
    {
        $blog->delete();
        return back()->with('success', 'Post eliminado.');
    }

    public function uploadImagen(Request $request)
    {
        $request->validate(['imagen' => 'required|image|max:6144']);
        $path = $request->file('imagen')->store('blog/contenido', 'public');
        return response()->json(['url' => asset('storage/' . $path)]);
    }

    // Recoge slots imagen_nueva_1…20 con sus posiciones
    private function recogerImagenesNuevas(Request $request, array $base): ?array
    {
        $result = $base;
        for ($s = 1; $s <= 20; $s++) {
            if (count($result) >= 20) break;
            if ($request->hasFile("imagen_nueva_{$s}")) {
                $ruta = $request->file("imagen_nueva_{$s}")->store('blog/galeria', 'public');
                $pos  = $request->integer("posicion_nueva_{$s}") ?: null;
                $result[] = ['ruta' => $ruta, 'posicion' => $pos];
            }
        }
        return $result ?: null;
    }
}
