<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::publicados()->get();
        return view('blog.index', compact('posts'));
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
                    ->where('publicado', true)
                    ->with(['lugares', 'imagenes', 'rutas' => fn ($q) => $q->where('publicado', true)->withCount('puntos')])
                    ->firstOrFail();

        return view('blog.show', compact('post'));
    }
}
