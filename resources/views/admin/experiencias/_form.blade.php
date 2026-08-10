{{-- Tabs idioma --}}
<div class="flex items-center gap-2 flex-wrap">
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
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition ml-2">
        ✨ ES→EN
    </button>
    <button type="button" id="btn-autotraducir-fr" onclick="autoTraducir('fr')"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition">
        ✨ ES→FR
    </button>
    <span id="traducir-estado" class="text-xs text-gray-400 hidden"></span>
</div>

{{-- Título + Proveedor --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <div data-lang-field="es">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Título <span class="text-[#fc5648]">ES</span> <span class="text-red-500">*</span>
            </label>
            <input id="titulo-es" type="text" name="titulo_es"
                   value="{{ old('titulo_es', $experiencia?->getTranslation('titulo','es',false)) }}"
                   required
                   class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none
                          {{ $errors->has('titulo_es') ? 'border-red-400' : 'border-gray-200' }}">
            @error('titulo_es') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div data-lang-field="en" style="display:none">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-blue-500">EN</span></label>
            <input id="titulo-en" type="text" name="titulo_en"
                   value="{{ old('titulo_en', $experiencia?->getTranslation('titulo','en',false)) }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-400 outline-none">
        </div>
        <div data-lang-field="fr" style="display:none">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Titre <span class="text-indigo-500">FR</span></label>
            <input id="titulo-fr" type="text" name="titulo_fr"
                   value="{{ old('titulo_fr', $experiencia?->getTranslation('titulo','fr',false)) }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
        </div>
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Proveedor / Instructor</label>
        <input type="text" name="proveedor"
               value="{{ old('proveedor', $experiencia->proveedor ?? '') }}"
               placeholder="Nombre de quien lo ofrece…"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('proveedor') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Descripción --}}
<div data-lang-field="es">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción <span class="text-[#fc5648]">ES</span></label>
    <textarea id="descripcion-es" name="descripcion_es" rows="3" maxlength="2000"
              placeholder="¿De qué trata la experiencia? ¿Qué van a vivir los participantes?…"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ old('descripcion_es', $experiencia?->getTranslation('descripcion','es',false)) }}</textarea>
    @error('descripcion_es') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>
<div data-lang-field="en" style="display:none">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Description <span class="text-blue-500">EN</span></label>
    <textarea id="descripcion-en" name="descripcion_en" rows="3" maxlength="2000"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-400 outline-none resize-none">{{ old('descripcion_en', $experiencia?->getTranslation('descripcion','en',false)) }}</textarea>
</div>
<div data-lang-field="fr" style="display:none">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Description <span class="text-indigo-500">FR</span></label>
    <textarea id="descripcion-fr" name="descripcion_fr" rows="3" maxlength="2000"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none">{{ old('descripcion_fr', $experiencia?->getTranslation('descripcion','fr',false)) }}</textarea>
</div>

