{{-- resources/views/noticias/index.blade.php --}}
    @php
        use Illuminate\Support\Str;
    @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Noticias') }}
        </h2>
    </x-slot>



    <div class="py-12" x-data="{ crearAbierto: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Botón crear --}}
                    <div class="mb-4 flex items-center justify-between">
                        <button @click="crearAbierto = !crearAbierto"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            <span x-show="!crearAbierto">{{ __('Crear Nueva Noticia') }}</span>
                            <span x-show="crearAbierto">{{ __('Cerrar formulario') }}</span>
                        </button>
                    </div>

                    {{-- Mensajes --}}
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
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
                        <form method="POST" action="{{ route('noticias.store') }}" enctype="multipart/form-data"
                              class="bg-gray-50 p-4 rounded border">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium mb-1">Título</label>
                                    <input name="titulo" value="{{ old('titulo') }}" required
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Fecha de publicación</label>
                                    <input type="date" name="fecha_publicacion" value="{{ old('fecha_publicacion') }}" required
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium mb-1">Resumen (opcional)</label>
                                    <input name="resumen" value="{{ old('resumen') }}"
                                           class="w-full border rounded px-3 py-2">
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium mb-1">Cuerpo</label>
                                    <textarea name="cuerpo" rows="5" required
                                              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">{{ old('cuerpo') }}</textarea>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium mb-1">Imagen (opcional)</label>
                                    <input type="file" name="imagen"
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                </div>
                            </div>

                            <div class="mt-4">
                                <button class="bg-[#fc5648] hover:opacity-90 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Guardar') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Listado con edición inline --}}
                    <ul class="space-y-2">
                        @forelse ($noticias as $n)
                            <li class="bg-gray-100 rounded-md p-4" x-data="{ edit:false }">
                                {{-- Vista lectura --}}
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4" x-show="!edit">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <h3 class="font-semibold text-lg">{{ $n->titulo }}</h3>
                                            <span class="text-sm text-gray-600">
                                                {{ $n->fecha_publicacion?->format('Y-m-d') }}
                                            </span>
                                        </div>

                                        @if($n->resumen)
                                            <p class="text-gray-700 mt-1">{{ Str::limit($n->resumen, 140) }}</p>
                                        @endif

                                        <p class="text-gray-600 mt-1">
                                            {!! nl2br(e(Str::limit($n->cuerpo, 220))) !!}
                                        </p>

                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button @click="edit=true"
                                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            {{ __('Editar') }}
                                        </button>
                                        <form action="{{ route('noticias.destroy', $n) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm"
                                                    onclick="return confirm('{{ __('¿Estás seguro de eliminar esta noticia?') }}')">
                                                {{ __('Eliminar') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Form edición inline --}}
                                <div x-show="edit" x-transition>
                                    <form action="{{ route('noticias.update', $n) }}" method="POST" enctype="multipart/form-data"
                                          class="bg-white p-4 rounded border">
                                        @csrf @method('PUT')

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium mb-1">Título</label>
                                                <input name="titulo" value="{{ old('titulo', $n->titulo) }}" required
                                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium mb-1">Fecha de publicación</label>
                                                <input type="date" name="fecha_publicacion"
                                                       value="{{ old('fecha_publicacion', optional($n->fecha_publicacion)->format('Y-m-d')) }}"
                                                       required class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                            </div>

                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium mb-1">Resumen (opcional)</label>
                                                <input name="resumen" value="{{ old('resumen', $n->resumen) }}"
                                                       class="w-full border rounded px-3 py-2">
                                            </div>

                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium mb-1">Cuerpo</label>
                                                <textarea name="cuerpo" rows="5" required
                                                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">{{ old('cuerpo', $n->cuerpo) }}</textarea>
                                            </div>

                                            <div class="md:col-span-3">
                                                <label class="block text-sm font-medium mb-1">Imagen (reemplazar)</label>
                                                <input type="file" name="imagen"
                                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring">
                                                @if($n->imagen)
                                                    <img src="{{ asset('storage/' .$n->imagen) }}" alt="Imagen actual"
                                                         class="mt-2 w-28 h-28 object-cover rounded">
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-4 flex gap-2">
                                            <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
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
                            <li class="text-gray-500">{{ __('No hay noticias creadas aún.') }}</li>
                        @endforelse
                    </ul>

                    <div class="mt-4">
                        {{ $noticias->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine para edición inline si no está ya en tu layout --}}
    <script src="//unpkg.com/alpinejs" defer></script>
</x-app-layout>
