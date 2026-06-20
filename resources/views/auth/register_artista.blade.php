<x-guest-layout>

    {{-- Panel izquierdo desktop: morado, textos de artista --}}
    <x-slot name="panelLeft">
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 relative overflow-hidden"
             style="background: linear-gradient(155deg, #7c3aed 0%, #6d28d9 45%, #5b21b6 100%)">

            <div class="absolute -top-24 -left-24 w-72 h-72 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-0 left-1/4 w-48 h-48 bg-black/5 rounded-full"></div>

            <div class="relative z-10">
                <a href="/" class="inline-flex items-center">
                    <span class="text-white font-bold text-3xl tracking-tight">Pin</span><span class="text-white/65 font-bold text-3xl tracking-tight">door</span>
                </a>
            </div>

            <div class="relative z-10">
                <h1 class="text-white text-4xl font-bold leading-tight mb-4">
                    Tu arte,<br>visible en La Escena
                </h1>
                <p class="text-white/80 text-base leading-relaxed mb-8">
                    Únete al directorio de artistas y agrupaciones<br>culturales de Valparaíso en Pindoor.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-white/80 text-sm">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white text-xs shrink-0">✓</span>
                        Perfil gratuito con tu disciplina y portafolio
                    </li>
                    <li class="flex items-center gap-3 text-white/80 text-sm">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white text-xs shrink-0">✓</span>
                        Enlaza Instagram, Spotify, YouTube y más
                    </li>
                    <li class="flex items-center gap-3 text-white/80 text-sm">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white text-xs shrink-0">✓</span>
                        Contacto directo con quienes te quieran contratar
                    </li>
                </ul>
            </div>

            <p class="relative z-10 text-white/35 text-sm">© {{ date('Y') }} Pindoor · Valparaíso, Chile</p>
        </div>
    </x-slot>

    {{-- Banner mobile --}}
    <div class="lg:hidden -mx-8 -mt-8 mb-8 rounded-t-2xl overflow-hidden"
         style="background: linear-gradient(155deg, #7c3aed 0%, #6d28d9 45%, #5b21b6 100%)">
        <div class="px-8 py-8">
            <a href="/" class="inline-flex items-center mb-4">
                <span class="text-white font-bold text-2xl tracking-tight">Pin</span><span class="text-white/65 font-bold text-2xl tracking-tight">door</span>
            </a>
            <h2 class="text-white text-xl font-bold leading-snug mb-2">Tu arte, visible en La Escena</h2>
            <p class="text-white/80 text-sm leading-relaxed mb-4">Únete al directorio de artistas y agrupaciones culturales de Valparaíso.</p>
            <ul class="space-y-2">
                <li class="flex items-center gap-2 text-white/80 text-sm">
                    <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-white text-[10px] shrink-0">✓</span>
                    Perfil gratuito con tu disciplina y portafolio
                </li>
                <li class="flex items-center gap-2 text-white/80 text-sm">
                    <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-white text-[10px] shrink-0">✓</span>
                    Enlaza tus redes: Instagram, Spotify, YouTube
                </li>
                <li class="flex items-center gap-2 text-white/80 text-sm">
                    <span class="w-4 h-4 rounded-full bg-white/20 flex items-center justify-center text-white text-[10px] shrink-0">✓</span>
                    Contacto directo con quienes te quieran contratar
                </li>
            </ul>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Únete a La Escena</h2>
        <p class="text-sm text-gray-500 mt-1">Artistas y agrupaciones culturales · Pindoor</p>
    </div>

    <form method="POST" action="{{ route('artista.register.store') }}">
        @csrf
        <div style="display:none" aria-hidden="true">
            <input type="text" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div>
            <x-input-label for="name" value="Tu nombre artístico, real o de la agrupación" />
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

    <p class="mt-4 text-center text-sm text-gray-500">
        ¿Tienes un negocio o espacio?
        <a href="{{ route('register') }}" class="font-semibold text-[#fc5648] hover:underline">Regístralo aquí</a>
    </p>
    <p class="mt-2 text-center text-sm text-gray-500">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="font-semibold text-[#fc5648] hover:underline">Inicia sesión</a>
    </p>
</x-guest-layout>
