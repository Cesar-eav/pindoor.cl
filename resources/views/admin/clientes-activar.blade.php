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
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3 mb-5">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                {{-- Opción A: vincular usuario existente --}}
                @if($usuariosSinPunto->isNotEmpty())
                <div x-data="{ modo: 'existente' }">
                    <div class="flex gap-2 mb-6">
                        <button type="button" @click="modo = 'existente'"
                                :class="modo === 'existente' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600'"
                                class="px-4 py-2 rounded-lg text-sm font-bold transition">
                            Vincular usuario existente
                        </button>
                        <button type="button" @click="modo = 'nuevo'"
                                :class="modo === 'nuevo' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600'"
                                class="px-4 py-2 rounded-lg text-sm font-bold transition">
                            Crear usuario nuevo
                        </button>
                    </div>

                    {{-- Formulario vincular existente --}}
                    <form x-show="modo === 'existente'" method="POST" action="{{ route('admin.clientes.activar', $punto) }}">
                        @csrf
                        <div>
                            <x-input-label for="user_id_existente" value="Seleccionar usuario cliente" />
                            <select name="user_id_existente" id="user_id_existente" required
                                    class="block mt-1 w-full border-gray-300 rounded-xl shadow-sm text-sm focus:ring-pindoor-accent">
                                <option value="">— Selecciona —</option>
                                @foreach($usuariosSinPunto as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-between items-center mt-6">
                            <a href="{{ route('admin.clientes') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver</a>
                            <button type="submit" class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                                Vincular y activar
                            </button>
                        </div>
                    </form>

                    {{-- Formulario crear nuevo --}}
                    <form x-show="modo === 'nuevo'" x-cloak method="POST" action="{{ route('admin.clientes.activar', $punto) }}">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <x-input-label for="name" value="Nombre del responsable *" />
                                <x-text-input id="name" name="name" class="block mt-1 w-full" value="{{ old('name') }}" placeholder="Ej: María González" />
                            </div>
                            <div>
                                <x-input-label for="email" value="Email de acceso *" />
                                <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" value="{{ old('email') }}" placeholder="hola@minegocio.cl" />
                            </div>
                            <div>
                                <x-input-label for="password" value="Contraseña temporal *" />
                                <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" autocomplete="new-password" />
                            </div>
                            <div>
                                <x-input-label for="password_confirmation" value="Confirmar contraseña *" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" />
                            </div>
                        </div>
                        <div class="flex justify-between items-center mt-6">
                            <a href="{{ route('admin.clientes') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver</a>
                            <button type="submit" class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                                Crear y activar
                            </button>
                        </div>
                    </form>
                </div>
                @else
                {{-- Solo crear nuevo --}}
                <form method="POST" action="{{ route('admin.clientes.activar', $punto) }}">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <x-input-label for="name" value="Nombre del responsable *" />
                            <x-text-input id="name" name="name" class="block mt-1 w-full" value="{{ old('name') }}" required autofocus placeholder="Ej: María González" />
                        </div>
                        <div>
                            <x-input-label for="email" value="Email de acceso *" />
                            <x-text-input id="email" name="email" type="email" class="block mt-1 w-full" value="{{ old('email') }}" required placeholder="hola@minegocio.cl" />
                        </div>
                        <div>
                            <x-input-label for="password" value="Contraseña temporal *" />
                            <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" required autocomplete="new-password" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar contraseña *" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" required />
                        </div>
                    </div>
                    <div class="flex justify-between items-center mt-8">
                        <a href="{{ route('admin.clientes') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver</a>
                        <button type="submit" class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                            Activar cliente
                        </button>
                    </div>
                </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
