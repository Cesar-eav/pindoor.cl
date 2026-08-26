<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\FlowPaymentException;
use App\Http\Controllers\Controller;
use App\Models\ReservaRuta;
use App\Models\RutaOperador;
use App\Notifications\ReservaIniciada;
use App\Models\Configuracion;
use App\Services\FlowService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PagoPruebaController extends Controller
{
    public function show()
    {
        $rutasOperador = RutaOperador::with(['ruta', 'operador', 'horariosActivos'])
            ->where('ticketing_activo', true)
            ->get();

        return view('admin.pagos.prueba', compact('rutasOperador'));
    }

    public function store(Request $request, FlowService $flow)
    {
        $data = $request->validate([
            'ruta_operador_id' => 'required|integer',
            'horario_id'       => 'required|integer',
            'fecha_visita'     => 'required|date|after_or_equal:today',
            'cantidad_adultos' => 'required|integer|min:1|max:30',
            'cantidad_ninos'   => 'nullable|integer|min:0|max:30',
            'nombre_cliente'   => 'required|string|max:255',
            'email_cliente'    => 'required|email|max:255',
            'telefono_cliente' => 'required|string|max:30',
        ]);

        $rutaOperador = RutaOperador::where('ticketing_activo', true)->findOrFail($data['ruta_operador_id']);
        $horario = $rutaOperador->horarios()->where('id', $data['horario_id'])->where('activo', true)->firstOrFail();

        $ninos = $data['cantidad_ninos'] ?? 0;
        $fecha = Carbon::parse($data['fecha_visita'])->startOfDay();
        $precioTotal = $rutaOperador->calcularPrecio($data['cantidad_adultos'], $ninos);

        $reserva = ReservaRuta::create([
            'ruta_operador_turistico_id' => $rutaOperador->id,
            'ruta_operador_horario_id'   => $horario->id,
            'fecha_visita'               => $fecha,
            'nombre_cliente'             => $data['nombre_cliente'],
            'email_cliente'              => $data['email_cliente'],
            'telefono_cliente'           => $data['telefono_cliente'],
            'cantidad_adultos'           => $data['cantidad_adultos'],
            'cantidad_ninos'             => $ninos,
            'precio_unitario_adulto'     => $data['cantidad_adultos'] === 1 && $ninos === 0
                ? $rutaOperador->precio_individual
                : $rutaOperador->precio_grupo_adulto,
            'precio_unitario_nino'       => $rutaOperador->precio_nino,
            'precio_total'               => $precioTotal,
            'estado'                     => 'pendiente',
            'es_prueba'                  => true,
            'expira_en'                  => now()->addMinutes(30),
            'ip_cliente'                 => $request->ip(),
        ]);

        try {
            Notification::route('mail', Configuracion::emailsNotificacion())
                ->notify(new ReservaIniciada($reserva));
        } catch (\Throwable $e) {
            Log::error('PagoPruebaController: falló el aviso de reserva iniciada', [
                'reserva' => $reserva->codigo_reserva,
                'error'   => $e->getMessage(),
            ]);
        }

        try {
            $pago = $flow->crearOrdenPago($reserva);
        } catch (FlowPaymentException $e) {
            $reserva->update(['estado' => 'anulada']);
            return back()->withInput()->withErrors(['flow' => 'No pudimos iniciar el pago con Flow. Intenta nuevamente en unos minutos.']);
        }

        $reserva->update([
            'flow_token' => $pago['token'],
            'flow_order' => $pago['flowOrder'],
        ]);

        return redirect()->away($flow->urlPago($pago['url'], $pago['token']));
    }
}
