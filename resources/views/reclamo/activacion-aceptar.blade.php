<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Activa tu perfil en Pindoor</h2>
        <p class="text-sm text-gray-500 mt-1">
            Crea tu cuenta para administrar <span class="font-semibold text-gray-700">{{ $reclamo->punto->title }}</span>.
        </p>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    @if($existe)
        <p class="text-sm text-gray-600 mb-4">
            Ya existe una cuenta Pindoor con el correo <strong>{{ $reclamo->email }}</strong>.
            Inicia sesión con esa cuenta y luego vuelve a abrir este mismo link para activar el perfil.
        </p>
        <a href="{{ route('login') }}" class="inline-block bg-[#fc5648] hover:opacity-90 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition">
            Iniciar sesión
        </a>
    @else
        <a href="{{ route('auth.google', ['reclamo_token' => $reclamo->activation_token]) }}"
           class="flex items-center justify-center gap-3 w-full py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.47a5.54 5.54 0 01-2.4 3.63v3.02h3.87c2.27-2.09 3.58-5.17 3.58-8.84z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.96-1.08 7.94-2.9l-3.87-3.02c-1.08.72-2.45 1.15-4.07 1.15-3.13 0-5.78-2.11-6.73-4.96H1.27v3.11A12 12 0 0012 24z"/>
                <path fill="#FBBC05" d="M5.27 14.27a7.2 7.2 0 010-4.54V6.62H1.27a12 12 0 000 10.76l4-3.11z"/>
                <path fill="#EA4335" d="M12 4.77c1.76 0 3.35.6 4.6 1.79l3.44-3.44C17.95 1.19 15.24 0 12 0A12 12 0 001.27 6.62l4 3.11C6.22 6.88 8.87 4.77 12 4.77z"/>
            </svg>
            Continuar con Google
        </a>

        <div class="flex items-center gap-3 mt-6 mb-6">
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-xs text-gray-400 font-medium">o</span>
            <div class="flex-1 h-px bg-gray-200"></div>
        </div>

        <form method="POST" action="{{ route('reclamo.activar.store', $reclamo->activation_token) }}">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-50" type="email" :value="$reclamo->email" :disabled="true" />
            </div>

            <div class="mt-4">
                <x-input-label for="name" value="Tu nombre" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                              :value="old('name')" required autofocus />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" value="Contraseña" />
                <x-text-input id="password" class="block mt-1 w-full" type="password"
                              name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password_confirmation" value="Confirmar contraseña" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                              name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex justify-end mt-6">
                <x-primary-button>Crear cuenta y activar perfil</x-primary-button>
            </div>
        </form>
    @endif
</x-guest-layout>
