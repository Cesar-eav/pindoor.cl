<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar: {{ $punto->title }}
            </h2>
            <a href="{{ route('admin.puntos.create') }}" class="text-sm text-gray-500 hover:text-gray-800 transition">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-1">Información del Punto Público</h3>
                <p class="text-xs text-gray-400 mb-6"><span class="text-red-500">*</span> Campo obligatorio</p>

                <form id="main-form" onsubmit="return false;">
                    @csrf

                    {{-- Fila 1: Nombre + Categoría --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Punto <span class="text-red-500">*</span>
                            </label>
                            <x-text-input id="title" name="title" class="block mt-1 w-full" required value="{{ $punto->title }}" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Categoría <span class="text-red-500">*</span>
                            </label>
                            <select name="categoria_id" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent">
                                <option value="">Selecciona una categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" @selected($punto->categoria_id == $cat->id)>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Fila 2: Autor + Tags --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="autor" value="Autor / Artista / Institución" />
                            <x-text-input id="autor" name="autor" class="block mt-1 w-full"
                                placeholder="Ej: Municipalidad de Valparaíso" value="{{ $punto->autor }}" />
                        </div>
                        <div>
                            <x-input-label for="tags" value="Etiquetas (separadas por coma)" />
                            <x-text-input id="tags" name="tags" class="block mt-1 w-full"
                                placeholder="vista, historia, arte"
                                value="{{ is_array($punto->tags) ? implode(', ', $punto->tags) : $punto->tags }}" />
                        </div>
                    </div>

                    {{-- Fila 3: Sector + Dirección --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="sector" value="Sector / Cerro" />
                            @include('admin.partials._sector-select', ['selected' => $punto->sector])
                        </div>
                        <div>
                            <x-input-label for="direccion" value="Dirección" />
                            <x-text-input id="direccion" name="direccion" class="block mt-1 w-full"
                                placeholder="Ej: Pasaje Gálvez 214, Cerro Alegre" value="{{ $punto->direccion }}" />
                        </div>
                    </div>

                    {{-- Fila 4: Horario + Enlace --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="horario" value="Horario de visita" />
                            <x-text-input id="horario" name="horario" class="block mt-1 w-full"
                                placeholder="Ej: Lun–Vie 09:00–18:00" value="{{ $punto->horario }}" />
                        </div>
                        <div>
                            <x-input-label for="enlace" value="Sitio web o Instagram" />
                            <x-text-input id="enlace" name="enlace" type="url" class="block mt-1 w-full"
                                placeholder="https://..." value="{{ $punto->enlace }}" />
                        </div>
                    </div>

                    {{-- Video YouTube --}}
                    <div class="mb-6">
                        <x-input-label for="video_url" value="Video de YouTube" />
                        <x-text-input id="video_url" name="video_url" type="url" class="block mt-1 w-full"
                            placeholder="https://www.youtube.com/watch?v=..." value="{{ $punto->video_url }}" />
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Reseña o descripción <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description-input" name="description" class="hidden">{{ $punto->description }}</textarea>
                        <div id="description-editor"
                             class="bg-white border border-gray-300 rounded-lg"
                             style="min-height: 180px;"></div>
                    </div>

                    {{-- Perfil de búsqueda (admin) --}}
                    <div class="mb-6 p-4 rounded-xl border border-amber-200 bg-amber-50">
                        <label class="block text-sm font-semibold text-amber-900 mb-1">🔍 Palabras clave de búsqueda (uso interno)</label>
                        <p class="text-xs text-amber-700 mb-2">
                            Invisible para los turistas, pero se usa para que este punto aparezca en el buscador.
                            Es el equivalente al "Perfil de búsqueda" que rellena el cliente desde su panel, pero editable solo desde acá
                            — útil para puntos sin dueño real (básicos) o atractivos que administra Pindoor directamente.
                        </p>
                        <textarea name="descripcion_busqueda_admin" rows="4"
                                  class="block w-full border-amber-200 bg-white rounded-lg shadow-sm text-sm focus:ring-amber-400 resize-none"
                                  placeholder="Sinónimos, palabras que la gente buscaría, tipo de lugar, características…">{{ old('descripcion_busqueda_admin', $punto->descripcion_busqueda_admin) }}</textarea>
                    </div>

                    {{-- Mapa + Galería (Vue) --}}
                    @php
                        $imagenesJson = json_encode($punto->imagenes->map(function($img) {
                            return [
                                'id'           => $img->id,
                                'url'          => asset('storage/' . $img->ruta),
                                'es_principal' => $img->es_principal,
                                'orden'        => $img->orden,
                            ];
                        })->values()->all());
                    @endphp

                    <div id="app" class="mb-6">
                        <x-input-label value="Ubicación en el mapa" />
                        <p class="text-xs text-gray-400 mb-2">Escribe la dirección y el pin se ubica solo. Si no queda exacto, haz clic en el mapa o arrastra el marcador.</p>
                        <selector-mapa
                            :initial-lat="{{ $punto->lat ?? -33.0472 }}"
                            :initial-lng="{{ $punto->lng ?? -71.6297 }}"
                            geocode-url="{{ route('admin.geocodificar') }}"
                            reverse-geocode-url="{{ route('admin.geocodificar-inverso') }}"
                        ></selector-mapa>

                        <div class="mt-6">
                            <x-input-label value="Fotografías" />
                            <p class="text-xs text-gray-400 mb-2">Puedes reordenar arrastrando. La imagen marcada como Principal es la portada.</p>
                            <galeria-subida
                                :punto-id="{{ $punto->id }}"
                                :initial-images="{{ $imagenesJson }}"
                                endpoint="{{ route('admin.puntos.update', $punto) }}"
                            ></galeria-subida>
                        </div>
                    </div>

                    {{-- Perfil básico (sin dueño) --}}
                    <div class="mb-6 p-4 rounded-xl border border-gray-200 bg-gray-50">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="publicar_como_basico" value="1"
                                   {{ $punto->esBasico() ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-pindoor-accent focus:ring-pindoor-accent">
                            <div>
                                <span class="text-sm font-semibold text-gray-800">Publicar como perfil básico (sin dueño asignado)</span>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    El negocio aparece en Pindoor con información básica y podrá activar su perfil más adelante para desbloquear galería, menú, eventos y promociones.
                                    Si ya tiene un dueño real asignado, desmarcar esta opción no le quita su cuenta.
                                </p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.puntos.create') }}"
                           class="px-6 py-3 rounded-2xl font-bold text-gray-600 hover:text-gray-900 transition">
                            Cancelar
                        </a>
                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('trigger-pindoor-submit'))"
                            class="bg-pindoor-accent text-white px-8 py-3 rounded-2xl font-bold shadow-lg hover:bg-red-600 transition">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>

@vite('resources/js/quill-editor.js')

<style>
    [x-cloak] { display: none !important; }
    #description-editor .ql-container { font-size: 14px; border-radius: 0 0 0.5rem 0.5rem; border-color: #d1d5db; }
    #description-editor .ql-toolbar { border-radius: 0.5rem 0.5rem 0 0; border-color: #d1d5db; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const quill = new Quill('#description-editor', {
        theme: 'snow',
        placeholder: 'Escribe una descripción del lugar…',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['clean']
            ]
        }
    });

    const hidden = document.getElementById('description-input');
    if (hidden.value) quill.root.innerHTML = hidden.value;

    window.addEventListener('trigger-pindoor-submit', () => {
        hidden.value = quill.root.innerHTML;
    }, true);
});
</script>
