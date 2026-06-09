<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminNewClientNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Honeypot: si el campo "website" llega con contenido, es un bot
        if ($request->filled('website')) {
            return redirect()->route('register');
        }

        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
            'imagen_logo' => ['nullable', 'image', 'max:10240'],
        ]);

        $logoPath = null;
        if ($request->hasFile('imagen_logo')) {
            try {
                $logoPath = $this->guardarImagenComprimida($request->file('imagen_logo'), 'logos');
            } catch (\Throwable $e) {
                return back()->withInput()->withErrors([
                    'imagen_logo' => 'No se pudo procesar la imagen. Intenta con una imagen más liviana o en formato JPG/PNG.',
                ]);
            }
        }

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'imagen_logo' => $logoPath,
        ]);

        event(new Registered($user));
        // 'soporte@pindoor.cl' ,'danielapazcabrera89@gmail.com' 
        Notification::route('mail', ['cesar.eav@gmail.com'])
            ->notify(new AdminNewClientNotification($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    private function guardarImagenComprimida($archivo, string $carpeta, int $maxWidth = 1600, int $calidad = 80): string
    {
        $img = imagecreatefromstring(file_get_contents($archivo->getPathname()));

        $w = imagesx($img);
        $h = imagesy($img);

        if ($w > $maxWidth) {
            $nuevoH = (int) round($h * $maxWidth / $w);
            $redim  = imagecreatetruecolor($maxWidth, $nuevoH);
            imagealphablending($redim, false);
            imagesavealpha($redim, true);
            imagecopyresampled($redim, $img, 0, 0, 0, 0, $maxWidth, $nuevoH, $w, $h);
            imagedestroy($img);
            $img = $redim;
        }

        ob_start();
        imagewebp($img, null, $calidad);
        $webp = ob_get_clean();
        imagedestroy($img);

        $ruta = $carpeta . '/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($ruta, $webp);

        return $ruta;
    }
}
