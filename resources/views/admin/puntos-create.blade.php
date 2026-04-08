<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Puntos Públicos') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ view: 'listado' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex mb-6 bg-gray-200 p-1 rounded-xl w-fit">
                <button
                    @click="view = 'crear'"
                    :class="view === 'crear' ? 'bg-white shadow-sm text-pindoor-accent' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-2 rounded-lg font-bold text-sm transition-all">
                    + Crear Nuevo
                </button>
                <button
                    @click="view = 'listado'"
                    :class="view === 'listado' ? 'bg-white shadow-sm text-pindoor-accent' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-2 rounded-lg font-bold text-sm transition-all">
                    Ver Listado
                </button>
            </div>

            {{-- FORMULARIO CREAR --}}
            <div x-show="view === 'crear'" x-cloak x-transition>
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 max-w-4xl mx-auto">
                    <h3 class="text-lg font-bold mb-1">Información del Nuevo Punto Público</h3>
                    <p class="text-xs text-gray-400 mb-6"><span class="text-red-500">*</span> Campo obligatorio</p>

                    <form action="{{ route('admin.puntos.store') }}" id="main-form" onsubmit="return false;">
                        @csrf

                        {{-- Fila 1: Nombre + Categoría --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre del Punto <span class="text-red-500">*</span>
                                </label>
                                <x-text-input id="title" name="title" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Categoría <span class="text-red-500">*</span>
                                </label>
                                <select name="categoria_id" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent">
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->icono }} {{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fila 2: Autor + Tags --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="autor" value="Autor / Artista / Institución" />
                                <x-text-input id="autor" name="autor" class="block mt-1 w-full" placeholder="Ej: Municipalidad de Valparaíso" />
                            </div>
                            <div>
                                <x-input-label for="tags" value="Etiquetas (separadas por coma)" />
                                <x-text-input id="tags" name="tags" class="block mt-1 w-full" placeholder="vista, historia, arte" />
                            </div>
                        </div>

                        {{-- Fila 3: Sector + Dirección --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="sector" value="Sector / Cerro" />
                                @include('admin.partials._sector-select')
                            </div>
                            <div>
                                <x-input-label for="direccion" value="Dirección" />
                                <x-text-input id="direccion" name="direccion" class="block mt-1 w-full" placeholder="Ej: Pasaje Gálvez 214, Cerro Alegre" />
                            </div>
                        </div>

                        {{-- Fila 4: Horario + Enlace --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="horario" value="Horario de visita" />
                                <x-text-input id="horario" name="horario" class="block mt-1 w-full" placeholder="Ej: Lun–Vie 09:00–18:00" />
                            </div>
                            <div>
                                <x-input-label for="enlace" value="Sitio web o Instagram" />
                                <x-text-input id="enlace" name="enlace" type="url" class="block mt-1 w-full" placeholder="https://..." />
                            </div>
                        </div>

                        {{-- Video YouTube --}}
                        <div class="mb-6">
                            <x-input-label for="video_url" value="Video de YouTube" />
                            <x-text-input id="video_url" name="video_url" type="url" class="block mt-1 w-full" placeholder="https://www.youtube.com/watch?v=..." />
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Reseña o descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" rows="4" required
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent focus:border-pindoor-accent"></textarea>
                        </div>

                        {{-- Mapa + Galería (Vue) --}}
                        <div id="app" class="mb-6">
                            <x-input-label value="Ubicación en el mapa" />
                            <p class="text-xs text-gray-400 mb-2">Haz clic en el mapa o arrastra el marcador para ajustar la posición exacta.</p>
                            <selector-mapa></selector-mapa>
                            <div class="mt-6">
                                <x-input-label value="Fotografías" />
                                <p class="text-xs text-gray-400 mb-2">Sube al menos una foto. Puedes reordenarlas arrastrando.</p>
                                <galeria-subida></galeria-subida>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="button"
                                onclick="window.dispatchEvent(new CustomEvent('trigger-pindoor-submit'))"
                                class="bg-pindoor-accent text-white px-8 py-3 rounded-2xl font-bold shadow-lg hover:bg-red-600 transition">
                                Publicar Punto Público
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- LISTADO --}}
            <div x-show="view === 'listado'" x-cloak x-transition>
                <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-4">Punto</th>
                                <th class="px-6 py-4">Sector</th>
                                <th class="px-6 py-4">Categoría</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($puntos as $punto)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $punto->title }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $punto->sector }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-[10px]">{{ $punto->category }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $punto->activo ? 'ACTIVO' : 'OCULTO' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.puntos.edit', $punto) }}" class="text-blue-600 font-bold hover:underline">Editar</a>
                                    <form action="{{ route('admin.puntos.toggle', $punto) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="font-bold {{ $punto->activo ? 'text-orange-600' : 'text-green-600' }} hover:underline">
                                            {{ $punto->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($puntos->isEmpty())
                        <div class="p-10 text-center text-gray-400 italic">No has creado puntos públicos todavía.</div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<style>
    [x-cloak] { display: none !important; }
</style>
