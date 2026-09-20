<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\ReclamoNegocio;
use App\Models\User;
use App\Notifications\AdminNewArtistaNotification;
use App\Notifications\AdminNewOperadorNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(Request $request)
    {
        $tipo = in_array($request->query('tipo'), ['operador', 'artista']) ? $request->query('tipo') : 'cliente';
        session(['registro_tipo' => $tipo]);

        if ($request->filled('reclamo_token')) {
            session(['reclamo_token' => $request->query('reclamo_token')]);
        }

        $challenge = $request->query('challenge');
        if ($request->boolean('app') && is_string($challenge) && preg_match('/^[a-f0-9]{64}$/', $challenge)) {
            session(['google_app_challenge' => $challenge]);
        }

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

            if ($user->type === 'operador') {
                Notification::route('mail', Configuracion::emailsNotificacion())
                    ->notify(new AdminNewOperadorNotification($user));
            } elseif ($user->type === 'artista') {
                Notification::route('mail', Configuracion::emailsNotificacion())
                    ->notify(new AdminNewArtistaNotification($user));
            }
        }

        $reclamoToken = session()->pull('reclamo_token');

        // Google bloquea OAuth dentro del WebView, así que la app lo hace en un Custom Tab (otro cookie jar)
        // y la sesión se transfiere al WebView con un token de un solo uso.
        if ($challenge = session()->pull('google_app_challenge')) {
            $token = Str::random(64);
            Cache::put("google_app_login:{$token}", [
                'user_id'       => $user->id,
                'challenge'     => $challenge,
                'reclamo_token' => $reclamoToken,
            ], now()->addSeconds(120));

            return response()->view('auth.google-app-return', [
                'url' => 'pindoor://auth/google/exchange?t=' . $token,
            ]);
        }

        Auth::login($user, remember: true);

        return $this->redirectAfterLogin($user, $reclamoToken);
    }

    public function exchange(Request $request)
    {
        $token    = (string) $request->query('t');
        $verifier = (string) $request->query('v');

        $payload = preg_match('/^[A-Za-z0-9]{64}$/', $token)
            ? Cache::pull("google_app_login:{$token}")
            : null;

        if (! $payload || ! hash_equals($payload['challenge'], hash('sha256', $verifier))) {
            return redirect()->route('login')->withErrors(['email' => 'No se pudo completar el inicio de sesión con Google. Intenta de nuevo.']);
        }

        $user = User::find($payload['user_id']);
        if (! $user) {
            return redirect()->route('login');
        }

        Auth::login($user, remember: true);

        return $this->redirectAfterLogin($user, $payload['reclamo_token']);
    }

    private function redirectAfterLogin(User $user, ?string $reclamoToken)
    {
        if ($reclamoToken) {
            $reclamo = ReclamoNegocio::where('activation_token', $reclamoToken)->first();

            if ($reclamo && $reclamo->vigente()) {
                if ($user->email === $reclamo->email) {
                    $reclamo->completar($user);

                    return redirect()->route('cliente.perfil.editar', $reclamo->punto)
                        ->with('success', '¡Tu perfil fue activado! Ya puedes editarlo.');
                }

                return redirect()->route('reclamo.activar', $reclamoToken)
                    ->withErrors(['email' => 'Tu cuenta de Google no coincide con el email de la invitación.']);
            }
        }

        return redirect()->intended(route('dashboard'));
    }
}
