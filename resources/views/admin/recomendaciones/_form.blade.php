{{-- Plan --}}
<div class="w-[90vw] mx-auto">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Plan contratado <span class="text-red-500">*</span></label>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        @foreach(\App\Models\Recomendacion::PLANES as $slug => $info)
        <label class="relative cursor-pointer">
            <input type="radio" name="plan" value="{{ $slug }}"
                   {{ old('plan', $recomendacion->plan ?? 'basica') === $slug ? 'checked' : '' }}
                   class="sr-only peer" required>
            <span class="flex items-center gap-2 px-4 py-3 rounded-xl border-2 border-gray-200 text-sm font-bold text-gray-500
                         peer-checked:border-[#fc5648] peer-checked:bg-[#fff0ef] peer-checked:text-[#fc5648] transition">
                <span class="text-lg">{{ $info['emoji'] }}</span> {{ $info['label'] }}
            </span>
        </label>
        @endforeach
    </div>
    @error('plan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Vincular a ficha existente --}}
<div class="w-[90vw] mx-auto">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Vincular a ficha de Pindoor (opcional)</label>
    <select name="punto_interes_id"
            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none bg-white">
        <option value="">— Sin vincular —</option>
        @foreach($puntos as $p)
        <option value="{{ $p->id }}"
                {{ (int) old('punto_interes_id', $recomendacion->punto_interes_id ?? null) === $p->id ? 'selected' : '' }}>
            {{ $p->title }}{{ $p->sector ? ' · ' . $p->sector : '' }}
        </option>
        @endforeach
    </select>
    <p class="text-xs text-gray-400 mt-1">Si el negocio ya tiene ficha en Pindoor, se mostrará un enlace a esta reseña desde ahí.</p>
    @error('punto_interes_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Título + Negocio --}}
<div class="w-[90vw] mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            Título de la nota <span class="text-red-500">*</span>
        </label>
        <input type="text" name="titulo"
               value="{{ old('titulo', $recomendacion->titulo ?? '') }}"
               placeholder="Ej: Café Fahrenheit: el rincón perfecto del cerro Alegre"
               required
               class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none
                      {{ $errors->has('titulo') ? 'border-red-400' : 'border-gray-200' }}">
        @error('titulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre del negocio <span class="text-red-500">*</span></label>
        <input type="text" name="negocio"
               value="{{ old('negocio', $recomendacion->negocio ?? '') }}"
               placeholder="Café Fahrenheit"
               required
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('negocio') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Rubro + Dirección --}}
<div class="w-[90vw] mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Rubro</label>
        <input type="text" name="rubro"
               value="{{ old('rubro', $recomendacion->rubro ?? '') }}"
               placeholder="Cafetería, hostal, tour, tienda…"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('rubro') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Dirección / Ubicación</label>
        <input type="text" name="direccion"
               value="{{ old('direccion', $recomendacion->direccion ?? '') }}"
               placeholder="Cerro Alegre, Valparaíso"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('direccion') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- ── Bloque 3 columnas: Galería · Editor · Preview ─────────────── --}}
@php
    $imagenesExistentes = ($recomendacion ?? null)?->relationLoaded('imagenes') ? $recomendacion->imagenes : collect();
    $slotsNuevos = max(0, 20 - $imagenesExistentes->count());
