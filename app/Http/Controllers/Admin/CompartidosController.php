<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Compartido;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompartidosController extends Controller
{
    // Secciones del sitio identificadas por el primer segmento de la URL.
    private const SECCIONES = [
        'lugar'        => ['emoji' => '📍', 'label' => 'Atractivo'],
        'panoramas'    => ['emoji' => '📅', 'label' => 'Panoramas'],
        'recomienda'   => ['emoji' => '🍽️', 'label' => 'Recomienda'],
        'blog'         => ['emoji' => '📰', 'label' => 'Blog'],
        'rutas'        => ['emoji' => '🧭', 'label' => 'Rutas'],
        'experiencias' => ['emoji' => '✨', 'label' => 'Experiencias'],
        'artista'      => ['emoji' => '🎨', 'label' => 'Artista'],
        'operador'     => ['emoji' => '🧳', 'label' => 'Operador'],
    ];

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
                'seccion'   => $this->describirUrl($url),
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

    // Convierte una URL larga en {emoji, label, detalle} para mostrarla corta y agrupada
    // por sección (ej: /panoramas/trio-certero-...-330 → 📅 Panoramas · "trio certero...").
    private function describirUrl(string $url): array
    {
        $segmentos = array_values(array_filter(explode('/', parse_url($url, PHP_URL_PATH) ?? '')));

        if (empty($segmentos)) {
            return ['emoji' => '🏠', 'label' => 'Inicio', 'detalle' => null];
        }

        // /panoramas/revival/{slug} es una subsección propia, no un panorama normal
        if ($segmentos[0] === 'panoramas' && ($segmentos[1] ?? null) === 'revival') {
            return ['emoji' => '🎞️', 'label' => 'Re-vival', 'detalle' => $this->slugLegible($segmentos[2] ?? null)];
        }

        $info = self::SECCIONES[$segmentos[0]] ?? ['emoji' => '🔗', 'label' => ucfirst($segmentos[0])];

        return ['emoji' => $info['emoji'], 'label' => $info['label'], 'detalle' => $this->slugLegible($segmentos[1] ?? null)];
    }

    private function slugLegible(?string $slug): ?string
    {
        if (!$slug) return null;
        return Str::limit(str_replace('-', ' ', $slug), 45);
    }
}
