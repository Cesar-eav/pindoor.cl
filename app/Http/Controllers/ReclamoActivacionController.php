<?php

namespace App\Http\Controllers;

use App\Models\ReclamoNegocio;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ReclamoActivacionController extends Controller
{
    public function activar(string $token)
    {
        $reclamo = ReclamoNegocio::where('activation_token', $token)->first();

        if (!$reclamo || !$reclamo->vigente()) {
            return view('reclamo.activacion-invalida');
        }

        if (auth()->check()) {
            if (auth()->user()->email !== $reclamo->email) {
                return view('reclamo.activacion-invalida', ['emailIncorrecto' => true]);
            }

            $reclamo->completar(auth()->user());

            return redirect()->route('cliente.perfil.editar', $reclamo->punto)
                ->with('success', '¡Tu perfil fue activado! Ya tiene la ficha básica cargada (título, descripción, ubicación...) — puedes actualizarla cuando quieras.');
        }

        $existe = User::where('email', $reclamo->email)->exists();

        return view('reclamo.activacion-aceptar', compact('reclamo', 'existe'));
    }

    public function store(Request $request, string $token)
    {
        $reclamo = ReclamoNegocio::where('activation_token', $token)->first();
        abort_unless($reclamo && $reclamo->vigente(), 404);

        abort_if(User::where('email', $reclamo->email)->exists(), 409);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'              => $data['name'],
            'email'             => $reclamo->email,
            'password'          => Hash::make($data['password']),
            'type'              => 'cliente',
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));
        Auth::login($user);

        $reclamo->completar($user);

        return redirect()->route('cliente.perfil.editar', $reclamo->punto)
            ->with('success', '¡Tu perfil fue activado! Ya tiene la ficha básica cargada (título, descripción, ubicación...) — puedes actualizarla cuando quieras.');
    }
}