@endphp
<div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
    <div class="grid grid-cols-1 lg:grid-cols-[210px_1fr_190px] gap-5 items-start">

        {{-- ── Columna izquierda: Galería ────────────────────────────── --}}
        <div class="lg:sticky lg:top-20 lg:max-h-[80vh] lg:overflow-y-auto lg:pr-1">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Galería</span>
                <span id="galeria-contador" class="text-[10px] font-bold text-gray-400">{{ $imagenesExistentes->count() }}/20</span>
            </div>
            <p class="text-[10px] text-gray-400 mb-3 leading-snug">
                Hasta 20 fotos (Plan 2 y 3). El nº indica tras qué párrafo aparece, o vacío para automático.
            </p>

            <div class="grid grid-cols-2 gap-2" id="galeria-grid">

                {{-- Imágenes existentes --}}
                @foreach($imagenesExistentes as $img)
                <div id="existente-{{ $img->id }}">
                    <div class="relative aspect-square rounded-xl overflow-hidden border-2 border-gray-100 bg-gray-50">
                        <img src="{{ asset('storage/' . $img->ruta) }}" alt="" class="w-full h-full object-cover">
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
                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400">Contenido de la nota</label>
                <div class="flex items-center gap-3 text-[11px] text-gray-400">
                    <span id="contenido-palabras">0 palabras</span>
                    <span id="contenido-chars" class="font-semibold">0 / 10 000</span>
                </div>
            </div>
            <div id="recomendacion-editor" class="min-h-100"></div>
            <div id="contenido-limite-aviso" class="hidden mt-2 text-xs text-red-500 font-semibold">
                Límite de 10 000 caracteres alcanzado.
            </div>
            <textarea id="contenido" name="contenido" class="hidden">{{ old('contenido', $recomendacion->contenido ?? '') }}</textarea>
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

{{-- Video Reel/TikTok (Instagram) + Video YouTube + Autor --}}
<div class="w-[90vw] mx-auto grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Video Reel / TikTok</label>
        <input type="url" name="video_url"
               value="{{ old('video_url', $recomendacion->video_url ?? '') }}"
               placeholder="https://instagram.com/reel/… (Plan 2 y 3)"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        <p class="text-xs text-gray-400 mt-1">Solo para Instagram — se muestra como enlace, no se incrusta en la nota.</p>
        @error('video_url') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Video de YouTube</label>
        <input type="url" name="video_youtube"
               value="{{ old('video_youtube', $recomendacion->video_youtube ?? '') }}"
               placeholder="https://www.youtube.com/watch?v=…"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        <p class="text-xs text-gray-400 mt-1">Entrevista / reportaje. Normalmente se incrusta en el cuerpo de la nota (Plan Premium).</p>
        @error('video_youtube') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

        <label class="flex items-center gap-2 mt-2 cursor-pointer">
            <input type="hidden" name="video_en_cabecera" value="0">
            <input type="checkbox" name="video_en_cabecera" value="1" id="video_en_cabecera"
                   {{ old('video_en_cabecera', $recomendacion->video_en_cabecera ?? false) ? 'checked' : '' }}
                   class="w-4 h-4 accent-[#fc5648] rounded">
            <span class="text-xs font-semibold text-gray-600">Mostrar este video en la cabecera (reemplaza la portada)</span>
        </label>
        <p class="text-[11px] text-amber-700 mt-0.5">Uso puntual para promociones — no es parte permanente de ningún plan.</p>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Autor / Redactor</label>
        <input type="text" name="autor"
               value="{{ old('autor', $recomendacion->autor ?? '') }}"
               placeholder="Equipo Pindoor"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('autor') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Enlace + WhatsApp --}}
