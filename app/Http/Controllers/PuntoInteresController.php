<?php

namespace App\Http\Controllers;

use App\Models\PuntoInteres;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PuntoInteresController extends Controller
{
    /**
     * Muestra el listado de locales que pertenecen al cliente logueado.
     */
    public function misPuntos()
    {
        // Solo traemos los puntos del usuario actual que NO estén eliminados
        $puntos = PuntoInteres::where('user_id', Auth::id())
                              ->where('eliminado', false)
                              ->latest()
                              ->get();

        return view('cliente.index', compact('puntos'));
    }

    /**
     * Muestra el formulario para crear un nuevo punto.
     */
    public function create()
    {
        return view('cliente.create');
    }

    /**
     * Guarda el nuevo punto de interés en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validar la entrada
        $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string',
            'sector'      => 'required|string',
            'description' => 'required|string',
            'lat'         => 'nullable|numeric',
            'lng'         => 'nullable|numeric',
        ]);

        // 2. Crear el registro
        PuntoInteres::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . rand(100, 999), // Evita duplicados de slug
            'category'    => $request->category,
            'sector'      => $request->sector,
            'description' => $request->description,
            'direccion'   => $request->direccion,
            'autor'       => $request->autor, // <--- GUARDAR
            'tags'        => $request->tags,
            'lat'         => $request->lat,
            'lng'         => $request->lng,
            'video_url'   => $request->video_url,
            'horario'     => $request->horario,
            'activo'      => true,    // Por defecto nace visible
            'eliminado'   => false,   // Por defecto no está borrado
        ]);

        // Procesar Imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                // Guardar archivo físicamente
                $path = $file->store('puntos', 'public');

                // Guardar en base de datos
                $punto->imagenes()->create([
                    'ruta' => $path,
                    'es_principal' => $request->ordered_images[$index]['is_main'] == 1,
                ]);
            }
        }

        // 3. Redirigir con mensaje de éxito
        return redirect()->route('cliente.mis-puntos')
                         ->with('success', '¡Local registrado correctamente en Pindoor!');
    }

    /**
     * "Borrado lógico": No eliminamos de la DB, solo marcamos como eliminado.
     */
    public function destroy($id)
    {
        $punto = PuntoInteres::where('user_id', Auth::id())->findOrFail($id);
        
        $punto->update([
            'eliminado' => true,
            'activo'    => false
        ]);

        return redirect()->route('cliente.mis-puntos')
                         ->with('success', 'El local ha sido retirado del mapa.');
    }

    /**
     * (Opcional) Vista pública para el turista
     */
    public function show($slug)
    {
        $punto = PuntoInteres::where('slug', $slug)
                             ->where('activo', true)
                             ->where('eliminado', false)
                             ->firstOrFail();

        return view('puntos.show', compact('punto'));
    }
}