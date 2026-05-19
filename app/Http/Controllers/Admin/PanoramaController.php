<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Panorama;
use App\Models\PanoramaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PanoramaController extends Controller
{
    public function index()
    {
        $panoramas = Panorama::orderBy('orden')->orderBy('id')->get();
        return view('admin.panoramas.index', compact('panoramas'));
    }

    public function create()
    {
        return view('admin.panoramas.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'ubicacion'   => 'nullable|string|max:255',
            'fecha'       => 'required|date',
            'fecha_fin'   => 'nullable|date|after_or_equal:fecha',
            'hora'        => 'nullable|string|max:100',
            'enlace'      => 'nullable|url|max:500',
            'categoria'   => 'nullable|string|in:' . implode(',', array_keys(\App\Models\Panorama::CATEGORIAS)),
            'orden'       => 'nullable|integer|min:0',
            'activo'      => 'nullable|boolean',
            'es_gratuito' => 'nullable|boolean',
            'imagen'      => 'nullable|image|max:4096',
            'imagenes.*'  => 'nullable|image|max:4096',
        ]);

        $data['activo']      = $request->boolean('activo', true);
        $data['es_gratuito'] = $request->boolean('es_gratuito', false);
        $data['orden']       = $request->input('orden', 0);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            Log::info('[Panorama][store] portada', [
                'original' => $file->getClientOriginalName(),
                'size'     => $file->getSize(),
                'mime'     => $file->getMimeType(),
                'disk_root'=> Storage::disk('public')->path(''),
            ]);
            try {
                $data['imagen'] = $file->store('panoramas', 'public');
                Log::info('[Panorama][store] portada guardada', ['ruta' => $data['imagen']]);
            } catch (\Throwable $e) {
                Log::error('[Panorama][store] error al guardar portada', ['error' => $e->getMessage()]);
                return back()->withInput()->withErrors(['imagen' => 'Error al guardar la imagen: ' . $e->getMessage()]);
            }
        }

        $panorama = Panorama::create($data);

        if ($request->hasFile('imagenes')) {
            foreach ($request->file('imagenes') as $i => $file) {
                Log::info('[Panorama][store] adicional #' . $i, [
                    'original' => $file->getClientOriginalName(),
                    'size'     => $file->getSize(),
                    'mime'     => $file->getMimeType(),
                ]);
                try {
                    $ruta = $file->store('panoramas', 'public');
                    $panorama->imagenes()->create(['ruta' => $ruta, 'orden' => $i]);
                    Log::info('[Panorama][store] adicional guardada', ['ruta' => $ruta]);
                } catch (\Throwable $e) {
                    Log::error('[Panorama][store] error adicional #' . $i, ['error' => $e->getMessage()]);
                }
            }
        }

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama creado correctamente.');
    }

    public function edit(Panorama $panorama)
    {
        $panorama->load('imagenes');
        return view('admin.panoramas.edit', compact('panorama'));
    }

    public function update(Request $request, Panorama $panorama)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'ubicacion'   => 'nullable|string|max:255',
            'fecha'       => 'required|date',
            'fecha_fin'   => 'nullable|date|after_or_equal:fecha',
            'hora'        => 'nullable|string|max:100',
            'enlace'      => 'nullable|url|max:500',
            'categoria'   => 'nullable|string|in:' . implode(',', array_keys(\App\Models\Panorama::CATEGORIAS)),
            'orden'       => 'nullable|integer|min:0',
            'activo'      => 'nullable|boolean',
            'es_gratuito' => 'nullable|boolean',
            'imagen'      => 'nullable|image|max:4096',
            'imagenes.*'  => 'nullable|image|max:4096',
        ]);

        $data['activo']      = $request->boolean('activo', true);
        $data['es_gratuito'] = $request->boolean('es_gratuito', false);
        $data['orden']       = $request->input('orden', 0);

        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            Log::info('[Panorama][update] portada', [
                'panorama_id' => $panorama->id,
                'original'    => $file->getClientOriginalName(),
                'size'        => $file->getSize(),
                'mime'        => $file->getMimeType(),
                'disk_root'   => Storage::disk('public')->path(''),
            ]);
            try {
                if ($panorama->imagen) {
                    Storage::disk('public')->delete($panorama->imagen);
                }
                $data['imagen'] = $file->store('panoramas', 'public');
                Log::info('[Panorama][update] portada guardada', ['ruta' => $data['imagen']]);
            } catch (\Throwable $e) {
                Log::error('[Panorama][update] error al guardar portada', ['error' => $e->getMessage()]);
                return back()->withInput()->withErrors(['imagen' => 'Error al guardar la imagen: ' . $e->getMessage()]);
            }
        }

        $panorama->update($data);

        if ($request->hasFile('imagenes')) {
            $offset = $panorama->imagenes()->max('orden') + 1;
            foreach ($request->file('imagenes') as $i => $file) {
                Log::info('[Panorama][update] adicional #' . $i, [
                    'panorama_id' => $panorama->id,
                    'original'    => $file->getClientOriginalName(),
                    'size'        => $file->getSize(),
                    'mime'        => $file->getMimeType(),
                ]);
                try {
                    $ruta = $file->store('panoramas', 'public');
                    $panorama->imagenes()->create(['ruta' => $ruta, 'orden' => $offset + $i]);
                    Log::info('[Panorama][update] adicional guardada', ['ruta' => $ruta]);
                } catch (\Throwable $e) {
                    Log::error('[Panorama][update] error adicional #' . $i, ['error' => $e->getMessage()]);
                }
            }
        }

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama actualizado correctamente.');
    }

    public function destroy(Panorama $panorama)
    {
        if ($panorama->imagen) {
            Storage::disk('public')->delete($panorama->imagen);
        }

        foreach ($panorama->imagenes as $imagen) {
            Storage::disk('public')->delete($imagen->ruta);
        }

        $panorama->delete();

        return redirect()->route('admin.panoramas.index')->with('success', 'Panorama eliminado.');
    }

    public function destroyImagen(PanoramaImagen $imagen)
    {
        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();

        return back()->with('success', 'Imagen eliminada.');
    }

    public function toggle(Panorama $panorama)
    {
        $panorama->update(['activo' => ! $panorama->activo]);
        return back()->with('success', 'Visibilidad actualizada.');
    }
}
