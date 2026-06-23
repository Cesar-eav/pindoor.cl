@php $esEdicion = isset($post) && $post !== null; @endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      id="blog-form" class="space-y-6">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    {{-- Publicar + Guardar --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 flex items-center justify-between gap-4">
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

    @if($errors->any())
    <div class="w-[90vw] mx-auto bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Fila superior: Título+Slug · Resumen · Portada ─────────────── --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Título + Slug --}}
            <div class="space-y-4">
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
                        <span class="normal-case font-normal text-gray-400 ml-1">— automático desde el título</span>
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
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                    Resumen
                    <span class="normal-case font-normal text-gray-400 ml-1">— aparece en las tarjetas</span>
                </label>
                <textarea id="resumen-input" name="resumen" rows="12" maxlength="600"
                          placeholder="Una breve introducción que invite a leer el post completo..."
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ old('resumen', $post?->resumen) }}</textarea>
                <div class="flex justify-between mt-1.5 text-[11px] text-gray-400">
                    <span id="resumen-palabras">0 palabras</span>
                    <span id="resumen-chars" class="font-semibold">0 / 600</span>
                </div>
            </div>

            {{-- Imagen portada --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Imagen de portada</label>

                @if($esEdicion && $post->imagen_portada_url)
                <div class="mb-3" id="portada-preview-actual">
                    <img src="{{ $post->imagen_portada_url }}" alt="Portada actual"
                         class="w-full rounded-xl object-cover aspect-video border border-gray-100">
                    <p class="text-xs text-gray-400 mt-1">Portada actual — sube una nueva para reemplazarla</p>
                </div>
                @endif

                <div id="portada-preview-nueva" class="mb-3 hidden">
                    <img id="portada-img-nueva" src="" alt="Nueva portada"
                         class="w-full rounded-xl object-cover aspect-video border border-gray-100">
                </div>

                <input type="file" name="imagen_portada" id="imagen_portada" accept="image/*"
                       class="block w-full text-sm text-gray-500
                              file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                              file:text-sm file:font-bold file:bg-[#fff0ef] file:text-[#fc5648]
                              hover:file:bg-[#ffe0dd] cursor-pointer">
            </div>

        </div>
    </div>

    {{-- ── Bloque 3 columnas: Galería · Editor · Preview ─────────────── --}}
    @php
        $imagenesExistentes = $post?->imagenes ?? [];
        $slotsNuevos = max(0, 20 - count($imagenesExistentes));
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="grid grid-cols-1 lg:grid-cols-[210px_1fr_190px] gap-5 items-start">

            {{-- ── Columna izquierda: Galería ────────────────────────────── --}}
            <div class="lg:sticky lg:top-20 lg:max-h-[80vh] lg:overflow-y-auto lg:pr-1">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Imágenes</span>
                    <span id="galeria-contador" class="text-[10px] font-bold text-gray-400">{{ count($imagenesExistentes) }}/20</span>
                </div>
                <p class="text-[10px] text-gray-400 mb-3 leading-snug">
                    Hasta 20 fotos. El nº indica tras qué párrafo aparece, o vacío para automático.
                </p>

                <div class="grid grid-cols-2 gap-2" id="galeria-grid">

                    {{-- Imágenes existentes --}}
                    @foreach($imagenesExistentes as $idx => $img)
                    @php $ruta = is_array($img) ? $img['ruta'] : $img; $pos = is_array($img) ? ($img['posicion'] ?? '') : ''; @endphp
                    <div id="existente-{{ $idx }}">
                        <div class="relative aspect-square rounded-xl overflow-hidden border-2 border-gray-100 bg-gray-50">
                            <img src="{{ asset('storage/' . $ruta) }}" alt="" class="w-full h-full object-cover">
                            <button type="button" onclick="toggleEliminar({{ $idx }})"
                                    id="btn-eliminar-{{ $idx }}"
                                    class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-[10px] font-bold shadow transition z-10">✕</button>
                            <div id="overlay-eliminar-{{ $idx }}"
                                 class="absolute inset-0 bg-red-500/75 hidden flex-col items-center justify-center gap-0.5 cursor-pointer"
                                 onclick="toggleEliminar({{ $idx }})">
                                <span class="text-white font-black text-[10px]">BORRAR</span>
                                <span class="text-white/80 text-[9px]">clic p/deshacer</span>
                            </div>
                            <input type="hidden" id="hidden-eliminar-{{ $idx }}" name="eliminar_imagen[]" value="{{ $idx }}" disabled>
                        </div>
                        <div id="pos-existente-{{ $idx }}" class="mt-1">
                            <input type="number" min="1" max="99"
                                   name="posicion_existente_{{ $idx }}"
                                   value="{{ old('posicion_existente_' . $idx, $pos) }}"
                                   placeholder="párrafo (auto)"
                                   class="w-full px-1.5 py-1 text-[10px] border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#fc5648] outline-none text-center">
                        </div>
                        <div id="label-eliminar-{{ $idx }}" class="mt-1 text-center hidden">
                            <span class="text-[9px] text-red-400 font-semibold">Se eliminará</span>
                        </div>
                    </div>
                    @endforeach

                    {{-- Slots nuevas imágenes --}}
                    @for($s = 1; $s <= $slotsNuevos; $s++)
                    <div id="slot-{{ $s }}">
                        <label class="relative aspect-square rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 hover:border-[#fc5648] hover:bg-[#fff8f7] transition cursor-pointer overflow-hidden block">
                            <img id="preview-{{ $s }}" src="" alt="" class="w-full h-full object-cover absolute inset-0 hidden">
                            <div id="placeholder-{{ $s }}" class="w-full h-full flex flex-col items-center justify-center gap-1">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <button type="button" id="btn-limpiar-{{ $s }}"
                                    onclick="limpiarSlot(event, {{ $s }})"
                                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 items-center justify-center text-[9px] font-bold shadow z-10 hidden">✕</button>
                            <input type="file" id="slot-input-{{ $s }}" name="imagen_nueva_{{ $s }}"
                                   accept="image/*"
                                   class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                                   onchange="previewSlot({{ $s }}, this)">
                        </label>
                        <div id="pos-nueva-{{ $s }}" class="mt-1 hidden">
                            <input type="number" min="1" max="99"
                                   name="posicion_nueva_{{ $s }}"
                                   placeholder="párrafo (auto)"
                                   class="w-full px-1.5 py-1 text-[10px] border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#fc5648] outline-none text-center">
                        </div>
                    </div>
                    @endfor

                </div>
            </div>

            {{-- ── Columna central: Editor ────────────────────────────────── --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Contenido</label>
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

            {{-- ── Columna derecha: Preview posiciones ────────────────────── --}}
            <div class="hidden lg:flex flex-col lg:sticky lg:top-20 lg:max-h-[80vh]">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Dónde quedarán</p>
                    <span id="preview-bloque-count" class="text-[10px] text-gray-400 font-semibold"></span>
                </div>
                <div id="preview-posiciones"
                     class="overflow-y-auto rounded-xl border border-gray-100 bg-gray-50 p-3 space-y-1 flex-1 min-h-40">
                    <p class="text-[11px] text-gray-300 italic">Escribe para ver la vista previa…</p>
                </div>
                <p class="mt-2 text-[10px] text-gray-400 leading-snug">
                    ↓ aparece <strong>después</strong> del bloque. Sin nº → automático.
                </p>
            </div>

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

</form>
