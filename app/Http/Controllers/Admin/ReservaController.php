<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReservaRuta;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    public function index(Request $request)
    {
        $query = ReservaRuta::with(['rutaOperador.ruta', 'rutaOperador.operador', 'horario'])
            ->latest();

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

        $reserva->update([
            'contactado'  => $request->boolean('contactado'),
            'notas_admin' => $data['notas_admin'] ?? null,
        ]);

        return back()->with('success', "Reserva {$reserva->codigo_reserva} actualizada.");
    }
}
