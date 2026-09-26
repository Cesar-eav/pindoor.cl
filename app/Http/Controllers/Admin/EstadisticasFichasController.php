<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Compartido;
use App\Models\EventoFicha;
use App\Models\PuntoInteres;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstadisticasFichasController extends Controller
{
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

        $eventosBase = EventoFicha::whereBetween('created_at', [$desde, $hasta]);
        $compartidosBase = Compartido::whereBetween('created_at', [$desde, $hasta]);

        // --- KPIs del rango ---
        $porTipo = (clone $eventosBase)
            ->selectRaw('tipo, count(*) as total')
            ->groupBy('tipo')
            ->pluck('total', 'tipo');

        $totales = [
            'visitas'     => (int) ($porTipo['visita'] ?? 0),
            'como_llegar' => (int) ($porTipo['como_llegar'] ?? 0),
            'whatsapp'    => (int) ($porTipo['whatsapp'] ?? 0),
            'compartidos' => (clone $compartidosBase)->count(),
        ];

        // Proxy direccional de contacto, no un funnel estricto por sesión/visitante.
        $tasaContacto = $totales['visitas'] > 0
            ? round((($totales['como_llegar'] + $totales['whatsapp']) / $totales['visitas']) * 100, 1)
            : 0;

        // --- Serie diaria (zero-filled) para el gráfico de tendencia ---
        $eventosPorDiaTipo = (clone $eventosBase)
            ->selectRaw('DATE(created_at) as fecha, tipo, count(*) as total')
            ->groupBy('fecha', 'tipo')
            ->get()
            ->groupBy('fecha');

        $compartidosPorDia = (clone $compartidosBase)
            ->selectRaw('DATE(created_at) as fecha, count(*) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $dias = collect();
        for ($d = $desde->copy(); $d->lte($hasta); $d->addDay()) {
            $key     = $d->format('Y-m-d');
            $porTipoDia = ($eventosPorDiaTipo->get($key) ?? collect())->pluck('total', 'tipo');

            $dias->push([
                'fecha'       => $d->format('d/m'),
                'visitas'     => (int) ($porTipoDia['visita'] ?? 0),
                'como_llegar' => (int) ($porTipoDia['como_llegar'] ?? 0),
                'whatsapp'    => (int) ($porTipoDia['whatsapp'] ?? 0),
                'compartidos' => (int) ($compartidosPorDia[$key] ?? 0),
            ]);
        }

        // --- Top fichas más visitadas (clientes y atractivos por igual) ---
        $visitasPorPunto = (clone $eventosBase)
            ->where('tipo', 'visita')
            ->selectRaw('punto_interes_id, count(*) as total')
            ->groupBy('punto_interes_id')
            ->orderByDesc('total')
            ->limit(30)
            ->pluck('total', 'punto_interes_id');

        $puntosTop = PuntoInteres::whereIn('id', $visitasPorPunto->keys())
            ->publico()
            ->get()
            ->keyBy('id');

        $topFichas = $visitasPorPunto
            ->map(fn ($total, $id) => $puntosTop->has($id) ? (object) [
                'punto' => $puntosTop[$id],
                'total' => $total,
            ] : null)
            ->filter()
            ->values()
            ->take(10);

        // --- Desglose por categoría ---
        $visitasPorPuntoTodas = (clone $eventosBase)
            ->where('tipo', 'visita')
            ->selectRaw('punto_interes_id, count(*) as total')
            ->groupBy('punto_interes_id')
            ->pluck('total', 'punto_interes_id');

        $puntosConCategoria = PuntoInteres::whereIn('id', $visitasPorPuntoTodas->keys())
            ->publico()
            ->select('id', 'categoria_id')
            ->get();

        $totalPorCategoriaId = $puntosConCategoria
            ->groupBy('categoria_id')
            ->map(fn ($grupo) => $grupo->sum(fn ($p) => $visitasPorPuntoTodas[$p->id] ?? 0));

        $categorias = Categoria::whereIn('id', $totalPorCategoriaId->keys())->get()->keyBy('id');

        $porCategoria = $totalPorCategoriaId
            ->map(fn ($total, $id) => (object) [
                'categoria' => $categorias->get($id),
                'total'     => $total,
            ])
            ->filter(fn ($fila) => $fila->categoria !== null)
            ->sortByDesc('total')
            ->values();

        // --- Compartidos por canal (mismos colores que la página de Compartidos) ---
        $porCanal = (clone $compartidosBase)
            ->selectRaw('canal, count(*) as total')
            ->groupBy('canal')
            ->pluck('total', 'canal');
        $maxCanal = $porCanal->max() ?: 1;

        return view('admin.estadisticas-fichas.index', compact(
            'desde', 'hasta', 'rangoActivo',
            'totales', 'tasaContacto',
            'dias', 'topFichas', 'porCategoria', 'porCanal', 'maxCanal'
        ));
    }
}
