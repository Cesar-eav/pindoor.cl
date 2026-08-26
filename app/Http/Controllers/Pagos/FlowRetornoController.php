<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\ReservaRuta;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlowRetornoController extends Controller
{
    private const MAPA_ESTADOS = [
        1 => 'pendiente',
        2 => 'pagada',
        3 => 'rechazada',
        4 => 'anulada',
    ];

    public function show(Request $request, FlowService $flow, string $codigo)
    {
        $reserva = ReservaRuta::with('rutaOperador.ruta', 'horario')
            ->where('codigo_reserva', $codigo)
            ->firstOrFail();

        $token = $request->input('token', $reserva->flow_token);

        if ($token && $reserva->estado === 'pendiente') {
            try {
                $estadoFlow = $flow->obtenerEstadoPago($token);
                $estado = self::MAPA_ESTADOS[$estadoFlow['status'] ?? null] ?? $reserva->estado;
                $yaEstabaPagada = $reserva->estado === 'pagada';

                $reserva->update([
                    'estado'       => $estado,
                    'payload_flow' => $estadoFlow,
                    'pagado_en'    => $estado === 'pagada' ? ($reserva->pagado_en ?? now()) : $reserva->pagado_en,
                ]);

                if ($estado === 'pagada' && !$yaEstabaPagada) {
                    $reserva->notificarPagada();
                }
            } catch (\Throwable $e) {
                // El webhook es la fuente de verdad; si esta consulta de respaldo falla,
                // se muestra igual la página con el último estado conocido en BD, pero
                // queda logueado para poder diagnosticar (ej. Flow caído, API key inválida).
                Log::error('FlowRetornoController: fallo al consultar/actualizar estado', [
                    'codigo' => $codigo,
                    'token'  => $token,
                    'error'  => $e->getMessage(),
                ]);
            }
        }

        return view('pagos.retorno', compact('reserva'));
    }
}
