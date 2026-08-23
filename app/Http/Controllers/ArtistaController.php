<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\ArtistaEvento;
use App\Models\ArtistaImagen;
use App\Models\User;
use App\Notifications\AdminNewArtistaNotification;
use App\Services\ImagenComprimida;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class ArtistaController extends Controller
{
    // ── Directorio público ───────────────────────────────────────────────────

    public function directorio()
    {
        $disciplina = request('disciplina');

        $artistas = Artista::where('activo', true)
            ->when($disciplina, fn($q) => $q->where('disciplina', $disciplina))
            ->orderBy('nombre')
            ->get();

        $disciplinas = Artista::DISCIPLINAS;

        return view('artista.index', compact('artistas', 'disciplinas', 'disciplina'));
    }

    // ── Registro ─────────────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register_artista');
    }

    public function register(Request $request)
    {
        if ($request->filled('website')) {
            return redirect()->route('artista.register');
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'type'     => 'artista',
        ]);

        event(new Registered($user));

        Notification::route('mail', ['cesar.eav@gmail.com', 'cesarandrade@pindoor.cl'])
            ->notify(new AdminNewArtistaNotification($user));

        Auth::login($user);

        return redirect()->route('artista.nuevo');
    }

    // ── Onboarding ───────────────────────────────────────────────────────────

    public function onboarding()
    {
        if (auth()->user()->artistas()->exists()) {
            return redirect()->route('artista.perfil');
        }
        return view('artista.onboarding');
    }

    public function crearPerfil(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'disciplina'  => 'required|string|in:' . implode(',', array_keys(Artista::DISCIPLINAS)),
            'descripcion' => 'nullable|string|max:2000',
            'ciudad'      => 'nullable|string|max:100',
            'imagen'      => 'nullable|image|max:4096',
        ]);

        $data['user_id'] = auth()->id();
        $data['slug']    = Artista::slugUnico($data['nombre']);

        if ($request->hasFile('imagen')) {
            $data['imagen_perfil'] = ImagenComprimida::guardar($request->file('imagen'), 'artistas');
        }

        $artista = Artista::create($data);
        $artista->miembros()->attach(auth()->id());
        session(['artista_activo_id' => $artista->id]);

        return redirect()->route('artista.perfil')->with('success', 'Perfil creado. ¡Ahora complétalo con tus redes y portafolio!');
    }

    // ── Cambio de banda activa (multi-membresía) ────────────────────────────

    public function cambiarActivo(Artista $artista)
    {
        abort_unless(auth()->user()->artistas->contains($artista->id), 403);

        session(['artista_activo_id' => $artista->id]);

        return redirect()->route('artista.perfil');
    }

    // ── Perfil (dashboard) ───────────────────────────────────────────────────

    public function perfil()
    {
        $artista = auth()->user()->artistaActivo();

        if (!$artista) {
            return redirect()->route('artista.nuevo');
        }

        $artista->load('imagenes');
        $eventos = $artista->eventos()->orderBy('fecha')->orderBy('hora')->get();
        $tiposEvento = \App\Models\CategoriaEvento::catalogo();
        $miembros = $artista->miembros;
        $invitaciones = $artista->invitaciones()->whereNull('aceptada_at')->where('expires_at', '>', now())->get();
        $misBandas = auth()->user()->artistas;

        return view('artista.perfil', compact('artista', 'eventos', 'tiposEvento', 'miembros', 'invitaciones', 'misBandas'));
    }

    public function actualizarPerfil(Request $request)
    {
        $artista = auth()->user()->artistaActivo();

        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'disciplina'        => 'required|string|in:' . implode(',', array_keys(Artista::DISCIPLINAS)),
            'descripcion'       => 'nullable|string|max:2000',
            'ciudad'            => 'nullable|string|max:100',
            'email_contacto'    => 'nullable|email|max:255',
            'telefono'          => 'nullable|string|max:30',
            'enlace_web'        => 'nullable|url|max:500',
            'enlace_instagram'  => 'nullable|url|max:500',
            'enlace_facebook'   => 'nullable|url|max:500',
            'enlace_spotify'    => 'nullable|url|max:500',
            'enlace_youtube'       => 'nullable|url|max:500',
            'enlace_youtube_video' => 'nullable|url|max:500',
            'imagen'            => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('imagen')) {
            if ($artista->imagen_perfil) {
                Storage::disk('public')->delete($artista->imagen_perfil);
            }
            $data['imagen_perfil'] = ImagenComprimida::guardar($request->file('imagen'), 'artistas');
        }

        $artista->update($data);

        return back()->with('success', 'Perfil actualizado.');
    }

    // ── Galería ──────────────────────────────────────────────────────────────

    public function subirImagen(Request $request)
    {
        $request->validate(['imagenes.*' => 'required|image|max:4096']);

        $artista = auth()->user()->artistaActivo();
        $offset  = $artista->imagenes()->max('orden') + 1;

        foreach ($request->file('imagenes') as $i => $file) {
            $ruta = ImagenComprimida::guardar($file, 'artistas/portafolio');
            $artista->imagenes()->create(['ruta' => $ruta, 'orden' => $offset + $i]);
        }

        return back()->with('success', 'Imágenes subidas.');
    }

    public function eliminarImagen(ArtistaImagen $imagen)
    {
        abort_if(!$imagen->artista->miembros->contains(auth()->id()), 403);
        Storage::disk('public')->delete($imagen->ruta);
        $imagen->delete();

        if (request()->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'Imagen eliminada.');
    }

    // ── Perfil público ───────────────────────────────────────────────────────

    public function show(string $slug)
    {
        $artista = Artista::where('slug', $slug)->where('activo', true)->firstOrFail();
        $artista->load('imagenes');

        $eventosProximos = $artista->eventos()
            ->where('activo', true)
            ->where('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')->orderBy('hora')
            ->get();

        return view('artista.show', compact('artista', 'eventosProximos'));
    }

    /**
     * Muestra un evento de un artista con la misma vista que un panorama subido
     * manualmente. Mismo patrón que PuntoInteresController::showEvento().
     */
    public function showEvento(string $slug, ArtistaEvento $item)
    {
        $artista = Artista::where('activo', true)->where('slug', $slug)->firstOrFail();

        abort_if($item->artista_id !== $artista->id || !$item->activo, 404);

        $item->setRelation('artista', $artista);
        $panorama = $item->comoPanorama();
        $puntoRelacionado = $artista;
        $origenTipo = 'artista';
        $panoramasRelacionados = \App\Models\Panorama::relacionados($panorama->categoria);

        return view('panoramas.show', compact('panorama', 'puntoRelacionado', 'origenTipo', 'panoramasRelacionados'));
    }
}
