<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\EventoEntrada;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EntradaFlowWebhookController extends Controller
{
    public function confirmar(Request $request, FlowService $flow)
    {
        $token = $request->input('token');

        if (!$token) {
            return response('', 400);
        }

        $entrada = EventoEntrada::where('flow_token', $token)->first();

        if (!$entrada) {
            Log::warning('EntradaFlowWebhookController: token sin entrada asociada', ['token' => $token]);
            return response('', 200);
        }

        try {
            $estadoFlow = $flow->obtenerEstadoPago($token);
        } catch (\Throwable $e) {
            Log::error('EntradaFlowWebhookController: error consultando estado', ['token' => $token, 'error' => $e->getMessage()]);
            return response('', 500);
        }

        EventoEntrada::aplicarEstadoFlow($entrada->id, $estadoFlow);

        return response('', 200);
    }
}
