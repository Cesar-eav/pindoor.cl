<?php

namespace App\Http\Controllers;

use App\Models\PuntoInteres;
use App\Models\Categoria;
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
        $categorias = Categoria::orderBy('nombre')->get();
        $puntos = PuntoInteres::where('user_id', auth()->id())
                            ->where('eliminado', false)
                            ->latest()
                            ->get();

        return view('admin.puntos-create', compact('puntos', 'categorias'));

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
            'categoria_id' => 'required|exists:categorias,id', // Validamos que el ID exista            'sector' => 'required',
            'description' => 'required',
            'photos'       => 'required|array',       // Debe venir al menos una foto
            'photos.*'     => 'image|mimes:jpeg,png,jpg|max:2048', // Reglas por cada imagen
        ]);

        $punto = PuntoInteres::create([
            'user_id' => auth()->id(), // El admin es el dueño de estos puntos
            'categoria_id' => $request->categoria_id,            
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . rand(100, 999),
            'sector' => $request->sector,
            'description' => $request->description,
            'tags'         => $request->tags ? array_map('trim', explode(',', $request->tags)) : [],
            'autor'        => $request->autor,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'activo' => true,
            'eliminado' => false,
            // Podríamos añadir una marca de "Lugar Público" si quisieras más adelante
        ]);

        // 3. Procesar y guardar las imágenes
    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $index => $file) {
            
            // Guardar el archivo en storage/app/public/puntos
            $path = $file->store('puntos', 'public');

            // Leer la metadata que envió Vue (is_main)
            $esPrincipal = $request->input("metadata.{$index}.is_main") == 1;

            // Crear el registro en la tabla imagenes_punto
            $punto->imagenes()->create([
                'ruta'         => $path,
                'es_principal' => $esPrincipal,
                'orden'        => $index, // Guardamos el orden del drag & drop
            ]);
        }
    }

        return response()->json([
            'success' => true,
            'url'     => route('admin.puntos.create'),
        ]);
    }
}