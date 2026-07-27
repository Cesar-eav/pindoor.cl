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
                    ->with('lugares')
                    ->firstOrFail();

        return view('blog.show', compact('post'));
    }
}
