<?php

namespace App\Http\Controllers;

use App\Models\PuntoInteres;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Dashboard principal del Administrador.
     */
    public function index()
    {
        // 1. Estadísticas Generales
        $totalPuntos = PuntoInteres::where('eliminado', false)->count();
        $totalClientes = User::where('type', 'cliente')->count();
        $puntosActivos = PuntoInteres::where('activo', true)->where('eliminado', false)->count();
        
        // 2. Últimos 5 locales registrados
        $ultimosPuntos = PuntoInteres::where('eliminado', false)
                            ->latest()
                            ->take(5)
                            ->get();

        // 3. Usuarios registrados recientemente
        $ultimosClientes = User::where('type', 'cliente')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('admin.stats', compact(
            'totalPuntos', 
            'totalClientes', 
            'puntosActivos', 
            'ultimosPuntos',
            'ultimosClientes'
        ));
    }
}