<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compartido;

class CompartidosController extends Controller
{
    public function index()
    {
        $compartidos = Compartido::selectRaw('url, canal, count(*) as total')
            ->groupBy('url', 'canal')
            ->get()
            ->groupBy('url')
            ->map(fn ($filas, $url) => (object) [
                'url'       => $url,
                'total'     => $filas->sum('total'),
                'por_canal' => $filas->pluck('total', 'canal'),
            ])
            ->sortByDesc('total')
            ->values();

        $totalGeneral = $compartidos->sum('total');

        return view('admin.compartidos.index', compact('compartidos', 'totalGeneral'));
    }
}
