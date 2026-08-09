<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\OperadorTuristico;
use App\Models\PuntoInteres;
use App\Models\User;
use App\Notifications\AdminNewOperadorNotification;
use App\Services\ImagenComprimida;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class OperadorController extends Controller
{
    // ── Registro ─────────────────────────────────────────────────────────────

    public function showRegister()
    {
        return view('auth.register_operador');
    }

    public function register(Request $request)
    {
        if ($request->filled('website')) {
            return redirect()->route('operador.register');
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
            'type'     => 'operador',
        ]);

        event(new Registered($user));

        Notification::route('mail', ['cesar.eav@gmail.com', 'cesarandrade@pindoor.cl'])
            ->notify(new AdminNewOperadorNotification($user));

        Auth::login($user);

        return redirect()->route('operador.nuevo');
    }

    // ── Onboarding ───────────────────────────────────────────────────────────

    public function onboarding()
    {
        if (auth()->user()->operador) {
            return redirect()->route('operador.perfil');
        }
        return view('operador.onboarding');
    }

    public function crearPerfil(Request $request)
    {
        $data = $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'ciudad'      => 'nullable|string|max:100',
            'imagen'      => 'nullable|image|max:4096',
        ]);

        $data['user_id'] = auth()->id();
        $data['slug']    = OperadorTuristico::slugUnico($data['nombre']);

        if ($request->hasFile('imagen')) {
            $data['imagen_perfil'] = ImagenComprimida::guardar($request->file('imagen'), 'operadores');
        }

        OperadorTuristico::create($data);

        return redirect()->route('operador.perfil')->with('success', 'Perfil creado. ¡Ahora complétalo y elige los lugares que visitas!');
    }

    // ── Perfil (dashboard) ───────────────────────────────────────────────────

    public function perfil()
    {
        $operador = auth()->user()->operador;

        if (!$operador) {
            return redirect()->route('operador.nuevo');
        }

        $operador->load('puntos.categoria');

        $categorias = Categoria::where('disponible_para_operadores', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'icono']);

        $puntosSeleccionados = $operador->puntos->pluck('id')->all();

        $puntosData = PuntoInteres::publico()
            ->whereIn('categoria_id', $categorias->pluck('id'))
            ->with('categoria:id,nombre,icono')
            ->get(['id', 'title', 'sector', 'categoria_id'])
            ->map(fn($p) => [
                'id'        => $p->id,
                'title'     => (string) $p->title,
                'sector'    => $p->sector,
                'categoria' => $p->categoria?->nombre,
                'emoji'     => $p->categoria?->icono,
            ])
            ->values();

        $categoriasDisponibles = $puntosData->pluck('categoria')->filter()->unique()->sort()->values();

        return view('operador.perfil', compact('operador', 'puntosData', 'categoriasDisponibles', 'puntosSeleccionados'));
    }

    public function actualizarPerfil(Request $request)
    {
        $operador = auth()->user()->operador;

        $data = $request->validate([
            'nombre'           => 'required|string|max:255',
            'descripcion'      => 'nullable|string|max:2000',
            'ciudad'           => 'nullable|string|max:100',
            'email_contacto'   => 'nullable|email|max:255',
            'telefono'         => 'nullable|string|max:30',
            'enlace_web'       => 'nullable|url|max:500',
            'enlace_instagram' => 'nullable|url|max:500',
            'enlace_facebook'  => 'nullable|url|max:500',
            'enlace_whatsapp'  => 'nullable|url|max:500',
            'imagen'           => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('imagen')) {
            if ($operador->imagen_perfil) {
                Storage::disk('public')->delete($operador->imagen_perfil);
            }
            $data['imagen_perfil'] = ImagenComprimida::guardar($request->file('imagen'), 'operadores');
        }

        $operador->update($data);

        return back()->with('success', 'Perfil actualizado.');
    }

    public function guardarPuntos(Request $request)
    {
        $operador = auth()->user()->operador;
        abort_unless($operador, 404);

        $categoriasPermitidas = Categoria::where('disponible_para_operadores', true)->pluck('id');

        $idsValidos = PuntoInteres::publico()
            ->whereIn('id', collect($request->input('puntos', []))->filter())
            ->whereIn('categoria_id', $categoriasPermitidas)
            ->pluck('id');

        $operador->puntos()->sync($idsValidos);

        return back()->with('success', 'Lugares actualizados.');
    }

    // ── Perfil público ───────────────────────────────────────────────────────

    public function show(string $slug)
    {
        $operador = OperadorTuristico::where('slug', $slug)->where('activo', true)->firstOrFail();
        $operador->load('puntos.categoria', 'puntos.imagenPrincipal', 'rutas.puntos');
        return view('operador.show', compact('operador'));
    }
}
