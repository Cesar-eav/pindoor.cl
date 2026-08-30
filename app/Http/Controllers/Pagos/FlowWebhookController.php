<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\ReservaRuta;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlowWebhookController extends Controller
{
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

        ReservaRuta::aplicarEstadoFlow($reserva->id, $estadoFlow);

        return response('', 200);
    }
}
