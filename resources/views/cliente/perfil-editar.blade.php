<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('cliente.perfil') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Volver</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar mi perfil</h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-5 py-3 font-medium">
                    ✓ {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cliente.perfil.actualizar') }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- Info básica --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-bold mb-4">Información del negocio</p>

                        {{-- Solo lectura: título y ubicación (los controla el admin) --}}
                        <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-4 mb-5">
                            <p class="text-xs text-gray-400 mb-1">Gestionado por el administrador</p>
                            <div class="font-semibold text-gray-700">{{ $punto->title }}</div>
                            <div class="text-sm text-gray-400">
                                {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                                &middot; 📍 {{ $punto->sector }}
                                &middot; {{ $punto->direccion }}
                            </div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div>
                        <x-input-label for="description" value="Descripción *" />
                        <div id="description-editor"
                             class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-40"></div>
                        <textarea id="description" name="description" class="hidden">{{ old('description', $punto->description) }}</textarea>
                    </div>

                    {{-- Horario --}}
                    <div>
                        <x-input-label for="horario" value="Horario de atención" />
                        <x-text-input id="horario" name="horario" class="block mt-1 w-full"
                                      value="{{ old('horario', $punto->horario) }}"
                                      placeholder="Ej: Lun–Vie 09:00–20:00, Sáb 10:00–14:00" />
                    </div>

                    {{-- Enlace --}}
                    <div>
                        <x-input-label for="enlace" value="Web o Instagram" />
                        <x-text-input id="enlace" name="enlace" type="url" class="block mt-1 w-full"
                                      value="{{ old('enlace', $punto->enlace) }}"
                                      placeholder="https://instagram.com/minegocio" />
                    </div>

                    {{-- Tags --}}
                    <div>
                        <x-input-label for="tags" value="Etiquetas (separadas por coma)" />
                        <x-text-input id="tags" name="tags" class="block mt-1 w-full"
                                      value="{{ old('tags', is_array($punto->tags) ? implode(', ', $punto->tags) : '') }}"
                                      placeholder="café, vegano, terraza, wifi" />
                        <p class="text-xs text-gray-400 mt-1">Estas etiquetas aparecen en tu ficha pública.</p>
                    </div>

                    {{-- Video YouTube --}}
                    <div>
                        <x-input-label for="video_url" value="Video de YouTube" />
                        <x-text-input id="video_url" name="video_url" type="url" class="block mt-1 w-full"
                                      value="{{ old('video_url', $punto->video_url) }}"
                                      placeholder="https://www.youtube.com/watch?v=..." />
                        <p class="text-xs text-gray-400 mt-1">Se mostrará incrustado en tu ficha pública.</p>
                    </div>
                </div>

                {{-- Imagen de perfil --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-4">Logo / Imagen de perfil</p>

                    <div class="flex items-center gap-5">
                        @if($punto->imagen_perfil)
                            <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                 alt="Logo actual"
                                 class="w-20 h-20 rounded-2xl object-cover border border-gray-100 shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center shrink-0 text-3xl">
                                🏪
                            </div>
                        @endif

                        <div class="flex-1">
                            <input type="file" name="imagen_perfil" id="imagen_perfil"
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   class="block w-full text-sm text-gray-500
                                          file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                          file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700
                                          hover:file:bg-gray-200 cursor-pointer" />
                            <p class="text-xs text-gray-400 mt-2">JPG, PNG o WEBP. Máximo 2 MB.</p>
                        </div>
                    </div>
                </div>

                {{-- Carta / Menú (si módulo habilitado) --}}
                @if(in_array('carta', $modulos))
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-bold mb-1">Carta / Menú</p>
                        <p class="text-xs text-gray-400 mb-4">Se mostrará en tu ficha pública con un botón "Ver carta".</p>
                    </div>

                    <div>
                        <x-input-label for="carta" value="Descripción de la carta (texto libre)" />
                        {{-- Editor Quill --}}
                        <div id="carta-editor"
                             class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-55"></div>
                        {{-- Textarea oculto que se envía con el form --}}
                        <textarea id="carta" name="carta" class="hidden">{{ old('carta', $datoCarta['texto'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <x-input-label for="carta_pdf" value="Carta en PDF (opcional)" />
                        @if($datoCarta['pdf_ruta'] ?? null)
                            <div class="flex items-center gap-3 mt-1 mb-2">
                                <a href="{{ asset('storage/' . $datoCarta['pdf_ruta']) }}" target="_blank"
                                   class="text-xs text-pindoor-accent font-bold hover:underline flex items-center gap-1">
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
                        <p class="text-xs text-gray-400 mt-1">Solo PDF. Máximo 5 MB.</p>
                    </div>
                </div>
                @endif

                {{-- ALOJAMIENTO (secciones independientes por módulo) --}}
                @php
                    $tieneModuloAlojamiento = in_array('habitaciones', $modulos)
                                          || in_array('servicios', $modulos)
                                          || in_array('politicas', $modulos);
                @endphp
                @if($tieneModuloAlojamiento)
                @php $catalogoServicios = App\Models\PuntoInteres::catalogoServicios(); @endphp
                <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 p-6 space-y-6">
                    <div>
                        <p class="text-xs text-indigo-600 uppercase font-bold mb-1">Alojamiento</p>
                        <p class="text-xs text-gray-400">Información específica para huéspedes.</p>
                    </div>

                    {{-- Precio, check-in/out y habitaciones --}}
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
                        <textarea id="habitaciones" name="habitaciones" class="hidden">{{ old('habitaciones', $datoAlojamiento['habitaciones'] ?? '') }}</textarea>
                    </div>
                    @endif

                    {{-- Servicios incluidos --}}
                    @if(in_array('servicios', $modulos))
                    <div>
                        <x-input-label value="Servicios incluidos" />
                        <p class="text-xs text-gray-400 mt-1 mb-4">Selecciona todo lo que ofreces. Se mostrará agrupado en tu ficha pública.</p>
                        @php $seleccionados = old('servicios_incluidos', $datoAlojamiento['servicios'] ?? []); @endphp
                        <div class="space-y-4">
                            @foreach($catalogoServicios as $grupo => $servicios)
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $grupo }}</p>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-3">
                                    @foreach($servicios as $slug => $servicio)
                                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 hover:bg-indigo-50 border border-transparent hover:border-indigo-200 rounded-xl px-3 py-2 transition">
                                        <input type="checkbox"
                                               name="servicios_incluidos[]"
                                               value="{{ $slug }}"
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

                    {{-- Políticas --}}
                    @if(in_array('politicas', $modulos))
                    <div>
                        <x-input-label for="politicas" value="Políticas del establecimiento" />
                        <div id="politicas-editor"
                             class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-40"></div>
                        <textarea id="politicas" name="politicas" class="hidden">{{ old('politicas', $datoAlojamiento['politicas'] ?? '') }}</textarea>
                    </div>
                    @endif
                </div>
                @endif

                {{-- Perfil de búsqueda --}}
                <div id="busqueda" class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                    <p class="text-xs text-amber-700 uppercase font-bold mb-1">Perfil de búsqueda</p>
                    <p class="text-xs text-amber-600 mb-4">
                        Este texto <strong>no se muestra a los turistas</strong>, pero es clave para que te encuentren.
                        Describe todo lo que ofreces: tipos de café, comidas, opciones veganas/celíacas, ambiente, etc.
                    </p>
                    <textarea id="descripcion_busqueda" name="descripcion_busqueda" rows="6"
                              class="block w-full border-amber-200 bg-white rounded-xl shadow-sm text-sm focus:ring-amber-400 resize-none"
                              placeholder="Cafetería especialidad, V60, chemex, aeropress, capuchino, latte, cortado, leche de avena, leche de almendras, desayunos, tostadas, croissant, vegano, sin gluten, terraza exterior, perros permitidos, wifi gratis, enchufes, buena luz, trabajo remoto..."
                    >{{ old('descripcion_busqueda', $punto->descripcion_busqueda) }}</textarea>
                </div>

                {{-- Botones --}}
                <div class="flex justify-between items-center pb-4">
                    <a href="{{ route('cliente.perfil') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                        Guardar cambios
                    </button>
                </div>

            </form>
        </div>
    </div>

{{-- Quill rich text (todos los editores) --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
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

        const quill = new Quill(editorEl, {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });

        // Cargar contenido existente
        if (textarea.value.trim()) {
            quill.clipboard.dangerouslyPasteHTML(textarea.value);
        }

        // Sincronizar textarea inmediatamente y en cada cambio
        const sync = () => { textarea.value = quill.root.innerHTML; };
        sync();
        quill.on('text-change', sync);

        allEditors.push({ quill, textarea });
    }

    initEditor('#description-editor', '#description');
    initEditor('#carta-editor',       '#carta');
    initEditor('#habitaciones-editor','#habitaciones');
    initEditor('#politicas-editor',   '#politicas');

    // Backup: sincronizar justo antes de enviar
    document.querySelector('form').addEventListener('submit', () => {
        allEditors.forEach(({ quill, textarea }) => {
            textarea.value = quill.root.innerHTML;
        });
    });
});
</script>

</x-app-layout>
