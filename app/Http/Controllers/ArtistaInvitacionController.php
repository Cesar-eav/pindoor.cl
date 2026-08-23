<?php

namespace App\Http\Controllers;

use App\Models\Artista;
use App\Models\ArtistaInvitacion;
use App\Models\User;
use App\Notifications\ArtistaInvitacionNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class ArtistaInvitacionController extends Controller
{
    // ── Gestión (desde el panel del artista) ────────────────────────────────

    public function store(Request $request)
    {
        $artista = auth()->user()->artistaActivo();
        abort_unless($artista, 404);

        $data = $request->validate([
            'email' => 'required|email|max:255',
        ]);
        $email = strtolower($data['email']);

        $yaEsMiembro = $artista->miembros()->where('email', $email)->exists();
        if ($yaEsMiembro) {
            return back()->withErrors(['email' => 'Ese correo ya es miembro de este perfil.']);
        }

        $invitacionPendiente = $artista->invitaciones()
            ->where('email', $email)
            ->whereNull('aceptada_at')
            ->where('expires_at', '>', now())
            ->exists();
        if ($invitacionPendiente) {
            return back()->withErrors(['email' => 'Ya existe una invitación pendiente para ese correo.']);
        }

        $invitacion = $artista->invitaciones()->create([
            'email'           => $email,
            'token'           => Str::random(40),
            'invitado_por_id' => auth()->id(),
            'expires_at'      => now()->addDays(7),
        ]);

        Notification::route('mail', $email)->notify(new ArtistaInvitacionNotification($invitacion));

        return back()->with('success', 'Invitación enviada a ' . $email . '.');
    }

    public function destroy(ArtistaInvitacion $invitacion)
    {
        $artista = auth()->user()->artistaActivo();
        abort_unless($artista && $invitacion->artista_id === $artista->id, 403);

        $invitacion->delete();

        return back()->with('success', 'Invitación cancelada.');
    }

    public function destroyMiembro(Artista $artista, User $user)
    {
        abort_unless(auth()->user()->artistas->contains($artista->id), 403);

        if ($artista->miembros()->count() <= 1) {
            return back()->withErrors(['miembro' => 'No puedes quitar al último miembro del perfil.']);
        }

        $artista->miembros()->detach($user->id);

        return back()->with('success', 'Miembro eliminado.');
    }

    // ── Aceptación pública (con o sin sesión) ───────────────────────────────

    public function aceptar(string $token)
    {
        $invitacion = ArtistaInvitacion::where('token', $token)->first();

        if (!$invitacion || !$invitacion->vigente()) {
            return view('artista.invitacion-invalida');
        }

        if (!auth()->check()) {
            $existe = User::where('email', $invitacion->email)->exists();
            return view('artista.invitacion-aceptar', compact('invitacion', 'existe'));
        }

        return $this->confirmar($invitacion);
    }

    public function storeAceptarNuevo(Request $request, string $token)
    {
        $invitacion = ArtistaInvitacion::where('token', $token)->first();
        abort_unless($invitacion && $invitacion->vigente(), 404);

        abort_if(User::where('email', $invitacion->email)->exists(), 409);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $invitacion->email,
            'password' => Hash::make($data['password']),
            'type'     => 'artista',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return $this->confirmar($invitacion);
    }

    private function confirmar(ArtistaInvitacion $invitacion)
    {
        if (auth()->user()->email !== $invitacion->email) {
            return view('artista.invitacion-invalida', ['emailIncorrecto' => true]);
        }

        $invitacion->artista->miembros()->syncWithoutDetaching(auth()->id());
        $invitacion->update(['aceptada_at' => now()]);

        session(['artista_activo_id' => $invitacion->artista_id]);

        return redirect()->route('artista.perfil')
            ->with('success', 'Te uniste a ' . $invitacion->artista->nombre . '.');
    }
}
