<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('cliente.perfil.ver', $punto) }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Volver</a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar perfil</h2>
            </div>
            <button form="form-perfil" type="submit"
                    class="px-5 py-2 bg-[#fc5648] text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                Guardar cambios
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-5 py-3 font-medium">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            {{-- Quick-nav --}}
            @php
                $tieneModuloAlojamiento = in_array('habitaciones', $modulos)
                                       || in_array('servicios', $modulos)
                                       || in_array('politicas', $modulos);
            @endphp
            <div class="sticky top-0 z-20 -mx-4 sm:mx-0 bg-white/90 backdrop-blur border-b border-gray-200 mb-6">
                <div class="overflow-x-auto px-4 sm:px-0">
                    <div class="flex gap-1.5 py-2 min-w-max">
                        <a href="#descripcion" class="nav-pill">📝 Descripción</a>
                        @if(in_array('carta', $modulos))
                        <a href="#seccion-carta" class="nav-pill">🍽️ Carta</a>
                        @endif
                        @if($tieneModuloAlojamiento)
                        <a href="#alojamiento" class="nav-pill">🛏️ Alojamiento</a>
                        @endif
                        <a href="#busqueda" class="nav-pill">🔍 Búsqueda</a>
                    </div>
                </div>
            </div>

            <form id="form-perfil" method="POST" action="{{ route('cliente.perfil.actualizar', $punto) }}"
                  enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="space-y-6">

                    {{-- FILA 1: Descripción (2/3) + Logo & ficha (1/3) --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                        {{-- Descripción --}}
                        <div id="descripcion" class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-blue-100 p-6 scroll-mt-20">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="w-1 h-5 rounded-full bg-blue-400 shrink-0"></span>
                                <p class="text-xs text-blue-600 uppercase font-bold tracking-wider">Descripción del espacio</p>
                            </div>
                            <div id="description-editor"
                                 class="bg-white border border-gray-200 rounded-xl text-sm min-h-44"></div>
                            <textarea id="description" name="description" class="hidden">{!! old('description', $punto->description) !!}</textarea>
                        </div>

                        {{-- Sidebar: ficha + logo --}}
                        <div class="space-y-4">
                            {{-- Info fija --}}
                            <div class="bg-gray-50 border border-dashed border-gray-200 rounded-2xl p-4">
                                <p class="text-xs text-gray-400 mb-2">Gestionado por el administrador</p>
                                <p class="font-semibold text-gray-700 text-sm">{{ $punto->title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                                    @if($punto->sector) &middot; 📍 {{ $punto->sector }} @endif
                                </p>
                            </div>

                            {{-- Imagen de perfil --}}
                            <div class="bg-white rounded-2xl shadow-sm border border-violet-100 p-5"
                                 x-data="{ preview: '{{ $punto->imagen_perfil ? asset('storage/' . $punto->imagen_perfil) : (auth()->user()->imagen_logo ? asset('storage/' . auth()->user()->imagen_logo) : '') }}' }">
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="w-1 h-5 rounded-full bg-violet-400 shrink-0"></span>
                                    <p class="text-xs text-violet-600 uppercase font-bold tracking-wider">Logo / Imagen de perfil</p>
                                </div>
                                <div class="flex items-center gap-4 mb-3">
                                    <template x-if="preview">
                                        <img :src="preview" alt="Logo" class="w-16 h-16 rounded-2xl object-cover border border-gray-100 shrink-0">
                                    </template>
                                    <template x-if="!preview">
                                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center shrink-0 text-2xl">🏪</div>
                                    </template>
                                    <p class="text-xs text-gray-400 leading-relaxed">JPG, PNG o WEBP<br>Máx. 2 MB</p>
                                </div>
                                <input type="file" name="imagen_perfil" id="imagen_perfil"
                                       accept="image/jpeg,image/png,image/jpg,image/webp"
                                       @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700
                                              hover:file:bg-gray-200 cursor-pointer" />
                            </div>
                        </div>
                    </div>

                    {{-- FILA 2: Ubicación | Contacto & medios | Búsqueda (3 columnas iguales) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">

                        {{-- Ubicación --}}
                        <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-5 space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="w-1 h-5 rounded-full bg-emerald-500 shrink-0"></span>
                                <p class="text-xs text-emerald-700 uppercase font-bold tracking-wider">Ubicación</p>
                            </div>
                            <div>
                                <x-input-label for="horario" value="Horario de atención" />
                                <x-text-input id="horario" name="horario" class="block mt-1 w-full"
                                              value="{{ old('horario', $punto->horario) }}"
                                              placeholder="Lun–Vie 09:00–20:00" />
                            </div>
                            <div>
                                <x-input-label for="sector" value="Sector / Cerro" />
                                @include('admin.partials._sector-select', ['selected' => old('sector', $punto->sector)])
                            </div>
                            <div>
                                <x-input-label for="direccion" value="Dirección" />
                                <x-text-input id="direccion" name="direccion" class="block mt-1 w-full"
                                              value="{{ old('direccion', $punto->direccion) }}"
                                              placeholder="Calle Example 123, Valparaíso" />
                            </div>
                        </div>

                        {{-- Contacto & medios --}}
                        <div class="bg-sky-50 rounded-2xl border border-sky-200 p-5 space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="w-1 h-5 rounded-full bg-sky-500 shrink-0"></span>
                                <p class="text-xs text-sky-700 uppercase font-bold tracking-wider">Contacto & medios</p>
                            </div>
                            <div>
                                <x-input-label for="enlace" value="Web o Instagram" />
                                <x-text-input id="enlace" name="enlace" type="url" class="block mt-1 w-full"
                                              value="{{ old('enlace', $punto->enlace) }}"
                                              placeholder="https://instagram.com/minegocio" />
                            </div>
                            <div>
                                <x-input-label for="tags" value="Etiquetas (separadas por coma)" />
                                <x-text-input id="tags" name="tags" class="block mt-1 w-full"
                                              value="{{ old('tags', is_array($punto->tags) ? implode(', ', $punto->tags) : '') }}"
                                              placeholder="café, vegano, terraza, wifi" />
                                <p class="text-xs text-gray-500 mt-1">Aparecen en tu ficha pública.</p>
                            </div>
                            <div>
                                <x-input-label for="video_url" value="Video de YouTube" />
                                <x-text-input id="video_url" name="video_url" type="url" class="block mt-1 w-full"
                                              value="{{ old('video_url', $punto->video_url) }}"
                                              placeholder="https://www.youtube.com/watch?v=..." />
                                <p class="text-xs text-gray-500 mt-1">Se incrusta en tu ficha pública.</p>
                            </div>
                        </div>

                        {{-- Búsqueda --}}
                        <div id="busqueda" class="bg-amber-50 border border-amber-200 rounded-2xl p-5 space-y-3 scroll-mt-20">
                            <div class="flex items-center gap-2">
                                <span class="w-1 h-5 rounded-full bg-amber-400 shrink-0"></span>
                                <p class="text-xs text-amber-700 uppercase font-bold tracking-wider">Perfil de búsqueda</p>
                            </div>
                            <p class="text-xs text-amber-600">
                                <strong>No visible para turistas</strong>, pero clave para que te encuentren.
                                Describe todo: tipo de café, comidas, ambiente, opciones especiales, etc.
                            </p>
                            <textarea id="descripcion_busqueda" name="descripcion_busqueda" rows="8"
                                      class="block w-full border-amber-200 bg-white rounded-xl shadow-sm text-sm focus:ring-amber-400 resize-none"
                                      placeholder="Cafetería especialidad, V60, chemex, leche de avena, desayunos, vegano, sin gluten, terraza, perros permitidos, wifi, trabajo remoto..."
                            >{{ old('descripcion_busqueda', $punto->descripcion_busqueda) }}</textarea>
                        </div>
                    </div>

                    {{-- FILA 3: Carta (ancho completo, condicional) --}}
                    @if(in_array('carta', $modulos))
                    <div id="seccion-carta" class="bg-white rounded-2xl shadow-sm border border-orange-100 p-6 space-y-5 scroll-mt-20">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-1 h-5 rounded-full bg-orange-400 shrink-0"></span>
                                <p class="text-xs text-orange-600 uppercase font-bold tracking-wider">Carta / Menú</p>
                            </div>
                            <p class="text-xs text-gray-400 ml-3">Se muestra en tu ficha pública con un botón "Ver carta".</p>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="carta" value="Descripción de la carta (texto libre)" />
                                <div id="carta-editor"
                                     class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-44"></div>
                                <textarea id="carta" name="carta" class="hidden">{!! old('carta', $datoCarta['texto'] ?? '') !!}</textarea>
                            </div>
                            <div>
                                <x-input-label for="carta_pdf" value="Carta en PDF (opcional)" />
                                @if($datoCarta['pdf_ruta'] ?? null)
                                    <div class="flex items-center gap-3 mt-1 mb-2">
                                        <a href="{{ asset('storage/' . $datoCarta['pdf_ruta']) }}" target="_blank"
                                           class="text-xs text-[#fc5648] font-bold hover:underline">
                                            📄 Ver carta actual
                                        </a>
                                        <label class="flex items-center gap-1 text-xs text-gray-400 cursor-pointer">
                                            <input type="checkbox" name="eliminar_carta_pdf" value="1" class="rounded"> Eliminar PDF
                                        </label>
                                    </div>
                                @endif
                                <input type="file" name="carta_pdf" id="carta_pdf" accept="application/pdf"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                              file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700
                                              hover:file:bg-gray-200 cursor-pointer mt-1" />
                                <p class="text-xs text-gray-400 mt-1">Solo PDF · Máx. 5 MB.</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- FILA 4: Alojamiento (ancho completo, condicional) --}}
                    @if($tieneModuloAlojamiento)
                    @php $catalogoServicios = App\Models\PuntoInteres::catalogoServicios(); @endphp
                    <div id="alojamiento" class="bg-white rounded-2xl shadow-sm border border-indigo-100 p-6 space-y-6 scroll-mt-20">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="w-1 h-5 rounded-full bg-indigo-400 shrink-0"></span>
                                <p class="text-xs text-indigo-600 uppercase font-bold tracking-wider">Alojamiento</p>
                            </div>
                            <p class="text-xs text-gray-400 ml-3">Información específica para huéspedes.</p>
                        </div>

                        @if(in_array('habitaciones', $modulos))
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <x-input-label for="precio_desde" value="Precio desde" />
                                <x-text-input id="precio_desde" name="precio_desde" class="block mt-1 w-full"
                                              value="{{ old('precio_desde', $datoAlojamiento['precio_desde'] ?? '') }}"
                                              placeholder="Ej: $15.000 / noche" />
                            </div>
                            <div>
                                <x-input-label for="check_in" value="Check-in" />
                                <x-text-input id="check_in" name="check_in" class="block mt-1 w-full"
                                              value="{{ old('check_in', $datoAlojamiento['entrada'] ?? '') }}"
                                              placeholder="14:00" />
                            </div>
                            <div>
                                <x-input-label for="check_out" value="Check-out" />
                                <x-text-input id="check_out" name="check_out" class="block mt-1 w-full"
                                              value="{{ old('check_out', $datoAlojamiento['salida'] ?? '') }}"
                                              placeholder="11:00" />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="habitaciones" value="Habitaciones disponibles" />
                            <div id="habitaciones-editor"
                                 class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-40"></div>
                            <textarea id="habitaciones" name="habitaciones" class="hidden">{!! old('habitaciones', $datoAlojamiento['habitaciones'] ?? '') !!}</textarea>
                        </div>
                        @endif

                        @if(in_array('servicios', $modulos))
                        <div>
                            <x-input-label value="Servicios incluidos" />
                            <p class="text-xs text-gray-400 mt-1 mb-4">Selecciona todo lo que ofreces.</p>
                            @php $seleccionados = old('servicios_incluidos', $datoAlojamiento['servicios'] ?? []); @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                                @foreach($catalogoServicios as $grupo => $servicios)
                                <div class="border border-gray-200 rounded-xl overflow-hidden">
                                    <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $grupo }}</p>
                                    </div>
                                    <div class="grid grid-cols-1 gap-1 p-2">
                                        @foreach($servicios as $slug => $servicio)
                                        <label class="flex items-center gap-2 cursor-pointer bg-gray-50 hover:bg-indigo-50 border border-transparent hover:border-indigo-200 rounded-xl px-3 py-2 transition">
                                            <input type="checkbox" name="servicios_incluidos[]" value="{{ $slug }}"
                                                   @checked(in_array($slug, $seleccionados))
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                            <span class="text-lg">{{ $servicio['emoji'] }}</span>
                                            <span class="text-xs font-medium text-gray-700">{{ $servicio['label'] }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if(in_array('politicas', $modulos))
                        <div>
                            <x-input-label for="politicas" value="Políticas del establecimiento" />
                            <div id="politicas-editor"
                                 class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-40"></div>
                            <textarea id="politicas" name="politicas" class="hidden">{!! old('politicas', $datoAlojamiento['politicas'] ?? '') !!}</textarea>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- Botón guardar --}}
                    <div class="flex justify-end pb-4">
                        <button type="submit"
                                class="px-8 py-2.5 bg-[#fc5648] text-white text-sm font-bold rounded-xl hover:opacity-90 transition shadow-lg">
                            Guardar cambios
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

@vite('resources/js/quill-editor.js')
<style>
    .nav-pill {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.5rem;
        background: white;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        white-space: nowrap;
        transition: border-color 0.15s, color 0.15s;
        text-decoration: none;
    }
    .nav-pill:hover { border-color: #fc5648; color: #fc5648; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toolbarOptions = [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'header': [2, 3, false] }],
        ['clean']
    ];

    const allEditors = [];

    function initEditor(editorId, textareaId) {
        const editorEl = document.querySelector(editorId);
        const textarea = document.querySelector(textareaId);
        if (!editorEl || !textarea) return;
        const quill = new Quill(editorEl, { theme: 'snow', modules: { toolbar: toolbarOptions } });
        if (textarea.value.trim()) quill.clipboard.dangerouslyPasteHTML(textarea.value);
        quill.on('text-change', () => { textarea.value = quill.root.innerHTML; });
        allEditors.push({ quill, textarea });
    }

    initEditor('#description-editor', '#description');
    initEditor('#carta-editor',        '#carta');
    initEditor('#habitaciones-editor', '#habitaciones');
    initEditor('#politicas-editor',    '#politicas');

    document.getElementById('form-perfil').addEventListener('submit', () => {
        allEditors.forEach(({ quill, textarea }) => { textarea.value = quill.root.innerHTML; });
    });
});
</script>

</x-app-layout>
