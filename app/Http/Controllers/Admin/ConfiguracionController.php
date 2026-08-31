<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\PuntoInteres;
use App\Services\FlowService;
use App\Services\HomeSeccionesService;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    const ETIQUETAS_SECCIONES = [
        'panoramas'  => 'Panoramas',
        'destacados' => 'Destacados',
        'recomienda' => 'Pindoor Recomienda',
        'guias'      => 'Guías',
        'rutas'      => 'Rutas Pindoor',
    ];

    public function index()
    {
        $aprobacionActiva = (bool) Configuracion::get('aprobacion_negocios_activa', false);
        $homePorCategoria = (int) Configuracion::get('home_puntos_por_categoria', \App\Http\Controllers\PuntoInteresController::PUNTOS_POR_CATEGORIA_DEFAULT);
        $ordenSecciones = HomeSeccionesService::ordenSecciones();
        $etiquetasSecciones = self::ETIQUETAS_SECCIONES;

        $notificacionesEmails = implode(', ', Configuracion::emailsNotificacion());
        $notificacionesTelegramChatId = Configuracion::telegramChatId();

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

        $flowModo = (new FlowService())->modo();
        $flowSandboxApiKey = Configuracion::get('flow_sandbox_api_key');
        $flowProduccionApiKey = Configuracion::get('flow_produccion_api_key');
        $flowSandboxSecretConfigurado = filled(Configuracion::get('flow_sandbox_secret_key'));
        $flowProduccionSecretConfigurado = filled(Configuracion::get('flow_produccion_secret_key'));

        return view('admin.configuracion.index', compact(
            'aprobacionActiva', 'homePorCategoria', 'idsExcluidos', 'puntosData', 'categoriasDisponibles', 'ascensores',
            'ordenSecciones', 'etiquetasSecciones', 'notificacionesEmails', 'notificacionesTelegramChatId',
            'flowModo', 'flowSandboxApiKey', 'flowProduccionApiKey', 'flowSandboxSecretConfigurado', 'flowProduccionSecretConfigurado'
        ));
    }

    public function actualizar(Request $request)
    {
        Configuracion::set('aprobacion_negocios_activa', $request->boolean('aprobacion_negocios_activa') ? '1' : '0');

        return back()->with('success', 'Configuración guardada.');
    }

    public function actualizarOrdenSecciones(Request $request)
    {
        $data = $request->validate(['orden' => 'required|string']);

        Configuracion::set('home_orden_secciones', $data['orden']);

        return back()->with('success', 'Orden de las secciones guardado.');
    }

    public function actualizarHomePorCategoria(Request $request)
    {
        $data = $request->validate([
            'home_puntos_por_categoria' => 'required|integer|min:1|max:100',
        ]);

        Configuracion::set('home_puntos_por_categoria', (string) $data['home_puntos_por_categoria']);

        return back()->with('success', 'Configuración guardada.');
    }

    public function actualizarNotificaciones(Request $request)
    {
        $data = $request->validate([
            'notificaciones_emails'            => 'required|string',
            'notificaciones_telegram_chat_id'  => 'nullable|string|max:50',
        ]);

        $emails = collect(explode(',', str_replace(["\n", "\r"], ',', $data['notificaciones_emails'])))
            ->map(fn ($email) => trim($email))
            ->filter();

        if ($emails->isEmpty() || $emails->contains(fn ($email) => ! filter_var($email, FILTER_VALIDATE_EMAIL))) {
            return back()->withErrors(['notificaciones_emails' => 'Ingresa uno o más correos válidos, separados por coma.'])->withInput();
        }

        Configuracion::set('notificaciones_emails', $emails->implode(','));
        Configuracion::set('notificaciones_telegram_chat_id', trim((string) ($data['notificaciones_telegram_chat_id'] ?? '')));

        return back()->with('success', 'Notificaciones guardadas.');
    }

    public function actualizarExcluidos(Request $request)
    {
        $ids = collect($request->input('puntos', []))
            ->filter()
            ->map(fn($id) => (int) $id);

        Configuracion::set('puntos_demo_excluidos', $ids->implode(','));

        return back()->with('success', 'Puntos de ejemplo actualizados.');
    }

    public function actualizarFlow(Request $request)
    {
        $data = $request->validate([
            'flow_modo'                  => 'required|in:sandbox,produccion',
            'flow_sandbox_api_key'       => 'nullable|string|max:100',
            'flow_sandbox_secret_key'    => 'nullable|string|max:100',
            'flow_produccion_api_key'    => 'nullable|string|max:100',
            'flow_produccion_secret_key' => 'nullable|string|max:100',
        ]);

        Configuracion::set('flow_modo', $data['flow_modo']);

        foreach (['flow_sandbox_api_key', 'flow_sandbox_secret_key', 'flow_produccion_api_key', 'flow_produccion_secret_key'] as $campo) {
            if (filled($data[$campo] ?? null)) {
                Configuracion::set($campo, trim($data[$campo]));
            }
        }

        return back()->with('success', 'Credenciales de Flow guardadas.');
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
