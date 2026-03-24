<?php

namespace App\Http\Controllers;

use App\Models\PuntoInteres;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function usuarios()
    {
        $usuarios = User::latest()->paginate(10);
        return view('admin.usuarios', compact('usuarios'));
    }

    public function createPunto()
    {

    // Traemos los puntos creados por el admin (puntos públicos)
        $puntos = PuntoInteres::where('user_id', auth()->id())
                            ->where('eliminado', false)
                            ->latest()
                            ->get();

        return view('admin.puntos-create', compact('puntos'));

    }

    public function togglePunto(PuntoInteres $punto)
    {
        $punto->update(['activo' => !$punto->activo]);
        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function storePunto(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'category' => 'required',
            'sector' => 'required',
            'description' => 'required',
        ]);

        PuntoInteres::create([
            'user_id' => auth()->id(), // El admin es el dueño de estos puntos
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . rand(100, 999),
            'category' => $request->category,
            'sector' => $request->sector,
            'description' => $request->description,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'activo' => true,
            'eliminado' => false,
            // Podríamos añadir una marca de "Lugar Público" si quisieras más adelante
        ]);

        return redirect()->route('admin.stats')->with('success', 'Punto de interés general creado.');
    }
}