<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\PuntoInteres;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $aprobacionActiva = (bool) Configuracion::get('aprobacion_negocios_activa', false);

        $idsExcluidos = PuntoInteres::idsExcluidos();

        $puntosData = PuntoInteres::where('eliminado', false)
            ->with('categoria:id,nombre,icono')
            ->get(['id', 'title', 'sector', 'categoria_id'])
            ->map(fn($p) => [
                'id'        => $p->id,
                'title'     => (string) $p->title,
                'sector'    => $p->sector,
                'categoria' => $p->categoria?->nombre,
                'emoji'     => $p->categoria?->icono,
            ])
            ->values();

        $categoriasDisponibles = $puntosData->pluck('categoria')->filter()->unique()->sort()->values();

        $ascensores = PuntoInteres::whereHas('categoria', fn($q) => $q->where('slug', 'ascensores'))
            ->where('eliminado', false)
            ->orderBy('title')
            ->get(['id', 'title', 'sector', 'fuera_de_servicio', 'fuera_de_servicio_motivo']);

        return view('admin.configuracion.index', compact(
            'aprobacionActiva', 'idsExcluidos', 'puntosData', 'categoriasDisponibles', 'ascensores'
        ));
    }

    public function actualizar(Request $request)
    {
        Configuracion::set('aprobacion_negocios_activa', $request->boolean('aprobacion_negocios_activa') ? '1' : '0');

        return back()->with('success', 'Configuración guardada.');
    }

    public function actualizarExcluidos(Request $request)
    {
        $ids = collect($request->input('puntos', []))
            ->filter()
            ->map(fn($id) => (int) $id);

        Configuracion::set('puntos_demo_excluidos', $ids->implode(','));

        return back()->with('success', 'Puntos de ejemplo actualizados.');
    }

    public function actualizarAscensores(Request $request)
    {
        $estados = $request->input('fuera_de_servicio', []);
        $motivos = $request->input('motivo', []);

        $ascensores = PuntoInteres::whereHas('categoria', fn($q) => $q->where('slug', 'ascensores'))
            ->where('eliminado', false)
            ->get(['id']);

        foreach ($ascensores as $ascensor) {
            $fueraDeServicio = (bool) ($estados[$ascensor->id] ?? false);

            $ascensor->update([
                'fuera_de_servicio'        => $fueraDeServicio,
                'fuera_de_servicio_motivo' => $fueraDeServicio ? trim((string) ($motivos[$ascensor->id] ?? '')) : null,
            ]);
        }

        return back()->with('success', 'Estado de los ascensores actualizado.');
    }
}