{{-- Categoría + Gratuito/Precio --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Categoría</label>
        <select name="categoria"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none bg-white">
            <option value="">— Sin categoría —</option>
            @foreach(\App\Models\Experiencia::CATEGORIAS as $slug => $cat)
                <option value="{{ $slug }}"
                        {{ old('categoria', $experiencia->categoria ?? '') === $slug ? 'selected' : '' }}>
                    {{ $cat['emoji'] }} {{ $cat['label'] }}
                </option>
            @endforeach
        </select>
        @error('categoria') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="flex flex-col justify-end pb-2.5">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="es_gratuito" value="0">
            <input type="checkbox" name="es_gratuito" value="1" id="es_gratuito"
                   {{ old('es_gratuito', $experiencia->es_gratuito ?? false) ? 'checked' : '' }}
                   class="w-4 h-4 accent-[#fc5648] rounded"
                   onchange="document.getElementById('precio-wrap').classList.toggle('opacity-30', this.checked)">
            <span class="text-sm font-semibold text-gray-700">🎟️ Entrada gratuita</span>
        </label>
    </div>
    <div id="precio-wrap" class="{{ old('es_gratuito', $experiencia->es_gratuito ?? false) ? 'opacity-30' : '' }}">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Precio (CLP)</label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">$</span>
            <input type="number" name="precio" min="0" step="100"
                   value="{{ old('precio', $experiencia->precio ?? '') }}"
                   placeholder="5000"
                   class="w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        </div>
        @error('precio') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Duración + Capacidad + Nivel --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Duración</label>
        <input type="text" name="duracion"
               value="{{ old('duracion', $experiencia->duracion ?? '') }}"
               placeholder="2 horas, 90 min…"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('duracion') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Cupos máximos</label>
        <input type="number" name="capacidad" min="1"
               value="{{ old('capacidad', $experiencia->capacidad ?? '') }}"
               placeholder="10"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('capacidad') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Nivel</label>
        <select name="nivel"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none bg-white">
            <option value="">— Sin especificar —</option>
            @foreach(\App\Models\Experiencia::NIVELES as $slug => $label)
                <option value="{{ $slug }}"
                        {{ old('nivel', $experiencia->nivel ?? '') === $slug ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('nivel') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Días de la semana --}}
@php
    $diasGuardados    = old('dias_semana', isset($experiencia) ? ($experiencia->dias_semana ?? []) : []);
    $tieneRecurrencia = !empty($diasGuardados);
@endphp
<div class="border border-gray-200 rounded-xl p-4 space-y-3">
    <label class="flex items-center gap-2.5 cursor-pointer">
        <input type="checkbox" id="toggle-dias"
               {{ $tieneRecurrencia ? 'checked' : '' }}
               class="w-4 h-4 accent-[#fc5648] rounded">
        <span class="text-sm font-semibold text-gray-700">🔁 Días en que se realiza</span>
    </label>
    <div id="dias-selector" class="{{ $tieneRecurrencia ? '' : 'hidden' }} space-y-2">
        <p class="text-xs text-gray-400">Marca los días de la semana en que está disponible esta experiencia.</p>
        <div class="flex gap-2 flex-wrap">
            @foreach(\App\Models\Experiencia::DIAS as $num => $label)
            <label class="relative cursor-pointer">
                <input type="checkbox" name="dias_semana[]" value="{{ $num }}"
                       {{ in_array($num, (array) $diasGuardados) ? 'checked' : '' }}
                       class="sr-only peer">
                <span class="flex items-center justify-center w-12 h-12 rounded-xl border-2 text-sm font-bold transition-all
                             border-gray-200 text-gray-400
                             peer-checked:bg-[#fc5648] peer-checked:border-[#fc5648] peer-checked:text-white
                             hover:border-[#fc5648] hover:text-[#fc5648]">
                    {{ $label }}
                </span>
            </label>
            @endforeach
        </div>
    </div>
</div>

<script>
document.getElementById('toggle-dias').addEventListener('change', function () {
    document.getElementById('dias-selector').classList.toggle('hidden', !this.checked);
    if (!this.checked) {
        document.querySelectorAll('[name="dias_semana[]"]').forEach(cb => cb.checked = false);
    }
});
</script>

{{-- Hora + Ubicación --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Hora</label>
        <input type="text" name="hora"
               value="{{ old('hora', $experiencia->hora ?? '') }}"
               placeholder="10:00, 19:00 hrs…"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('hora') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Ubicación</label>
        <input type="text" name="ubicacion"
               value="{{ old('ubicacion', $experiencia->ubicacion ?? '') }}"
               placeholder="Cerro Alegre, Estudio central…"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('ubicacion') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Período de vigencia --}}
<div class="border border-gray-200 rounded-xl p-4 space-y-3">
    <p class="text-sm font-semibold text-gray-700">📅 Período de vigencia <span class="font-normal text-gray-400">(opcional)</span></p>
    <p class="text-xs text-gray-400">Si la experiencia solo se da durante ciertos meses, indica el rango. Ej: todos los martes de junio a agosto.</p>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
            <input type="date" name="fecha_inicio"
                   value="{{ old('fecha_inicio', isset($experiencia->fecha_inicio) ? $experiencia->fecha_inicio?->format('Y-m-d') : '') }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
            @error('fecha_inicio') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
            <input type="date" name="fecha_fin"
                   value="{{ old('fecha_fin', isset($experiencia->fecha_fin) ? $experiencia->fecha_fin?->format('Y-m-d') : '') }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
            @error('fecha_fin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Enlace + WhatsApp --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Enlace</label>
        <input type="url" name="enlace"
               value="{{ old('enlace', $experiencia->enlace ?? '') }}"
               placeholder="https://…"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        <p class="text-xs text-gray-400 mt-1">URL para reservar o más información (opcional)</p>
        @error('enlace') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            <span class="inline-flex items-center gap-1">
                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                WhatsApp
            </span>
        </label>
        <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-400">+</span>
            <input type="tel" name="whatsapp"
                   value="{{ old('whatsapp', $experiencia->whatsapp ?? '') }}"
                   placeholder="56912345678"
                   class="w-full pl-7 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        </div>
        <p class="text-xs text-gray-400 mt-1">Con código de país, sin espacios ni +</p>
        @error('whatsapp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Imagen portada --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Imagen portada</label>

    @if(isset($experiencia->imagen) && $experiencia->imagen)
        <div class="mb-3">
            <img src="{{ asset('storage/' . $experiencia->imagen) }}"
                 alt="Imagen actual"
                 class="h-40 w-auto rounded-xl border border-gray-200 object-cover">
            <p class="text-xs text-gray-400 mt-1">Imagen actual. Sube una nueva para reemplazarla.</p>
        </div>
    @endif

    <input id="input-portada" type="file" name="imagen" accept="image/*"
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
    @error('imagen') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Imágenes adicionales --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Imágenes adicionales</label>

    @if(isset($experiencia) && $experiencia->relationLoaded('imagenes') && $experiencia->imagenes->isNotEmpty())
        <div class="flex flex-wrap gap-3 mb-3">
            @foreach($experiencia->imagenes as $img)
                <div class="relative group" id="img-wrap-{{ $img->id }}">
                    <img src="{{ asset('storage/' . $img->ruta) }}"
                         alt="Imagen"
                         class="h-28 w-28 object-cover rounded-xl border border-gray-200">
                    <button type="button"
                            onclick="eliminarImagen({{ $img->id }}, '{{ route('admin.experiencias.imagenes.destroy', $img) }}', '{{ csrf_token() }}')"
                            class="absolute top-1 right-1 bg-white bg-opacity-90 text-red-500 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow hover:bg-red-500 hover:text-white transition opacity-0 group-hover:opacity-100">
                        ✕
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <input id="input-adicionales" type="file" name="imagenes[]" accept="image/*" multiple
           class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-xl file:border-0
                  file:text-sm file:font-bold
                  file:bg-[#fff0ef] file:text-[#fc5648]
                  hover:file:bg-[#ffe0dd] cursor-pointer">
    <p class="text-xs text-gray-400 mt-1">Varias imágenes a la vez — JPG, PNG, WEBP — máx. 4 MB c/u</p>
    <div id="preview-adicionales" class="hidden mt-3 flex-wrap gap-3"></div>
    @error('imagenes.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

<script>
(function () {
    document.getElementById('input-portada').addEventListener('change', function () {
        const file = this.files[0];
        const wrap = document.getElementById('preview-portada');
        const img  = document.getElementById('preview-portada-img');
        if (!file) { wrap.classList.add('hidden'); return; }
        img.src = URL.createObjectURL(file);
        img.onload = () => URL.revokeObjectURL(img.src);
        wrap.classList.remove('hidden');
    });

    document.getElementById('input-adicionales').addEventListener('change', function () {
        const container = document.getElementById('preview-adicionales');
        container.innerHTML = '';
        if (!this.files.length) { container.classList.remove('flex'); container.classList.add('hidden'); return; }
        Array.from(this.files).forEach(file => {
            const url = URL.createObjectURL(file);
            const img = document.createElement('img');
            img.src = url;
            img.onload = () => URL.revokeObjectURL(url);
            img.className = 'h-28 w-28 object-cover rounded-xl border border-[#fc5648]/40';
            container.appendChild(img);
        });
        container.classList.remove('hidden');
        container.classList.add('flex');
    });
})();

function eliminarImagen(id, url, token) {
    if (!confirm('¿Eliminar esta imagen?')) return;
    fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
        .then(res => { if (!res.ok) throw new Error(); document.getElementById('img-wrap-' + id)?.remove(); })
        .catch(() => alert('No se pudo eliminar la imagen.'));
}
</script>

{{-- Orden + Activo --}}
<div class="flex flex-wrap items-center gap-6">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Orden</label>
        <input type="number" name="orden" min="0"
               value="{{ old('orden', $experiencia->orden ?? 0) }}"
               class="w-20 px-3 py-2 border border-gray-200 rounded-xl text-sm text-center focus:ring-2 focus:ring-[#fc5648] outline-none">
        <p class="text-xs text-gray-400 mt-1">0 = primero</p>
    </div>
    <div class="flex items-center gap-3 mt-4">
        <input type="hidden" name="activo" value="0">
        <input type="checkbox" name="activo" value="1" id="activo"
               {{ old('activo', $experiencia->activo ?? true) ? 'checked' : '' }}
               class="w-4 h-4 accent-[#fc5648] rounded">
        <label for="activo" class="text-sm font-semibold text-gray-700">Visible en la web</label>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentLang = 'es';

    const TAB_ESTILOS = {
        es: 'border-[#fc5648] bg-[#fc5648] text-white',
        en: 'border-blue-500 bg-blue-500 text-white',
        fr: 'border-indigo-500 bg-indigo-500 text-white',
    };
    const TAB_INACTIVO = 'border-gray-200 bg-white text-gray-500 hover:border-gray-400';

    window.setLang = function(lang) {
        document.querySelectorAll('[data-lang-field]').forEach(el => {
            el.style.display = el.dataset.langField === lang ? '' : 'none';
        });

        Object.keys(TAB_ESTILOS).forEach(l => {
            const tab = document.getElementById('tab-' + l);
            if (!tab) return;
            tab.className = 'flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border transition '
                + (l === lang ? TAB_ESTILOS[l] : TAB_INACTIVO);
        });

        currentLang = lang;
    };

    window.autoTraducir = async function(destino) {
        const btn    = document.getElementById('btn-autotraducir-' + destino);
        const estado = document.getElementById('traducir-estado');

        const tituloEs      = document.getElementById('titulo-es').value.trim();
        const descripcionEs = document.getElementById('descripcion-es').value.trim();

        if (!tituloEs && !descripcionEs) {
            alert('Escribe primero el contenido en Español.');
            return;
        }

        btn.disabled  = true;
        btn.className = btn.className.replace('text-gray-500', 'text-blue-400');
        estado.classList.remove('hidden');

        async function traducirTexto(txt) {
            if (!txt) return '';
            const oraciones = txt.match(/[^.!?]+[.!?]+/g) || [txt];
            const chunks = [];
            let cur = '';
            for (const o of oraciones) {
                if ((cur + o).length > 450) { if (cur) chunks.push(cur); cur = o; }
                else cur += (cur ? ' ' : '') + o;
            }
            if (cur) chunks.push(cur);

            let resultado = '';
            for (let i = 0; i < chunks.length; i++) {
                estado.textContent = `Traduciendo… ${i + 1}/${chunks.length}`;
                const r = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(chunks[i])}&langpair=es|${destino}&de=cesar.eav@gmail.com`);
                const d = await r.json();
                resultado += (d.responseData?.translatedText || chunks[i]) + ' ';
                await new Promise(r => setTimeout(r, 600));
            }
            return resultado.trim();
        }

        try {
            if (tituloEs) {
                estado.textContent = 'Traduciendo título…';
                document.getElementById('titulo-' + destino).value = await traducirTexto(tituloEs);
            }
            if (descripcionEs) {
                estado.textContent = 'Traduciendo descripción…';
                document.getElementById('descripcion-' + destino).value = await traducirTexto(descripcionEs);
            }

            setLang(destino);
            estado.textContent = '✓ Traducción completada';
            setTimeout(() => { estado.classList.add('hidden'); estado.textContent = ''; }, 3000);
        } catch(e) {
            estado.textContent = 'Error al traducir. Inténtalo de nuevo.';
        } finally {
            btn.disabled  = false;
            btn.className = btn.className.replace('text-blue-400', 'text-gray-500');
        }
    };
});
</script>
