<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experiencia;
use App\Models\ExperienciaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExperienciaController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->input('search');
        $categoria = $request->input('categoria');

        $query = Experiencia::orderBy('orden')->orderBy('titulo');

        if ($search) {
            $query->where(fn($q) => $q->where('titulo', 'like', "%{$search}%")
                                      ->orWhere('proveedor', 'like', "%{$search}%")
                                      ->orWhere('ubicacion', 'like', "%{$search}%"));
        }
        if ($categoria === 'gratuito') {
            $query->where('es_gratuito', true);
        } elseif ($categoria) {
            $query->where('categoria', $categoria);
        }

        $experiencias = $query->get();
        $pendientes   = Experiencia::pendientes()->get();
        $categorias   = Experiencia::CATEGORIAS;

        return view('admin.experiencias.index', compact('experiencias', 'pendientes', 'categorias', 'search', 'categoria'));
    }

    public function create()
    {
        return view('admin.experiencias.create');
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data = $this->procesarBooleanos($request, $data);
        $data = $this->procesarDias($request, $data);

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('experiencias', 'public');
        }

        $experiencia = Experiencia::create($data);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $i => $file) {
                $ruta = $file->store('experiencias', 'public');
                $experiencia->imagenes()->create(['ruta' => $ruta, 'orden' => $i]);
            }
        }

        return redirect()->route('admin.experiencias.index')->with('success', 'Experiencia creada correctamente.');
    }

    public function edit(Experiencia $experiencia)
    {
        $experiencia->load('imagenes');
        return view('admin.experiencias.edit', compact('experiencia'));
    }

    public function update(Request $request, Experiencia $experiencia)
    {
        $data = $this->validar($request);
        $data = $this->procesarBooleanos($request, $data);
        $data = $this->procesarDias($request, $data);

        if ($request->hasFile('imagen')) {
            if ($experiencia->imagen) Storage::disk('public')->delete($experiencia->imagen);
            $data['imagen'] = $request->file('imagen')->store('experiencias', 'public');
        }

        $experiencia->update($data);

        if ($request->hasFile('imagenes')) {
            $offset = $experiencia->imagenes()->max('orden') + 1;
            foreach ($request->file('imagenes') as $i => $file) {
                $ruta = $file->store('experiencias', 'public');
                $experiencia->imagenes()->create(['ruta' => $ruta, 'orden' => $offset + $i]);
            }
        }

        return redirect()->route('admin.experiencias.index')->with('success', 'Experiencia actualizada correctamente.');
    }

    public function destroy(Experiencia $experiencia)
    {
        foreach ($experiencia->imagenes as $img) {
            Storage::disk('public')->delete($img->ruta);
        }
        $experiencia->delete();

        return redirect()->route('admin.experiencias.index')->with('success', 'Experiencia eliminada.');
    }

    public function destroyImagen(ExperienciaImagen $imagen)
    {
        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Imagen eliminada.');
    }

    public function toggle(Experiencia $experiencia)
    {
        $experiencia->update(['activo' => !$experiencia->activo]);
        return back()->with('success', 'Visibilidad actualizada.');
    }

    public function aprobar(Experiencia $experiencia)
    {
        $experiencia->update(['estado' => 'aprobada', 'activo' => true]);
        return back()->with('success', 'Experiencia aprobada y publicada.');
    }

    public function rechazar(Experiencia $experiencia)
    {
        $experiencia->update(['estado' => 'rechazada', 'activo' => false]);
        return back()->with('success', 'Experiencia rechazada.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'titulo'       => 'required|string|max:255',
            'proveedor'    => 'nullable|string|max:255',
            'descripcion'  => 'nullable|string|max:2000',
            'ubicacion'    => 'nullable|string|max:255',
            'categoria'    => 'nullable|string|in:' . implode(',', array_keys(Experiencia::CATEGORIAS)),
            'es_gratuito'  => 'nullable|boolean',
            'precio'       => 'nullable|integer|min:0',
            'duracion'     => 'nullable|string|max:50',
            'capacidad'    => 'nullable|integer|min:1',
            'nivel'        => 'nullable|string|in:' . implode(',', array_keys(Experiencia::NIVELES)),
            'dias_semana'  => 'nullable|array',
            'dias_semana.*'=> 'integer|between:1,7',
            'hora'         => 'nullable|string|max:100',
            'enlace'       => 'nullable|url|max:500',
            'whatsapp'     => 'nullable|string|max:30',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date|after_or_equal:fecha_inicio',
            'orden'        => 'nullable|integer|min:0',
            'activo'       => 'nullable|boolean',
            'imagen'       => 'nullable|image|max:4096',
            'imagenes.*'   => 'nullable|image|max:4096',
        ]);
    }

    private function procesarBooleanos(Request $request, array $data): array
    {
        $data['activo']      = $request->boolean('activo', true);
        $data['es_gratuito'] = $request->boolean('es_gratuito', false);
        $data['orden']       = $request->input('orden', 0);
        if ($data['es_gratuito']) $data['precio'] = null;
        return $data;
    }

    private function procesarDias(Request $request, array $data): array
    {
        $data['dias_semana'] = $request->has('dias_semana')
            ? array_map('intval', $request->input('dias_semana'))
            : null;
        return $data;
    }
}
