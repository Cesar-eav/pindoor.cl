<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Te invitaron a Pindoor</h2>
        <p class="text-sm text-gray-500 mt-1">
            Únete a <span class="font-semibold text-gray-700">{{ $invitacion->artista->nombre }}</span> para ayudar a administrar su perfil.
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
            Ya existe una cuenta Pindoor con el correo <strong>{{ $invitacion->email }}</strong>.
            Inicia sesión con esa cuenta y luego vuelve a abrir este mismo link para unirte.
        </p>
        <a href="{{ route('login') }}" class="inline-block bg-[#fc5648] hover:opacity-90 text-white px-6 py-2.5 rounded-xl font-bold text-sm transition">
            Iniciar sesión
        </a>
    @else
        <form method="POST" action="{{ route('artista.invitacion.crear-cuenta', $invitacion->token) }}">
            @csrf

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full bg-gray-50" type="email" :value="$invitacion->email" :disabled="true" />
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
                <x-primary-button>Crear cuenta y unirme</x-primary-button>
            </div>
        </form>
    @endif
</x-guest-layout>
