<?php

namespace App\Http\Controllers;

use App\Models\ModuloItem;
use App\Models\PuntoInteres;
use Illuminate\Support\Facades\Auth;

class ClienteEventosController extends Controller
{
    /** Página de agenda cultural del negocio — el CRUD lo maneja el componente Livewire. */
    public function index(PuntoInteres $punto)
    {
        abort_if((int) $punto->user_id !== Auth::id(), 403);

        $modulos = $punto->modulos_habilitados ?? [];

        return view('cliente.eventos', compact('punto', 'modulos'));
    }

    /** Vista de pantalla completa con los próximos eventos en scroll automático, pensada para grabar un reel. */
    public function reel(PuntoInteres $punto)
    {
        abort_if((int) $punto->user_id !== Auth::id(), 403);

        $eventos = ModuloItem::where('punto_interes_id', $punto->id)
            ->where('modulo', 'eventos')
            ->whereDate('fecha', '>=', today())
            ->orderByDesc('destacado')
            ->orderBy('fecha')
            ->get();

        return view('cliente.eventos-reel', compact('punto', 'eventos'));
    }
}
