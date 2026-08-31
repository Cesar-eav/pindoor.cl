<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservaGestion;
use App\Models\ReservaRuta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $query = ReservaRuta::with([
            'rutaOperador.ruta', 'rutaOperador.operador', 'rutaOperador.horarios',
            'horario', 'gestiones.admin', 'gestiones.horarioAnterior', 'gestiones.horarioNuevo',
        ])->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->input('contactado') === 'si') {
            $query->where('contactado', true);
        } elseif ($request->input('contactado') === 'no') {
            $query->where('contactado', false);
        }

        if ($request->input('prueba') === 'si') {
            $query->where('es_prueba', true);
        } elseif ($request->input('prueba') !== 'todas') {
            $query->where('es_prueba', false);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_cliente', 'like', "%{$search}%")
                  ->orWhere('email_cliente', 'like', "%{$search}%")
                  ->orWhere('telefono_cliente', 'like', "%{$search}%")
                  ->orWhere('codigo_reserva', 'like', "%{$search}%");
            });
        }

        $reservas = $query->paginate(30)->withQueryString();

        $totalPendientes = ReservaRuta::where('estado', 'pendiente')->where('es_prueba', false)->count();
        $totalSinContactar = ReservaRuta::where('contactado', false)->where('es_prueba', false)->count();

        return view('admin.reservas.index', compact('reservas', 'totalPendientes', 'totalSinContactar'));
    }

    public function update(Request $request, ReservaRuta $reserva)
    {
        $data = $request->validate([
            'notas_admin' => 'nullable|string|max:2000',
        ]);

        $notaNueva = $data['notas_admin'] ?? null;

        if ($notaNueva && $notaNueva !== $reserva->notas_admin) {
            ReservaGestion::create([
                'ticketera_reserva_id' => $reserva->id,
                'tipo'                 => 'nota',
                'motivo'               => $notaNueva,
                'admin_id'             => auth()->id(),
            ]);
        }

        $reserva->update([
            'contactado'  => $request->boolean('contactado'),
            'notas_admin' => $notaNueva,
        ]);

        return back()->with('success', "Reserva {$reserva->codigo_reserva} actualizada.");
    }

    public function reembolsar(Request $request, ReservaRuta $reserva)
    {
        $data = $request->validate(['motivo' => 'nullable|string|max:1000']);

        $ok = ReservaRuta::reembolsar($reserva->id, $data['motivo'] ?? null, auth()->id());

        if (!$ok) {
            return back()->withErrors(['reembolso' => 'Solo se pueden reembolsar reservas que estén pagadas.']);
        }

        return back()->with('success', "Reserva {$reserva->codigo_reserva} marcada como reembolsada. El cupo quedó liberado.");
    }

    public function reagendar(Request $request, ReservaRuta $reserva)
    {
        $data = $request->validate([
            'nuevo_horario_id' => ['required', 'integer', Rule::exists('ruta_operador_horarios', 'id')
                ->where('ruta_operador_turistico_id', $reserva->ruta_operador_turistico_id)],
            'nueva_fecha' => ['required', 'date'],
            'motivo'      => ['nullable', 'string', 'max:1000'],
        ]);

        $resultado = ReservaRuta::reagendar(
            $reserva->id, $data['nuevo_horario_id'], $data['nueva_fecha'], $data['motivo'] ?? null, auth()->id()
        );

        if (!$resultado['ok']) {
            return back()->withErrors(['reagendar' => $resultado['error']]);
        }

        return back()->with('success', "Reserva {$reserva->codigo_reserva} reagendada correctamente.");
    }

    public function checkinShow(ReservaRuta $reserva)
    {
        return view('admin.reservas.checkin', compact('reserva'));
    }

    public function checkinStore(ReservaRuta $reserva)
    {
        if ($reserva->estado !== 'pagada') {
            return back()->withErrors(['checkin' => 'Esta reserva no está pagada, no se puede marcar el ingreso.']);
        }

        if (!$reserva->checkin_at) {
            $reserva->update([
                'checkin_at' => now(),
                'checkin_by' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.reservas.checkin.show', $reserva)->with('success', 'Ingreso registrado.');
    }
}
