<x-guest-layout>

    {{-- Mobile: diseño visual en lugar de descripción --}}
    <div class="lg:hidden -mx-8 -mt-8 mb-8 rounded-t-2xl overflow-hidden" style="background: linear-gradient(155deg, #ff6b5b 0%, #fc5648 45%, #e83a2c 100%)">
        <div class="px-8 py-8 flex items-center justify-between">
            <div>
                <a href="/" class="inline-flex items-center mb-1">
                    <span class="text-white font-bold text-2xl tracking-tight">Pin</span><span class="text-white/65 font-bold text-2xl tracking-tight">door</span>
                </a>
                <p class="text-white/80 text-sm">Bienvenido de vuelta</p>
            </div>
            @if($featuredPuntos->count() >= 3)
            <div class="flex -space-x-3">
                @foreach($featuredPuntos->take(3) as $punto)
                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white shadow">
                    <img src="{{ asset('storage/' . $punto->imagenPrincipal->ruta) }}" class="w-full h-full object-cover">
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Accede a tu perfil</h2>
        <p class="text-sm text-gray-500 mt-1">Administra tu espacio en Pindoor</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                          name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                       class="rounded border-gray-300 text-[#fc5648] shadow-sm focus:ring-[#fc5648]"
                       name="remember">
                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-500 hover:text-gray-800 transition"
                   href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
            <x-primary-button>Iniciar sesión</x-primary-button>
        </div>
    </form>

    <div class="flex items-center gap-3 mt-6">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400 font-medium">o</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <a href="{{ route('auth.google') }}"
       class="mt-4 flex items-center justify-center gap-3 w-full py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.47a5.54 5.54 0 01-2.4 3.63v3.02h3.87c2.27-2.09 3.58-5.17 3.58-8.84z"/>
            <path fill="#34A853" d="M12 24c3.24 0 5.96-1.08 7.94-2.9l-3.87-3.02c-1.08.72-2.45 1.15-4.07 1.15-3.13 0-5.78-2.11-6.73-4.96H1.27v3.11A12 12 0 0012 24z"/>
            <path fill="#FBBC05" d="M5.27 14.27a7.2 7.2 0 010-4.54V6.62H1.27a12 12 0 000 10.76l4-3.11z"/>
            <path fill="#EA4335" d="M12 4.77c1.76 0 3.35.6 4.6 1.79l3.44-3.44C17.95 1.19 15.24 0 12 0A12 12 0 001.27 6.62l4 3.11C6.22 6.88 8.87 4.77 12 4.77z"/>
        </svg>
        Continuar con Google
    </a>

    <p class="mt-6 text-center text-sm text-gray-500">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="font-semibold text-[#fc5648] hover:underline">Regístrate</a>
    </p>
</x-guest-layout>
