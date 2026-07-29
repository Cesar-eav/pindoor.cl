<x-guest-layout>

    {{-- Mobile: descripción de Pindoor --}}
    <div class="lg:hidden -mx-8 -mt-8 mb-8 rounded-t-2xl overflow-hidden" style="background: linear-gradient(155deg, #ff6b5b 0%, #fc5648 45%, #e83a2c 100%)">
        <div class="px-8 py-8">
            <a href="/" class="inline-flex items-center mb-4">
                <span class="text-white font-bold text-2xl tracking-tight">Pin</span><span class="text-white/65 font-bold text-2xl tracking-tight">door</span>
            </a>
            <h2 class="text-white text-xl font-bold leading-snug mb-2">Tu espacio, visible en Valparaíso</h2>
            <p class="text-white/80 text-sm leading-relaxed mb-4">Carta, menú del día, ofertas y agenda. Sin depender de redes sociales.</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-white/80 text-sm">
                    <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-white text-[10px] shrink-0">✓</span>
                    Perfil gratuito para tu establecimiento
                </li>
                <li class="flex items-center gap-2 text-white/80 text-sm">
                    <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-white text-[10px] shrink-0">✓</span>
                    Publica ofertas, menú del día y agenda
                </li>
                <li class="flex items-center gap-2 text-white/80 text-sm">
                    <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-white text-[10px] shrink-0">✓</span>
                    Comunicación directa con tu público
                </li>
            </ul>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Registra tu espacio</h2>
        <p class="text-sm text-gray-500 mt-1">Crea tu perfil gratuito en Pindoor</p>
    </div>

    <a href="{{ route('auth.google') }}"
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

    <form method="POST" action="{{ route('register') }}"
          x-data="{ loading: false }"
          @submit="loading = true">
        @csrf
        <div style="display:none" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                          :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                          :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

        <div class="mt-6">
            <div class="flex justify-end">
                <x-primary-button x-bind:disabled="loading">
                    <span x-show="!loading">Crear cuenta</span>
                    <span x-show="loading" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                        </svg>
                        Creando cuenta…
                    </span>
                </x-primary-button>
            </div>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="font-semibold text-[#fc5648] hover:underline">Inicia sesión</a>
    </p>
</x-guest-layout>
