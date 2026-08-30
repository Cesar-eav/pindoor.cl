<?php

namespace App\Http\Controllers\Pagos;

use App\Http\Controllers\Controller;
use App\Models\ReservaRuta;
use App\Services\FlowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlowRetornoController extends Controller
{
    public function show(Request $request, FlowService $flow, string $codigo)
    {
        $reserva = ReservaRuta::with('rutaOperador.ruta', 'horario')
            ->where('codigo_reserva', $codigo)
            ->firstOrFail();

        $token = $request->input('token', $reserva->flow_token);

        if ($token && $reserva->estado === 'pendiente') {
            try {
                $estadoFlow = $flow->obtenerEstadoPago($token);
                ReservaRuta::aplicarEstadoFlow($reserva->id, $estadoFlow);
                $reserva->refresh();
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