<div class="w-[90vw] mx-auto grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Enlace (reservas / sitio web)</label>
        <input type="url" name="enlace"
               value="{{ old('enlace', $recomendacion->enlace ?? '') }}"
               placeholder="https://…"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('enlace') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            <span class="inline-flex items-center gap-1">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp del negocio
            </span>
        </label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">+</span>
            <input type="tel" name="whatsapp"
                   value="{{ old('whatsapp', $recomendacion->whatsapp ?? '') }}"
                   placeholder="56912345678"
                   class="w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        </div>
        <p class="text-xs text-gray-400 mt-1">Con código de país, sin espacios ni +</p>
        @error('whatsapp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Destacado en portada (Plan Premium) --}}
<div class="w-[90vw] mx-auto border border-amber-200 bg-amber-50 rounded-xl p-4 space-y-3">
    <label class="flex items-center gap-2.5 cursor-pointer">
        <input type="hidden" name="destacado_portada" value="0">
        <input type="checkbox" name="destacado_portada" value="1" id="destacado_portada"
               {{ old('destacado_portada', $recomendacion->destacado_portada ?? false) ? 'checked' : '' }}
               class="w-4 h-4 accent-amber-500 rounded">
        <span class="text-sm font-semibold text-amber-900">🥇 Destacar en banner "Recomendados" de la portada</span>
    </label>
    <div>
        <label class="block text-xs font-semibold text-amber-800 mb-1">Vigente hasta</label>
        <input type="date" name="destacado_hasta"
               value="{{ old('destacado_hasta', isset($recomendacion->destacado_hasta) ? $recomendacion->destacado_hasta?->format('Y-m-d') : '') }}"
               class="w-full sm:w-56 px-4 py-2 border border-amber-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-400 outline-none bg-white">
        <p class="text-xs text-amber-700 mt-1">Ej: 30 días desde la publicación (Plan Premium)</p>
        @error('destacado_hasta') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Imagen portada --}}
<div class="w-[90vw] mx-auto">
    <label class="block text-sm font-semibold text-gray-700 mb-2">Imagen portada</label>

    @if(isset($recomendacion->imagen_portada) && $recomendacion->imagen_portada)
        <div class="mb-3">
            <img src="{{ asset('storage/' . $recomendacion->imagen_portada) }}"
                 alt="Imagen actual"
                 class="h-40 w-auto rounded-xl border border-gray-200 object-cover">
            <p class="text-xs text-gray-400 mt-1">Imagen actual. Sube una nueva para reemplazarla.</p>
        </div>
    @endif

    <input id="input-portada" type="file" name="imagen_portada" accept="image/*"
           class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-xl file:border-0
                  file:text-sm file:font-bold
                  file:bg-[#fff0ef] file:text-[#fc5648]
                  hover:file:bg-[#ffe0dd] cursor-pointer">
    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — máx. 4 MB</p>
    <div id="preview-portada" class="mt-3 hidden">
        <img id="preview-portada-img" src="" alt="Preview"
             class="h-40 w-auto rounded-xl border border-[#fc5648]/40 object-cover">
    </div>
    @error('imagen_portada') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

<script>
document.getElementById('input-portada').addEventListener('change', function () {
    const file = this.files[0];
    const wrap = document.getElementById('preview-portada');
    const img  = document.getElementById('preview-portada-img');
    if (!file) { wrap.classList.add('hidden'); return; }
    img.src = URL.createObjectURL(file);
    img.onload = () => URL.revokeObjectURL(img.src);
    wrap.classList.remove('hidden');
});
</script>

{{-- Orden + Publicado + Activo --}}
<div class="w-[90vw] mx-auto flex flex-wrap items-center gap-6">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Orden</label>
        <input type="number" name="orden" min="0"
               value="{{ old('orden', $recomendacion->orden ?? 0) }}"
               class="w-20 px-3 py-2 border border-gray-200 rounded-xl text-sm text-center focus:ring-2 focus:ring-[#fc5648] outline-none">
        <p class="text-xs text-gray-400 mt-1">0 = primero</p>
    </div>
    <div class="flex items-center gap-3 mt-4">
        <input type="hidden" name="publicado" value="0">
        <input type="checkbox" name="publicado" value="1" id="publicado"
               {{ old('publicado', $recomendacion->publicado ?? false) ? 'checked' : '' }}
               class="w-4 h-4 accent-[#fc5648] rounded">
        <label for="publicado" class="text-sm font-semibold text-gray-700">Publicada</label>
    </div>
    <div class="flex items-center gap-3 mt-4">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox" name="activo" value="1" id="activo"
               {{ old('activo', $recomendacion->activo ?? true) ? 'checked' : '' }}
               class="w-4 h-4 accent-[#fc5648] rounded">
        <label for="activo" class="text-sm font-semibold text-gray-700">Visible en la web</label>
    </div>
</div>
