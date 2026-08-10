<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperadorTuristico;
use App\Models\PuntoInteres;
use App\Models\Ruta;
use App\Services\ImagenComprimida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RutaController extends Controller
{
    public function index()
    {
        $rutas = Ruta::orderByDesc('created_at')->get();
        return view('admin.rutas.index', compact('rutas'));
    }

    public function create()
    {
        $puntos = $this->puntosDisponibles();
        $operadores = OperadorTuristico::where('activo', true)->orderBy('nombre')->get();
        return view('admin.rutas.create', compact('puntos', 'operadores'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        $data['slug']       = Ruta::generarSlug($data['titulo_es']);
        $publicado          = (bool) ($data['publicado'] ?? false);
        $data['publicado']  = $publicado;
        $data['publicado_en'] = $publicado ? now() : null;

        if ($request->hasFile('imagen_portada')) {
            $data['imagen_portada'] = ImagenComprimida::guardar($request->file('imagen_portada'), 'rutas');
        }

        $ruta = new Ruta();
        $ruta->slug            = $data['slug'];
        $ruta->imagen_portada  = $data['imagen_portada'] ?? null;
        $ruta->publicado       = $publicado;
        $ruta->publicado_en    = $data['publicado_en'];
        $ruta->setTranslation('titulo', 'es', $data['titulo_es'])
             ->setTranslation('titulo', 'en', $data['titulo_en'] ?? '')
             ->setTranslation('titulo', 'fr', $data['titulo_fr'] ?? '')
             ->setTranslation('descripcion', 'es', $data['descripcion_es'] ?? '')
             ->setTranslation('descripcion', 'en', $data['descripcion_en'] ?? '')
             ->setTranslation('descripcion', 'fr', $data['descripcion_fr'] ?? '');
        $ruta->save();

        $this->sincronizarPuntos($request, $ruta);
        $this->sincronizarOperadores($request, $ruta);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.rutas.edit', $ruta)->with('success', 'Ruta creada correctamente.');
        }
        return redirect()->route('admin.rutas.index')->with('success', 'Ruta creada correctamente.');
    }

    public function edit(Ruta $ruta)
    {
        $ruta->load('puntos', 'operadores');
        $puntos = $this->puntosDisponibles();
        $operadores = OperadorTuristico::where('activo', true)->orderBy('nombre')->get();
        return view('admin.rutas.edit', compact('ruta', 'puntos', 'operadores'));
    }

    public function update(Request $request, Ruta $ruta)
    {
        $data = $this->validar($request);

        $data['slug'] = Ruta::generarSlug($data['titulo_es'], $ruta->id);
        $publicado    = (bool) ($data['publicado'] ?? false);

        if ($publicado && !$ruta->publicado_en) {
            $ruta->publicado_en = now();
        } elseif (!$publicado) {
            $ruta->publicado_en = null;
        }

        if ($request->hasFile('imagen_portada')) {
            if ($ruta->imagen_portada) {
                Storage::disk('public')->delete($ruta->imagen_portada);
            }
            $ruta->imagen_portada = ImagenComprimida::guardar($request->file('imagen_portada'), 'rutas');
        }

        $ruta->slug      = $data['slug'];
        $ruta->publicado = $publicado;
        $ruta->setTranslation('titulo', 'es', $data['titulo_es'])
             ->setTranslation('titulo', 'en', $data['titulo_en'] ?? '')
             ->setTranslation('titulo', 'fr', $data['titulo_fr'] ?? '')
             ->setTranslation('descripcion', 'es', $data['descripcion_es'] ?? '')
             ->setTranslation('descripcion', 'en', $data['descripcion_en'] ?? '')
             ->setTranslation('descripcion', 'fr', $data['descripcion_fr'] ?? '');
        $ruta->save();

        $this->sincronizarPuntos($request, $ruta);
        $this->sincronizarOperadores($request, $ruta);

        if ($request->input('accion') === 'seguir') {
            return redirect()->route('admin.rutas.edit', $ruta)->with('success', 'Ruta actualizada correctamente.');
        }
        return redirect()->route('admin.rutas.index')->with('success', 'Ruta actualizada correctamente.');
    }

    public function destroy(Ruta $ruta)
    {
        $ruta->delete();
        return redirect()->route('admin.rutas.index')->with('success', 'Ruta eliminada.');
    }

    public function preview(Ruta $ruta)
    {
        $ruta->load('puntos.categoria', 'puntos.imagenPrincipal', 'operadores');
        return view('rutas.show', compact('ruta'));
    }

    private function puntosDisponibles()
    {
        return PuntoInteres::where('eliminado', false)
            ->with(['categoria:id,nombre,icono', 'imagenPrincipal:id,punto_interes_id,ruta'])
            ->get(['id', 'title', 'sector', 'categoria_id'])
            ->sortBy(fn ($p) => $p->title)->values();
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'titulo_es'      => 'required|string|max:255',
            'titulo_en'      => 'nullable|string|max:255',
            'titulo_fr'      => 'nullable|string|max:255',
            'descripcion_es' => 'nullable|string|max:2000',
            'descripcion_en' => 'nullable|string|max:2000',
            'descripcion_fr' => 'nullable|string|max:2000',
            'imagen_portada' => 'nullable|image|max:25600',
            'publicado'      => 'nullable|boolean',
        ]);
    }

    // Sincroniza los puntos seleccionados respetando el orden en que se agregaron.
    private function sincronizarPuntos(Request $request, Ruta $ruta): void
    {
        $ids = $request->input('puntos', []);
        $ruta->puntos()->sync(
            collect($ids)->filter()->values()
                ->mapWithKeys(fn ($id, $i) => [(int) $id => ['orden' => $i]])
                ->all()
        );
    }

    private function sincronizarOperadores(Request $request, Ruta $ruta): void
    {
        $ids = $request->input('operadores', []);
        $ruta->operadores()->sync(collect($ids)->filter()->values()->all());
    }
}
