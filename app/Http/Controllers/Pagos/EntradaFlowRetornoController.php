<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\EventoEntrada;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EntradaFlowRetornoController extends Controller
{
    public function show(Request $request, FlowService $flow, string $codigo)
    {
        $entrada = EventoEntrada::with('moduloItem', 'punto')
            ->where('codigo_entrada', $codigo)
            ->firstOrFail();

        $token = $request->input('token', $entrada->flow_token);

        if ($token && $entrada->estado === 'pendiente') {
            try {
                $estadoFlow = $flow->obtenerEstadoPago($token);
                EventoEntrada::aplicarEstadoFlow($entrada->id, $estadoFlow);
                $entrada->refresh();
            } catch (\Throwable $e) {
                Log::error('EntradaFlowRetornoController: fallo al consultar/actualizar estado', [
                    'codigo' => $codigo,
                    'token'  => $token,
                    'error'  => $e->getMessage(),
                ]);
            }
        }

        return view('pagos.entrada-retorno', compact('entrada'));
    }
}
