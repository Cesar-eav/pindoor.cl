<?php

namespace App\Http\Controllers;

use App\Models\Revival;

class RevivalController extends Controller
{
    public function index()
    {
        $revivals = Revival::publicados()->get();
        return view('revival.index', compact('revivals'));
    }

    public function show(string $slug)
    {
        $revival = Revival::where('slug', $slug)
                    ->where('publicado', true)
                    ->firstOrFail();

        return view('revival.show', compact('revival'));
    }
}
