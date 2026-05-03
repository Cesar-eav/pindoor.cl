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
        <p class="text-sm text-gray-500 mt-1">Administra tu negocio en Pindoor</p>
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

    <p class="mt-6 text-center text-sm text-gray-500">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="font-semibold text-[#fc5648] hover:underline">Regístrate</a>
    </p>
</x-guest-layout>
