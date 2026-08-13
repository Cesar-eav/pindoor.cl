<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compartido;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompartidosController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->filled('desde')
            ? Carbon::parse($request->desde)->startOfDay()
            : now()->subDays(29)->startOfDay();

        $hasta = $request->filled('hasta')
            ? Carbon::parse($request->hasta)->endOfDay()
            : now()->endOfDay();

        $base = Compartido::whereBetween('created_at', [$desde, $hasta]);

        $compartidos = (clone $base)
            ->selectRaw('url, canal, count(*) as total')
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

        $porDiaRaw = (clone $base)
            ->selectRaw('DATE(created_at) as fecha, count(*) as total')
            ->groupBy('fecha')
            ->get()
            ->keyBy('fecha');

        $dias = collect();
        for ($d = $desde->copy(); $d->lte($hasta); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $dias->push((object) [
                'fecha' => $key,
                'total' => (int) ($porDiaRaw->get($key)->total ?? 0),
            ]);
        }
        $maxDia = $dias->max('total') ?: 1;

        $recientes = (clone $base)
            ->latest('created_at')
            ->limit(50)
            ->get();

        return view('admin.compartidos.index', compact(
            'compartidos', 'totalGeneral', 'dias', 'maxDia', 'recientes', 'desde', 'hasta'
        ));
    }
}
