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

    // Orden y color fijos de los canales para los gráficos — WhatsApp usa su verde
    // de marca (ya usado en el ícono del botón compartir); el resto toma colores
    // categóricos separados y validados contra confusión por daltonismo.
    public const CANALES = [
        'whatsapp'   => ['emoji' => '🟢', 'label' => 'WhatsApp',   'color' => '#25D366'],
        'nativo'     => ['emoji' => '📤', 'label' => 'Nativo',     'color' => '#2a78d6'],
        'copiar'     => ['emoji' => '🔗', 'label' => 'Copiado',    'color' => '#4a3aa7'],
        'calendario' => ['emoji' => '📅', 'label' => 'Calendario', 'color' => '#eb6834'],
    ];

    // Rangos rápidos disponibles como botones de filtro (además del selector de fechas manual).
    private const RANGOS = ['hoy', 'semana', 'mes', '30dias'];

    public function index(Request $request)
    {
        $rangoActivo = null;

        if ($request->filled('rango') && !$request->filled('desde') && !$request->filled('hasta')) {
            $rangoActivo = in_array($request->rango, self::RANGOS) ? $request->rango : '30dias';

            switch ($rangoActivo) {
                case 'hoy':
                    $desde = now()->startOfDay();
                    $hasta = now()->endOfDay();
                    break;
                case 'semana':
                    $desde = now()->startOfWeek();
                    $hasta = now()->endOfWeek();
                    break;
                case 'mes':
                    $desde = now()->startOfMonth();
                    $hasta = now()->endOfMonth();
                    break;
                default:
                    $desde = now()->subDays(29)->startOfDay();
                    $hasta = now()->endOfDay();
                    break;
            }
        } else {
            $desde = $request->filled('desde')
                ? Carbon::parse($request->desde)->startOfDay()
                : now()->subDays(29)->startOfDay();

            $hasta = $request->filled('hasta')
                ? Carbon::parse($request->hasta)->endOfDay()
                : now()->endOfDay();

            if (!$request->filled('desde') && !$request->filled('hasta')) {
                $rangoActivo = '30dias';
            }
        }

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

        // Mismo dato que $compartidos pero agrupado por categoría (Rutas, Blog, Atractivo...)
        // en vez de por página individual — para ver de un vistazo desde dónde se comparte más.
        $porCategoria = $compartidos
            ->groupBy(fn ($fila) => $fila->seccion['label'])
            ->map(function ($filas, $label) {
                $porCanal = collect();
                foreach ($filas as $fila) {
                    foreach ($fila->por_canal as $canal => $cantidad) {
                        $porCanal[$canal] = ($porCanal[$canal] ?? 0) + $cantidad;
                    }
                }

                return (object) [
                    'emoji'     => $filas->first()->seccion['emoji'],
                    'label'     => $label,
                    'total'     => $filas->sum('total'),
                    'por_canal' => $porCanal,
                ];
            })
            ->sortByDesc('total')
            ->values();
        $maxCategoria = $porCategoria->max('total') ?: 1;

        $porDiaCanalRaw = (clone $base)
            ->selectRaw('DATE(created_at) as fecha, canal, count(*) as total')
            ->groupBy('fecha', 'canal')
            ->get()
            ->groupBy('fecha');

        $dias = collect();
        for ($d = $desde->copy(); $d->lte($hasta); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $porCanalDia = ($porDiaCanalRaw->get($key) ?? collect())->pluck('total', 'canal');
            $dias->push((object) [
                'fecha'     => $key,
                'total'     => (int) $porCanalDia->sum(),
                'por_canal' => $porCanalDia,
            ]);
        }
        $maxDia = $dias->max('total') ?: 1;

        // Totales por canal en todo el rango, para el gráfico de barras horizontales.
        $porCanalTotal = (clone $base)
            ->selectRaw('canal, count(*) as total')
            ->groupBy('canal')
            ->pluck('total', 'canal');
        $maxCanal = $porCanalTotal->max() ?: 1;

        $recientes = (clone $base)
            ->latest('created_at')
            ->limit(50)
            ->get();

        // "Qué se comparte" se puede filtrar por categoría sin afectar el resto de
        // las secciones (que siguen mostrando el total del rango de fechas).
        $paginaCategoria = $request->input('pagina_categoria');
        $compartidosPagina = $paginaCategoria
            ? $compartidos->filter(fn ($fila) => $fila->seccion['label'] === $paginaCategoria)->values()
            : $compartidos;

        return view('admin.compartidos.index', compact(
            'compartidosPagina', 'porCategoria', 'maxCategoria', 'totalGeneral',
            'dias', 'maxDia', 'porCanalTotal', 'maxCanal', 'recientes', 'desde', 'hasta',
            'rangoActivo', 'paginaCategoria'
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
