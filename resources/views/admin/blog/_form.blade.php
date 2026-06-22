@php $esEdicion = isset($post) && $post !== null; @endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      id="blog-form" class="space-y-6">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Título + Slug --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Título *</label>
            <input id="titulo" type="text" name="titulo" required
                   value="{{ old('titulo', $post?->titulo) }}"
                   placeholder="Ej: Los mejores rincones del cerro Alegre"
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold focus:ring-2 focus:ring-[#fc5648] outline-none">
        </div>
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                Slug (URL)
                <span class="normal-case font-normal text-gray-400 ml-1">— se genera automático desde el título</span>
            </label>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-400 shrink-0">/blog/</span>
                <input id="slug" type="text" name="slug"
                       value="{{ old('slug', $post?->slug) }}"
                       placeholder="mi-primer-post"
                       class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
            </div>
        </div>
    </div>

    {{-- Resumen --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
            Resumen
            <span class="normal-case font-normal text-gray-400 ml-1">— aparece en las tarjetas del blog</span>
        </label>
        <textarea id="resumen-input" name="resumen" rows="3" maxlength="600"
                  placeholder="Una breve introducción que invite a leer el post completo..."
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ old('resumen', $post?->resumen) }}</textarea>
        <div class="flex justify-between mt-1.5 text-[11px] text-gray-400">
            <span id="resumen-palabras">0 palabras</span>
            <span id="resumen-chars" class="font-semibold">0 / 600</span>
        </div>
    </div>

    {{-- Imagen portada --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Imagen de portada</label>

        @if($esEdicion && $post->imagen_portada_url)
        <div class="mb-4" id="portada-preview-actual">
            <img src="{{ $post->imagen_portada_url }}" alt="Portada actual"
                 class="w-full max-w-sm rounded-2xl object-cover aspect-video border border-gray-100">
            <p class="text-xs text-gray-400 mt-1">Portada actual — sube una nueva para reemplazarla</p>
        </div>
        @endif

        <div id="portada-preview-nueva" class="mb-4 hidden">
            <img id="portada-img-nueva" src="" alt="Nueva portada"
                 class="w-full max-w-sm rounded-2xl object-cover aspect-video border border-gray-100">
        </div>

        <input type="file" name="imagen_portada" id="imagen_portada" accept="image/*"
               class="block w-full text-sm text-gray-500
                      file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                      file:text-sm file:font-bold file:bg-[#fff0ef] file:text-[#fc5648]
                      hover:file:bg-[#ffe0dd] cursor-pointer">
    </div>

    {{-- Galería de imágenes (hasta 5) --}}
    @php
        $imagenesExistentes = $post?->imagenes ?? [];
        $slotsNuevos = max(0, 20 - count($imagenesExistentes));
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400">Imágenes del artículo</label>
            <span id="galeria-contador" class="text-xs font-bold text-gray-400">{{ count($imagenesExistentes) }}/20</span>
        </div>
        <p class="text-xs text-gray-400 mb-4">
            Sube hasta 20 fotos. Indica el número de párrafo tras el que debe aparecer, o déjalo vacío para distribución automática.
        </p>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4" id="galeria-grid">

            {{-- Imágenes existentes --}}
            @foreach($imagenesExistentes as $idx => $img)
            @php $ruta = is_array($img) ? $img['ruta'] : $img; $pos = is_array($img) ? ($img['posicion'] ?? '') : ''; @endphp
            <div id="existente-{{ $idx }}">
                <div class="relative aspect-square rounded-2xl overflow-hidden border-2 border-gray-100 bg-gray-50">
                    <img src="{{ asset('storage/' . $ruta) }}" alt="" class="w-full h-full object-cover">
                    <button type="button"
                            onclick="toggleEliminar({{ $idx }})"
                            id="btn-eliminar-{{ $idx }}"
                            class="absolute top-1.5 right-1.5 bg-red-500 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs font-bold shadow-md transition z-10">
                        ✕
                    </button>
                    <div id="overlay-eliminar-{{ $idx }}"
                         class="absolute inset-0 bg-red-500/70 hidden flex-col items-center justify-center gap-1 cursor-pointer"
                         onclick="toggleEliminar({{ $idx }})">
                        <span class="text-white font-black text-xs">BORRAR</span>
                        <span class="text-white/80 text-[10px]">clic para deshacer</span>
                    </div>
                    <input type="hidden" id="hidden-eliminar-{{ $idx }}" name="eliminar_imagen[]" value="{{ $idx }}" disabled>
                </div>
                <div id="pos-existente-{{ $idx }}" class="mt-1.5">
                    <p class="text-[10px] text-gray-400 font-semibold text-center mb-0.5">Tras párrafo nº</p>
                    <input type="number" min="1" max="99"
                           name="posicion_existente_{{ $idx }}"
                           value="{{ old('posicion_existente_' . $idx, $pos) }}"
                           placeholder="auto"
                           class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#fc5648] outline-none text-center">
                </div>
                <div id="label-eliminar-{{ $idx }}" class="mt-1.5 text-center hidden">
                    <span class="text-[10px] text-red-400 font-semibold">Se eliminará al guardar</span>
                </div>
            </div>
            @endforeach

            {{-- Slots para nuevas imágenes --}}
            @for($s = 1; $s <= $slotsNuevos; $s++)
            <div id="slot-{{ $s }}">
                <label class="relative aspect-square rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 hover:border-[#fc5648] hover:bg-[#fff8f7] transition cursor-pointer overflow-hidden block">
                    <img id="preview-{{ $s }}" src="" alt="" class="w-full h-full object-cover absolute inset-0 hidden">
                    <div id="placeholder-{{ $s }}" class="w-full h-full flex flex-col items-center justify-center gap-1">
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span class="text-[10px] text-gray-400 font-bold">Foto</span>
                    </div>
                    <button type="button" id="btn-limpiar-{{ $s }}"
                            onclick="limpiarSlot(event, {{ $s }})"
                            class="absolute top-1.5 right-1.5 bg-red-500 text-white rounded-full w-6 h-6 items-center justify-center text-xs font-bold shadow z-10 hidden">
                        ✕
                    </button>
                    <input type="file" id="slot-input-{{ $s }}" name="imagen_nueva_{{ $s }}"
                           accept="image/*"
                           class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                           onchange="previewSlot({{ $s }}, this)">
                </label>
                <div id="pos-nueva-{{ $s }}" class="mt-1.5 hidden">
                    <input type="number" min="1" max="99"
                           name="posicion_nueva_{{ $s }}"
                           placeholder="Párrafo (auto)"
                           class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#fc5648] outline-none text-center">
                </div>
            </div>
            @endfor

        </div>
    </div>

    <script>
    function toggleEliminar(idx) {
        var overlay  = document.getElementById('overlay-eliminar-' + idx);
        var btn      = document.getElementById('btn-eliminar-' + idx);
        var hidden   = document.getElementById('hidden-eliminar-' + idx);
        var posDiv   = document.getElementById('pos-existente-' + idx);
        var labelDiv = document.getElementById('label-eliminar-' + idx);
        var marcado  = overlay.classList.contains('hidden');
        overlay.classList.toggle('hidden', !marcado);
        overlay.classList.toggle('flex', marcado);
        btn.classList.toggle('hidden', marcado);
        hidden.disabled = !marcado;
        posDiv.classList.toggle('hidden', marcado);
        labelDiv.classList.toggle('hidden', !marcado);
    }

    function previewSlot(slot, input) {
        var file = input.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-' + slot).src = e.target.result;
            document.getElementById('preview-' + slot).classList.remove('hidden');
            document.getElementById('placeholder-' + slot).classList.add('hidden');
            document.getElementById('btn-limpiar-' + slot).classList.remove('hidden');
            document.getElementById('btn-limpiar-' + slot).classList.add('flex');
            document.getElementById('pos-nueva-' + slot).classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    function limpiarSlot(event, slot) {
        event.preventDefault();
        var input = document.getElementById('slot-input-' + slot);
        input.value = '';
        try { input.files = new DataTransfer().files; } catch(e) {}
        document.getElementById('preview-' + slot).classList.add('hidden');
        document.getElementById('placeholder-' + slot).classList.remove('hidden');
        document.getElementById('btn-limpiar-' + slot).classList.add('hidden');
        document.getElementById('btn-limpiar-' + slot).classList.remove('flex');
        document.getElementById('pos-nueva-' + slot).classList.add('hidden');
        document.getElementById('pos-nueva-' + slot).querySelector('input').value = '';
    }
    </script>

    {{-- Contenido Quill --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-3">
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400">Contenido</label>
            <div class="flex items-center gap-3 text-[11px] text-gray-400">
                <span id="contenido-palabras">0 palabras</span>
                <span id="contenido-chars" class="font-semibold">0 / 10 000</span>
            </div>
        </div>

        <div id="blog-editor" class="min-h-100"></div>
        <div id="contenido-limite-aviso" class="hidden mt-2 text-xs text-red-500 font-semibold">
            Límite de 10 000 caracteres alcanzado.
        </div>

        <textarea id="contenido-hidden" name="contenido" class="hidden">{{ old('contenido', $post?->contenido) }}</textarea>
    </div>

    {{-- Publicar + Guardar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center justify-between gap-4">
        <label class="flex items-center gap-3 cursor-pointer select-none">
            <div class="relative">
                <input type="checkbox" name="publicado" id="publicado" value="1" class="sr-only peer"
                       {{ old('publicado', $post?->publicado) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 rounded-full peer
                            peer-checked:bg-[#fc5648] transition-colors duration-200"></div>
                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow
                            peer-checked:translate-x-5 transition-transform duration-200"></div>
            </div>
            <span class="text-sm font-bold text-gray-700" id="publicado-label">
                {{ old('publicado', $post?->publicado) ? 'Publicado' : 'Borrador' }}
            </span>
        </label>

        <button type="submit"
                class="bg-gray-900 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-black transition">
            {{ $esEdicion ? 'Guardar cambios' : 'Crear post' }}
        </button>
    </div>
</form>
