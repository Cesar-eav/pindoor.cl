<?php

namespace App\Http\Controllers;

use App\Exceptions\FlowPaymentException;
use App\Models\Configuracion;
use App\Models\OperadorTuristico;
use App\Models\ReservaRuta;
use App\Models\Ruta;
use App\Models\RutaOperador;
use App\Models\RutaOperadorHorario;
use App\Notifications\ReservaIniciada;
use App\Services\FlowService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ReservaController extends Controller
{
    private function rutaOperadorTicketing(string $rutaSlug, string $operadorSlug): array
    {
        $ruta = Ruta::where('slug', $rutaSlug)->where('publicado', true)->firstOrFail();
        $operador = OperadorTuristico::where('slug', $operadorSlug)->where('activo', true)->firstOrFail();

        $rutaOperador = RutaOperador::where('ruta_id', $ruta->id)
            ->where('operador_turistico_id', $operador->id)
            ->where('ticketing_activo', true)
            ->firstOrFail();

        return [$ruta, $operador, $rutaOperador];
    }

    public function show(string $rutaSlug, string $operadorSlug)
    {
        [$ruta, $operador, $rutaOperador] = $this->rutaOperadorTicketing($rutaSlug, $operadorSlug);

        $horarios = $rutaOperador->horariosActivos()->get();

        return view('reservas.reservar', compact('ruta', 'operador', 'rutaOperador', 'horarios'));
    }

    public function disponibilidad(Request $request, string $rutaSlug, string $operadorSlug)
    {
        [, , $rutaOperador] = $this->rutaOperadorTicketing($rutaSlug, $operadorSlug);

        $request->validate(['fecha' => 'required|date|after_or_equal:today']);

        $fecha = Carbon::parse($request->input('fecha'))->startOfDay();

        $horarios = $rutaOperador->horariosActivos()->get()
            ->filter(fn (RutaOperadorHorario $h) => $h->aplicaEnFecha($fecha))
            ->map(fn (RutaOperadorHorario $h) => [
                'id'              => $h->id,
                'hora'            => substr($h->hora, 0, 5),
                'cupo_disponible' => $h->cupoDisponible($fecha),
            ])
            ->sortBy('hora')
            ->values();

        return response()->json(['horarios' => $horarios]);
    }

    public function store(Request $request, string $rutaSlug, string $operadorSlug, FlowService $flow)
    {
        [$ruta, $operador, $rutaOperador] = $this->rutaOperadorTicketing($rutaSlug, $operadorSlug);

        $data = $request->validate([
            'fecha_visita'     => 'required|date|after_or_equal:today',
            'horario_id'       => 'required|integer',
            'cantidad_adultos' => 'required|integer|min:1|max:30',
            'cantidad_ninos'   => 'nullable|integer|min:0|max:30',
            'nombre_cliente'   => 'required|string|max:255',
            'email_cliente'    => 'required|email|max:255',
            'telefono_cliente' => 'required|string|max:30',
        ]);

        $ninos = $data['cantidad_ninos'] ?? 0;
        $fecha = Carbon::parse($data['fecha_visita'])->startOfDay();

        $horario = $rutaOperador->horarios()->where('id', $data['horario_id'])->where('activo', true)->first();

        if (!$horario || !$horario->aplicaEnFecha($fecha)) {
            return back()->withInput()->withErrors(['horario_id' => 'Ese horario ya no está disponible para la fecha elegida.']);
        }

        try {
            $reserva = DB::transaction(function () use ($horario, $fecha, $data, $ninos, $rutaOperador, $request) {
                RutaOperadorHorario::where('id', $horario->id)->lockForUpdate()->first();

                $ocupado = ReservaRuta::where('ruta_operador_horario_id', $horario->id)
                    ->whereDate('fecha_visita', $fecha)
                    ->where(function ($q) {
                        $q->where('estado', 'pagada')
                          ->orWhere(function ($q2) {
                              $q2->where('estado', 'pendiente')->where('expira_en', '>', now());
                          });
                    })
                    ->lockForUpdate()
                    ->get()
                    ->sum(fn ($r) => $r->cantidad_adultos + $r->cantidad_ninos);

                $solicitado = $data['cantidad_adultos'] + $ninos;

                if ($ocupado + $solicitado > $horario->cupo_maximo) {
                    throw new \RuntimeException('sin_cupo');
                }

                $precioTotal = $rutaOperador->calcularPrecio($data['cantidad_adultos'], $ninos);

                return ReservaRuta::create([
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
                    'expira_en'                  => now()->addMinutes(30),
                    'ip_cliente'                 => $request->ip(),
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'sin_cupo') {
                return back()->withInput()->withErrors(['horario_id' => 'Ya no queda cupo disponible para ese horario y fecha.']);
            }
            throw $e;
        }

        try {
            Notification::route('mail', Configuracion::emailsNotificacion())
                ->notify(new ReservaIniciada($reserva));
        } catch (\Throwable $e) {
            Log::error('ReservaController: falló el aviso de reserva iniciada', [
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
