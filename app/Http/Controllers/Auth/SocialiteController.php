<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(Request $request)
    {
        $tipo = in_array($request->query('tipo'), ['operador', 'artista']) ? $request->query('tipo') : 'cliente';
        session(['registro_tipo' => $tipo]);

        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'No se pudo autenticar con Google. Intenta de nuevo.']);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Vincula google_id si llegó por email la primera vez
            if (! $user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
            // El email de Google ya viene verificado
            if (! $user->email_verified_at) {
                $user->markEmailAsVerified();
            }
        } else {
            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'google_id'         => $googleUser->getId(),
                'email_verified_at' => now(),
                'password'          => null,
                'type'              => session()->pull('registro_tipo', 'cliente'),
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
