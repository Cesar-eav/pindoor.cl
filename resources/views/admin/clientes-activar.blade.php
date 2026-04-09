<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Activar cliente &mdash; {{ $punto->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            {{-- Info del punto --}}
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-5 mb-6">
                <p class="text-xs text-gray-400 uppercase font-bold mb-2">Negocio a activar</p>
                <div class="text-gray-800 font-semibold text-lg">{{ $punto->title }}</div>
                <div class="text-sm text-gray-500 mt-1">
                    {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                    &middot; {{ $punto->sector }}
                </div>
            </div>

            {{-- Formulario credenciales --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="font-bold text-gray-700 mb-1">Crear credenciales de acceso</h3>
                <p class="text-xs text-gray-400 mb-6">
                    Se creará un usuario con rol <strong>cliente</strong> y se vinculará a este negocio.
                    El cliente solo podrá editar su propio perfil.
                </p>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3 mb-5">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.clientes.activar', $punto) }}">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <x-input-label for="name" value="Nombre del responsable *" />
                            <x-text-input id="name" name="name" class="block mt-1 w-full"
                                          value="{{ old('name') }}" required autofocus
                                          placeholder="Ej: María González" />
                        </div>

                        <div>
                            <x-input-label for="email" value="Email de acceso *" />
                            <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                                          value="{{ old('email') }}" required
                                          placeholder="hola@minegocio.cl" />
                            <p class="text-xs text-gray-400 mt-1">Con este email el cliente iniciará sesión.</p>
                        </div>

                        <div>
                            <x-input-label for="password" value="Contraseña temporal *" />
                            <x-text-input id="password" name="password" type="password" class="block mt-1 w-full"
                                          required autocomplete="new-password" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar contraseña *" />
                            <x-text-input id="password_confirmation" name="password_confirmation"
                                          type="password" class="block mt-1 w-full" required />
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-8">
                        <a href="{{ route('admin.clientes') }}"
                           class="text-sm text-gray-500 hover:text-gray-700">
                            &larr; Volver
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                            Activar cliente
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
