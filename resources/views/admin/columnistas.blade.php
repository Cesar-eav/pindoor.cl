<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Columnistas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ crearAbierto: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Botón crear (toggle) --}}
                    <div class="mb-4 flex items-center justify-between">
                        <button @click="crearAbierto = !crearAbierto"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <span x-show="!crearAbierto">{{ __('Crear Nuevo Columnista') }}</span>
                            <span x-show="crearAbierto">{{ __('Cerrar formulario') }}</span>
                        </button>
                    </div>

                    {{-- Mensajes --}}
                    @if (session('success'))
                        <div class="bg-green-200 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <strong class="font-bold">{{ __('Éxito') }}!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Formulario de creación --}}
                    <div x-show="crearAbierto" x-transition class="mb-6">
                        <form action="{{ route('columnistas.store') }}" method="POST"  enctype="multipart/form-data"
                            class="bg-gray-50 p-4 rounded border">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Nombre</label>
                                    <input name="nombre" value="{{ old('nombre') }}" required
                                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Email (opcional)</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Revista (opcional)</label>
                                    <select name="revista_id" class="w-full border rounded px-3 py-2">
                                        <option value="">{{ __('— Sin asignar —') }}</option>
                                        @foreach ($revistas as $rev)
                                            <option value="{{ $rev->id }}" @selected(old('revista_id') == $rev->id)>
                                                {{ $rev->titulo ?? 'Edición ' . $rev->id }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Bio</label>
                                    <textarea name="bio" rows="2" class="w-full border rounded px-3 py-2">{{ old('bio') }}</textarea>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input id="crear_proximo" type="checkbox" name="participa_proximo_numero"
                                        value="1" class="h-4 w-4">
                                    <label for="crear_proximo" class="text-sm">Participa en próximo número</label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Foto</label>
                                <input type="file" name="foto"
                                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                            </div>

                            <div class="mt-4">
                                <button class="bg-[#fc5648] hover:opacity-90 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Guardar') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Listado + edición inline --}}
                    <ul class="space-y-2">
                        @forelse ($columnistas as $c)
                            <li class="bg-gray-100 rounded-md p-4" x-data="{ edit: false }">
                                {{-- Vista lectura --}}
                                <div class="flex justify-between items-center" x-show="!edit">
                                    <div class="space-y-1">
                                        <div class="font-semibold">
                                            {{ $c->nombre }}
                                            @if ($c->participa_proximo_numero)
                                                <span
                                                    class="ml-2 text-xs px-2 py-1 bg-green-100 text-green-700 rounded">Próximo
                                                    número</span>
                                            @endif
                                        </div>
                                        <div>
                                            @if($c->foto)
                                                <img src="{{ asset($c->foto) }}" alt="{{ $c->nombre }}" class="w-16 h-16 object-cover rounded-full shadow">
                                            @else
                                                <div class="w-16 h-16 flex items-center justify-center bg-gray-300 rounded-full text-gray-600">
                                                    <span class="text-xs">Sin foto</span>
                                                </div>
                                            @endif                                        
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            <span class="mr-2">{{ $c->email ?? '—' }}</span>
                                            <span class="text-gray-400">|</span>
                                            <span class="ml-2">{{ $c->revista->titulo ?? 'Sin revista' }}</span>
                                        </div>
                                        @if ($c->bio)
                                            <p class="text-sm text-gray-600 mt-1">{{ $c->bio }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="edit=true"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            {{ __('Editar') }}
                                        </button>
                                        <form action="{{ route('columnistas.destroy', $c) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm"
                                                onclick="return confirm('{{ __('¿Estás seguro de eliminar este columnista?') }}')">
                                                {{ __('Eliminar') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Form edición inline --}}
                                <div x-show="edit" x-transition>
                                    <form action="{{ route('columnistas.update', $c) }}" method="POST"
                                        class="bg-white p-4 rounded border">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium mb-1">Nombre</label>
                                                <input name="nombre" value="{{ old('nombre', $c->nombre) }}" required
                                                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1">Email</label>
                                                <input type="email" name="email"
                                                    value="{{ old('email', $c->email) }}"
                                                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1">Revista</label>
                                                <select name="revista_id" class="w-full border rounded px-3 py-2">
                                                    <option value="">{{ __('— Sin asignar —') }}</option>
                                                    @foreach ($revistas as $rev)
                                                        <option value="{{ $rev->id }}"
                                                            @selected(old('revista_id', $c->revista_id) == $rev->id)>
                                                            {{ $rev->titulo ?? 'Edición ' . $rev->id }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium mb-1">Bio</label>
                                                <textarea name="bio" rows="2" class="w-full border rounded px-3 py-2">{{ old('bio', $c->bio) }}</textarea>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input id="proximo_{{ $c->id }}" type="checkbox"
                                                    name="participa_proximo_numero" value="1"
                                                    @checked(old('participa_proximo_numero', $c->participa_proximo_numero)) class="h-4 w-4">
                                                <label for="proximo_{{ $c->id }}" class="text-sm">Participa en
                                                    próximo número</label>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-1">Foto</label>
                                            <input type="file" name="imagen"
                                                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                            @if ($c->imagen)
                                                <img src="{{ asset('storage/' . $c->imagen) }}"
                                                    alt="{{ $c->nombre }}"
                                                    class="mt-2 w-16 h-16 object-cover rounded-full">
                                            @endif
                                        </div>
                                        <div class="mt-4 flex gap-2">
                                            <button
                                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                                {{ __('Guardar cambios') }}
                                            </button>
                                            <button type="button" @click="edit=false"
                                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                                                {{ __('Cancelar') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="text-gray-500">{{ __('No hay columnistas creados aún.') }}</li>
                        @endforelse
                    </ul>

                    <div class="mt-4">
                        {{ $columnistas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
