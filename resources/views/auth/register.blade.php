<x-guest-layout>

    {{-- Mobile: descripción de Pindoor --}}
    <div class="lg:hidden -mx-8 -mt-8 mb-8 rounded-t-2xl overflow-hidden" style="background: linear-gradient(155deg, #ff6b5b 0%, #fc5648 45%, #e83a2c 100%)">
        <div class="px-8 py-8">
            <a href="/" class="inline-flex items-center mb-4">
                <span class="text-white font-bold text-2xl tracking-tight">Pin</span><span class="text-white/65 font-bold text-2xl tracking-tight">door</span>
            </a>
            <h2 class="text-white text-xl font-bold leading-snug mb-2">Tu negocio, visible en Valparaíso</h2>
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
        <h2 class="text-xl font-bold text-gray-900">Registra tu negocio</h2>
        <p class="text-sm text-gray-500 mt-1">Crea tu perfil gratuito en Pindoor</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        {{-- Honeypot: campo invisible que solo los bots llenan --}}
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

        <div class="flex justify-end mt-6">
            <x-primary-button>Crear cuenta</x-primary-button>
        </div>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="font-semibold text-[#fc5648] hover:underline">Inicia sesión</a>
    </p>
</x-guest-layout>
