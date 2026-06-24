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
        $post   = Post::where('slug', $slug)
                      ->where('publicado', true)
                      ->firstOrFail();
        $locale = app()->getLocale();

        // Fallback a ES cuando el idioma activo no tiene contenido
        if (!$post->getTranslation('contenido', $locale, false)) {
            app()->setLocale('es');
        }

        return view('blog.show', compact('post'));
    }
}
