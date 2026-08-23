@php $esEdicion = isset($revival) && $revival !== null; @endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      id="revival-form" class="space-y-6">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    {{-- Publicar + Guardar --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 flex items-center justify-between gap-4">
        <label class="flex items-center gap-3 cursor-pointer select-none">
            <div class="relative">
                <input type="checkbox" name="publicado" id="publicado" value="1" class="sr-only peer"
                       {{ old('publicado', $revival?->publicado) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 rounded-full peer
                            peer-checked:bg-[#fc5648] transition-colors duration-200"></div>
                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow
                            peer-checked:translate-x-5 transition-transform duration-200"></div>
            </div>
            <span class="text-sm font-bold text-gray-700" id="publicado-label">
                {{ old('publicado', $revival?->publicado) ? 'Publicado' : 'Borrador' }}
            </span>
        </label>
        <div class="flex items-center gap-3">
            @if($esEdicion)
            <a href="{{ route('admin.revival.preview', $revival) }}" target="_blank"
               class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-5 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Vista previa
            </a>
            @endif
            <span id="guardar-spinner" class="hidden items-center gap-2 text-sm text-gray-400">
                <svg class="animate-spin w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Guardando…
            </span>
            <button type="submit" name="accion" value="seguir" id="guardar-seguir-btn"
                    class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition">
                {{ $esEdicion ? 'Guardar y seguir editando' : 'Crear y seguir editando' }}
            </button>
            <button type="submit" name="accion" value="guardar" id="guardar-btn"
                    class="bg-gray-900 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-black transition">
                {{ $esEdicion ? 'Guardar cambios' : 'Crear re-vival' }}
            </button>
        </div>
    </div>

    @if($esEdicion)
    @include('admin.partials._preview-link', ['modelo' => $revival, 'tipo' => 'revival'])
    @endif

    @if($errors->any())
    <div class="w-[90vw] mx-auto bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Tabs idioma ─────────────────────────────────────────────────── --}}
    <div class="w-[90vw] mx-auto flex items-center gap-2 flex-wrap">
        <button type="button" id="tab-es" onclick="setLang('es')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-[#fc5648] bg-[#fc5648] text-white transition">
            🇪🇸 Español
        </button>
        <button type="button" id="tab-en" onclick="setLang('en')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-gray-400 transition">
            🇬🇧 English
        </button>
        <button type="button" id="tab-fr" onclick="setLang('fr')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-gray-400 transition">
            🇫🇷 Français
        </button>
        <button type="button" id="btn-autotraducir-en" onclick="autoTraducir('en')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition ml-2">
            ✨ Auto-traducir ES→EN
        </button>
        <button type="button" id="btn-autotraducir-fr" onclick="autoTraducir('fr')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition">
            ✨ Auto-traducir ES→FR
        </button>
        <span id="traducir-estado" class="text-xs text-gray-400 hidden"></span>
        <span class="text-xs text-gray-400 ml-auto">Slug e imágenes son compartidos</span>
    </div>

    {{-- ── Fila superior: Título+Slug · Autor+Video · Portada ─────────── --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Título + Slug --}}
            <div class="space-y-4">
                <div data-lang-field="es">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Título * <span class="text-[#fc5648]">ES</span></label>
                    <input id="titulo-es" type="text" name="titulo_es" required
                           value="{{ old('titulo_es', $revival?->getTranslation('titulo','es',false)) }}"
                           placeholder="Ej: Así vivimos la Fiesta de la Primavera 2025"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <div data-lang-field="en" style="display:none">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Title <span class="text-blue-500">EN</span></label>
                    <input id="titulo-en" type="text" name="titulo_en"
                           value="{{ old('titulo_en', $revival?->getTranslation('titulo','en',false)) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div data-lang-field="fr" style="display:none">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Titre <span class="text-indigo-500">FR</span></label>
                    <input id="titulo-fr" type="text" name="titulo_fr"
                           value="{{ old('titulo_fr', $revival?->getTranslation('titulo','fr',false)) }}"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                        Slug (URL)
                        <span class="normal-case font-normal text-gray-400 ml-1">— automático desde el título ES</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-400 shrink-0">/panoramas/revival/</span>
                        <input id="slug" type="text" name="slug"
                               value="{{ old('slug', $revival?->slug) }}"
                               placeholder="fiesta-de-la-primavera-2025"
                               class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                    </div>
                </div>
            </div>

            {{-- Autor + Video --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                        Autor de la reseña
                        <span class="normal-case font-normal text-gray-400 ml-1">— opcional</span>
                    </label>
                    <input type="text" name="autor"
                           value="{{ old('autor', $revival?->autor) }}"
                           placeholder="Ej: Equipo Pindoor"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                        Video del evento
                        <span class="normal-case font-normal text-gray-400 ml-1">— opcional, enlace de YouTube</span>
                    </label>
                    <input type="url" name="video_url"
                           value="{{ old('video_url', $revival?->video_url) }}"
                           placeholder="https://www.youtube.com/watch?v=..."
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
            </div>

            {{-- Imagen portada --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">
                    Imagen de portada
                    <span class="normal-case font-normal text-gray-400 ml-1">— opcional</span>
                </label>

                @if($esEdicion && $revival->imagen_portada_url)
                <div class="mb-3" id="portada-preview-actual">
                    <img src="{{ $revival->imagen_portada_url }}" alt="Portada actual"
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
        $imagenesExistentes = $revival?->imagenes ?? [];
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
                    Hasta 20 fotos. Arrastra para ordenar la galería — el nº indica tras qué párrafo aparece además, o vacío para automático.
                </p>

                <label class="mb-3 flex items-center justify-center gap-1.5 py-2 rounded-xl border-2 border-dashed border-gray-200 text-[11px] font-bold text-gray-500 hover:border-[#fc5648] hover:text-[#fc5648] hover:bg-[#fff8f7] transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar varias fotos a la vez
                    <input type="file" id="galeria-multi-input" accept="image/*" multiple class="hidden">
                </label>

                <div class="grid grid-cols-2 gap-2" id="galeria-grid">

                    {{-- Imágenes existentes --}}
                    @foreach($imagenesExistentes as $img)
                    <div id="existente-{{ $img->id }}" data-tile draggable="true" class="cursor-move">
                        <input type="hidden" name="galeria_orden[]" value="existente:{{ $img->id }}">
                        <div class="relative aspect-square rounded-xl overflow-hidden border-2 border-gray-100 bg-gray-50">
                            <img src="{{ asset('storage/' . $img->ruta) }}" alt="" draggable="false" class="w-full h-full object-cover pointer-events-none">
                            <span class="absolute top-1 left-1 bg-black/40 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow z-10">⠿</span>
                            <button type="button" onclick="toggleEliminar({{ $img->id }})"
                                    id="btn-eliminar-{{ $img->id }}"
                                    class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-[10px] font-bold shadow transition z-10">✕</button>
                            <div id="overlay-eliminar-{{ $img->id }}"
                                 class="absolute inset-0 bg-red-500/75 hidden flex-col items-center justify-center gap-0.5 cursor-pointer"
                                 onclick="toggleEliminar({{ $img->id }})">
                                <span class="text-white font-black text-[10px]">Se borrará</span>
                                <span class="text-white/80 text-[9px]">al guardar · clic p/deshacer</span>
                            </div>
                            <input type="hidden" id="hidden-eliminar-{{ $img->id }}" name="eliminar_imagen[]" value="{{ $img->id }}" disabled>
                        </div>
                        <div id="pos-existente-{{ $img->id }}" class="mt-1">
                            <input type="number" min="1" max="99"
                                   name="posicion_existente_{{ $img->id }}"
                                   value="{{ old('posicion_existente_' . $img->id, $img->posicion) }}"
                                   placeholder="párrafo (auto)"
                                   class="w-full px-1.5 py-1 text-[10px] border border-gray-200 rounded-lg focus:ring-1 focus:ring-[#fc5648] outline-none text-center">
                        </div>
                        <div id="label-eliminar-{{ $img->id }}" class="mt-1 text-center hidden">
                            <span class="text-[9px] text-red-400 font-semibold">Se eliminará</span>
                        </div>
                    </div>
                    @endforeach

                    {{-- Slots nuevas imágenes --}}
                    @for($s = 1; $s <= $slotsNuevos; $s++)
                    <div id="slot-{{ $s }}" data-tile draggable="true" class="cursor-move">
                        <input type="hidden" name="galeria_orden[]" value="nueva:{{ $s }}">
                        <label class="relative aspect-square rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 hover:border-[#fc5648] hover:bg-[#fff8f7] transition cursor-pointer overflow-hidden block">
                            <img id="preview-{{ $s }}" src="" alt="" draggable="false" class="w-full h-full object-cover absolute inset-0 hidden pointer-events-none">
                            <div id="placeholder-{{ $s }}" class="w-full h-full flex flex-col items-center justify-center gap-1">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <span class="absolute top-1 left-1 bg-black/30 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow z-10">⠿</span>
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
                    <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                        Descripción / comentario del evento * — <span id="editor-lang-label" class="text-[#fc5648]">ES</span>
                    </label>
                    <div class="flex items-center gap-3 text-[11px] text-gray-400">
                        <span id="contenido-palabras">0 palabras</span>
                        <span id="contenido-chars" class="font-semibold">0 / 10 000</span>
                    </div>
                </div>
                <div id="revival-editor" class="min-h-100"></div>
                <div id="contenido-limite-aviso" class="hidden mt-2 text-xs text-red-500 font-semibold">
                    Límite de 10 000 caracteres alcanzado.
                </div>
                <textarea id="contenido_es" name="contenido_es" class="hidden">{{ old('contenido_es', $revival?->getTranslation('contenido','es',false)) }}</textarea>
                <textarea id="contenido_en" name="contenido_en" class="hidden">{{ old('contenido_en', $revival?->getTranslation('contenido','en',false)) }}</textarea>
                <textarea id="contenido_fr" name="contenido_fr" class="hidden">{{ old('contenido_fr', $revival?->getTranslation('contenido','fr',false)) }}</textarea>
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
        var marcado  = hidden && !hidden.disabled;
        if (marcado) {
            overlay.style.display = 'none';
            btn.style.display = '';
            hidden.disabled = true;
            posDiv.style.display = '';
            labelDiv.style.display = 'none';
        } else {
            overlay.style.display = 'flex';
            btn.style.display = 'none';
            hidden.disabled = false;
            posDiv.style.display = 'none';
            labelDiv.style.display = 'block';
        }
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

    // Reordenar la galería arrastrando las miniaturas — el orden final se manda
    // en orden[] según la posición de cada tarjeta en el DOM al enviar el form.
    (function() {
        var grid = document.getElementById('galeria-grid');
        if (!grid) return;
        var dragged = null;

        grid.addEventListener('dragstart', function(e) {
            var tile = e.target.closest('[data-tile]');
            if (!tile) return;
            dragged = tile;
            e.dataTransfer.effectAllowed = 'move';
            setTimeout(function() { tile.classList.add('opacity-30'); }, 0);
        });

        grid.addEventListener('dragend', function() {
            if (dragged) dragged.classList.remove('opacity-30');
            dragged = null;
        });

        grid.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        grid.addEventListener('drop', function(e) {
            e.preventDefault();
            var target = e.target.closest('[data-tile]');
            if (!target || !dragged || target === dragged) return;
            var tiles = Array.prototype.slice.call(grid.children);
            var draggedIdx = tiles.indexOf(dragged);
            var targetIdx  = tiles.indexOf(target);
            if (draggedIdx < targetIdx) {
                target.after(dragged);
            } else {
                target.before(dragged);
            }
        });
    })();

    // Subir varias fotos a la vez: reparte los archivos elegidos en los slots
    // vacíos, en orden, y dispara el mismo preview que si se hubieran elegido
    // uno por uno — no toca el backend, cada foto sigue siendo su propio slot.
    document.getElementById('galeria-multi-input')?.addEventListener('change', function(e) {
        var files = Array.prototype.slice.call(e.target.files);
        if (!files.length) return;
        var libres = Array.prototype.slice.call(document.querySelectorAll('[id^="slot-input-"]'))
            .filter(function(input) { return !input.files || input.files.length === 0; });
        if (files.length > libres.length) {
            alert('Solo hay espacio para ' + libres.length + ' foto(s) más. Se cargarán las primeras ' + libres.length + '.');
        }
        files.slice(0, libres.length).forEach(function(file, i) {
            var input = libres[i];
            var dt = new DataTransfer();
            dt.items.add(file);
            input.files = dt.files;
            previewSlot(input.id.replace('slot-input-', ''), input);
        });
        e.target.value = '';
    });
    </script>

</form>
