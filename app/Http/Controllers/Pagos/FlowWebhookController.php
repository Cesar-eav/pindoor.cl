<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\ReservaRuta;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlowWebhookController extends Controller
{
    // Estados de Flow: 1 pendiente, 2 pagada, 3 rechazada, 4 anulada.
    private const MAPA_ESTADOS = [
        1 => 'pendiente',
        2 => 'pagada',
        3 => 'rechazada',
        4 => 'anulada',
    ];

    public function confirmar(Request $request, FlowService $flow)
    {
        $token = $request->input('token');

        if (!$token) {
            return response('', 400);
        }

        $reserva = ReservaRuta::where('flow_token', $token)->first();

        if (!$reserva) {
            Log::warning('FlowWebhookController: token sin reserva asociada', ['token' => $token]);
            return response('', 200);
        }

        try {
            $estadoFlow = $flow->obtenerEstadoPago($token);
        } catch (\Throwable $e) {
            Log::error('FlowWebhookController: error consultando estado', ['token' => $token, 'error' => $e->getMessage()]);
            return response('', 500);
        }

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

        return response('', 200);
    }
}
