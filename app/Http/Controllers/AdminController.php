<?php

namespace App\Http\Controllers;

use App\Models\PuntoInteres;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function editPunto(PuntoInteres $punto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        $punto->load('imagenes');
        return view('admin.puntos-edit', compact('punto', 'categorias'));
    }

    public function updatePunto(Request $request, PuntoInteres $punto)
    {
        $request->validate([
            'title'       => 'required|max:255',
            'categoria_id'=> 'required|exists:categorias,id',
            'description' => 'required',
            'photos.*'    => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $categoria = Categoria::find($request->categoria_id);

        $punto->update([
            'categoria_id' => $request->categoria_id,
            'title'        => $request->title,
            'sector'       => $request->sector,
            'description'  => $request->description,
            'tags'      => $request->tags ? array_map('trim', explode(',', $request->tags)) : [],
            'autor'     => $request->autor,
            'direccion' => $request->direccion,
            'horario'   => $request->horario,
            'video_url' => $request->video_url,
            'enlace'    => $request->enlace,
            'lat'       => $request->lat,
            'lng'       => $request->lng,
        ]);

        // Actualizar imágenes existentes que se conservan
        if ($request->has('keep_images')) {
            $keepIds = array_keys($request->keep_images);

            // Eliminar las que no están en keep_images
            $punto->imagenes()->whereNotIn('id', $keepIds)->each(function ($img) {
                \Storage::disk('public')->delete($img->ruta);
                $img->delete();
            });

            // Actualizar is_main y orden de las conservadas
            foreach ($request->keep_images as $id => $meta) {
                $punto->imagenes()->where('id', $id)->update([
                    'es_principal' => ($meta['is_main'] ?? 0) == 1,
                    'orden'        => $meta['orden'] ?? 0,
                ]);
            }
        } else {
            // Si no se conserva ninguna, eliminar todas las existentes
            $punto->imagenes->each(function ($img) {
                \Storage::disk('public')->delete($img->ruta);
                $img->delete();
            });
        }

        // Añadir nuevas imágenes
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $file) {
                $path = $file->store('puntos', 'public');
                $esPrincipal = ($request->input("metadata.{$index}.is_main") == 1);
                $orden = $request->input("metadata.{$index}.orden", $index);

                $punto->imagenes()->create([
                    'ruta'         => $path,
                    'es_principal' => $esPrincipal,
                    'orden'        => $orden,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'url'     => route('admin.puntos.create'),
        ]);
    }

    // ─── Gestión de Clientes ──────────────────────────────────────────────────

    public function clientes()
    {
        $clientes = PuntoInteres::clientes()
            ->where('eliminado', false)
            ->with(['user', 'categoria'])
            ->latest()
            ->paginate(15);

        // Puntos que aún no son clientes: para que el admin pueda activar uno
        $puntosDisponibles = PuntoInteres::where('es_cliente', false)
            ->where('eliminado', false)
            ->orderBy('title')
            ->get();

        return view('admin.clientes', compact('clientes', 'puntosDisponibles'));
    }

    public function mostrarActivarCliente(PuntoInteres $punto)
    {
        // Usuarios cliente sin punto asignado (para vincular uno existente)
        $usuariosSinPunto = User::where('type', 'cliente')
            ->whereDoesntHave('puntoInteres')
            ->orderBy('name')
            ->get();

        return view('admin.clientes-activar', compact('punto', 'usuariosSinPunto'));
    }

    public function activarCliente(Request $request, PuntoInteres $punto)
    {
        // Vincular usuario existente
        if ($request->filled('user_id_existente')) {
            $request->validate(['user_id_existente' => 'required|exists:users,id']);

            $user = User::findOrFail($request->user_id_existente);
            $user->update(['type' => 'cliente']);

            $punto->update(['user_id' => $user->id, 'es_cliente' => true]);

            return redirect()->route('admin.clientes')
                ->with('success', "Punto \"{$punto->title}\" vinculado a {$user->email}.");
        }

        // Crear usuario nuevo
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'type'     => 'cliente',
        ]);

        $punto->update([
            'user_id'    => $user->id,
            'es_cliente' => true,
        ]);

        return redirect()->route('admin.clientes')
            ->with('success', "Cliente \"{$punto->title}\" activado. Credenciales creadas para {$user->email}.");
    }

    public function desactivarCliente(PuntoInteres $punto)
    {
        $punto->update(['es_cliente' => false]);

        return redirect()->route('admin.clientes')
            ->with('success', "\"{$punto->title}\" ya no figura como cliente.");
    }

    // ─────────────────────────────────────────────────────────────────────────

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

        $categoria = Categoria::find($request->categoria_id);

        $punto = PuntoInteres::create([
            'user_id'      => auth()->id(),
            'categoria_id' => $request->categoria_id,
            'title'        => $request->title,
            'slug' => Str::slug($request->title) . '-' . rand(100, 999),
            'sector' => $request->sector,
            'description' => $request->description,
            'tags'      => $request->tags ? array_map('trim', explode(',', $request->tags)) : [],
            'autor'     => $request->autor,
            'direccion' => $request->direccion,
            'horario'   => $request->horario,
            'video_url' => $request->video_url,
            'enlace'    => $request->enlace,
            'lat'       => $request->lat,
            'lng'       => $request->lng,
            'activo'    => true,
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