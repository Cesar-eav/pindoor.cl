{{-- Título --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">
        Título <span class="text-red-500">*</span>
    </label>
    <input type="text" name="titulo"
           value="{{ old('titulo', $panorama->titulo ?? '') }}"
           required
           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none
                  {{ $errors->has('titulo') ? 'border-red-400' : 'border-gray-200' }}">
    @error('titulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Ubicación --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Ubicación</label>
    <input type="text" name="ubicacion"
           value="{{ old('ubicacion', $panorama->ubicacion ?? '') }}"
           placeholder="Teatro Municipal, Parque Cultural…"
           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
    @error('ubicacion') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Categoría + Gratuito --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Categoría</label>
        <select name="categoria"
                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none bg-white">
            <option value="">— Sin categoría —</option>
            @foreach(\App\Models\Panorama::CATEGORIAS as $slug => $cat)
                <option value="{{ $slug }}" {{ old('categoria', $panorama->categoria ?? '') === $slug ? 'selected' : '' }}>
                    {{ $cat['emoji'] }} {{ $cat['label'] }}
                </option>
            @endforeach
        </select>
        @error('categoria') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div class="flex items-end pb-2.5">
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="hidden" name="es_gratuito" value="0">
            <input type="checkbox" name="es_gratuito" value="1" id="es_gratuito"
                   {{ old('es_gratuito', $panorama->es_gratuito ?? false) ? 'checked' : '' }}
                   class="w-4 h-4 accent-[#fc5648] rounded">
            <span class="text-sm font-semibold text-gray-700">🎟️ Entrada gratuita</span>
        </label>
    </div>
</div>

{{-- Fechas --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            Fecha inicio <span class="text-red-500">*</span>
        </label>
        <input type="date" name="fecha" required
               value="{{ old('fecha', isset($panorama->fecha) ? $panorama->fecha->format('Y-m-d') : '') }}"
               class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none
                      {{ $errors->has('fecha') ? 'border-red-400' : 'border-gray-200' }}">
        @error('fecha') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">
            Fecha fin <span class="text-gray-400 font-normal">(opcional)</span>
        </label>
        <input type="date" name="fecha_fin"
               value="{{ old('fecha_fin', isset($panorama->fecha_fin) ? $panorama->fecha_fin->format('Y-m-d') : '') }}"
               class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none
                      {{ $errors->has('fecha_fin') ? 'border-red-400' : 'border-gray-200' }}">
        @error('fecha_fin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Recurrencia --}}
@php
    $diasGuardados = old('dias_semana', isset($panorama) ? ($panorama->dias_semana ?? []) : []);
    $tieneRecurrencia = !empty($diasGuardados);
@endphp
<div class="border border-gray-200 rounded-xl p-4 space-y-3">
    <label class="flex items-center gap-2.5 cursor-pointer">
        <input type="checkbox" id="toggle-recurrencia"
               {{ $tieneRecurrencia ? 'checked' : '' }}
               class="w-4 h-4 accent-[#fc5648] rounded">
        <span class="text-sm font-semibold text-gray-700">🔁 Repite en días específicos</span>
    </label>

    <div id="dias-selector" class="{{ $tieneRecurrencia ? '' : 'hidden' }} space-y-2">
        <p class="text-xs text-gray-400">
            Selecciona los días de la semana en que ocurre este evento dentro del rango de fechas.
        </p>
        <div class="flex gap-2 flex-wrap">
            @foreach(\App\Models\Panorama::DIAS as $num => $label)
            <label class="relative cursor-pointer">
                <input type="checkbox" name="dias_semana[]" value="{{ $num }}"
                       id="dia-{{ $num }}"
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
        <p class="text-xs text-gray-400">La <strong>Fecha inicio</strong> y <strong>Fecha fin</strong> definen el periodo del ciclo.</p>
    </div>
</div>

<script>
document.getElementById('toggle-recurrencia').addEventListener('change', function () {
    document.getElementById('dias-selector').classList.toggle('hidden', !this.checked);
    if (!this.checked) {
        document.querySelectorAll('[name="dias_semana[]"]').forEach(cb => cb.checked = false);
    }
});
</script>

{{-- Hora --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Hora</label>
    <input type="text" name="hora"
           value="{{ old('hora', $panorama->hora ?? '') }}"
           placeholder="19:00 / 10:00 a.m."
           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
    @error('hora') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>


{{-- Enlace --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Enlace</label>
    <input type="url" name="enlace"
           value="{{ old('enlace', $panorama->enlace ?? '') }}"
           placeholder="https://..."
           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
    <p class="text-xs text-gray-400 mt-1">URL del evento, entradas o más información (opcional)</p>
    @error('enlace') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Imagen portada --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Imagen portada</label>

    @if(isset($panorama->imagen) && $panorama->imagen)
        <div class="mb-3">
            <img src="{{ asset('storage/' . $panorama->imagen) }}"
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
        <img id="preview-portada-img" src="" alt="Preview portada"
             class="h-40 w-auto rounded-xl border border-[#fc5648]/40 object-cover">
        <p class="text-xs text-gray-400 mt-1">Vista previa — se guardará al enviar el formulario</p>
    </div>

    @error('imagen') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Imágenes adicionales --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Imágenes adicionales</label>

    @if(isset($panorama) && $panorama->relationLoaded('imagenes') && $panorama->imagenes->isNotEmpty())
        <div class="flex flex-wrap gap-3 mb-3">
            @foreach($panorama->imagenes as $img)
                <div class="relative group" id="img-wrap-{{ $img->id }}">
                    <img src="{{ asset('storage/' . $img->ruta) }}"
                         alt="Imagen"
                         class="h-28 w-28 object-cover rounded-xl border border-gray-200">
                    <button type="button"
                            onclick="eliminarImagen({{ $img->id }}, '{{ route('admin.panoramas.imagenes.destroy', $img) }}', '{{ csrf_token() }}')"
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
    <p class="text-xs text-gray-400 mt-1">Puedes seleccionar varias imágenes a la vez — JPG, PNG, WEBP — máx. 4 MB c/u</p>
    <div id="preview-adicionales" class="hidden mt-3 flex-wrap gap-3"></div>

    @error('imagenes.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

<script>
(function () {
    // Preview portada
    document.getElementById('input-portada').addEventListener('change', function () {
        const file = this.files[0];
        const wrap = document.getElementById('preview-portada');
        const img  = document.getElementById('preview-portada-img');

        if (!file) { wrap.classList.add('hidden'); return; }

        console.info('[Panorama] portada seleccionada', {
            name: file.name,
            size: (file.size / 1024).toFixed(1) + ' KB',
            type: file.type,
        });

        if (file.size > 4 * 1024 * 1024) {
            console.warn('[Panorama] portada supera 4 MB — será rechazada por el servidor');
        }

        img.src = URL.createObjectURL(file);
        img.onload = () => URL.revokeObjectURL(img.src);
        wrap.classList.remove('hidden');
    });

    // Preview adicionales
    document.getElementById('input-adicionales').addEventListener('change', function () {
        const container = document.getElementById('preview-adicionales');
        container.innerHTML = '';

        if (!this.files.length) { container.classList.remove('flex'); container.classList.add('hidden'); return; }

        console.info('[Panorama] adicionales seleccionadas', this.files.length + ' archivo(s)');

        Array.from(this.files).forEach((file, i) => {
            console.info('[Panorama] adicional #' + i, {
                name: file.name,
                size: (file.size / 1024).toFixed(1) + ' KB',
                type: file.type,
            });

            if (file.size > 4 * 1024 * 1024) {
                console.warn('[Panorama] adicional #' + i + ' supera 4 MB — será rechazada');
            }

            const url = URL.createObjectURL(file);
            const img = document.createElement('img');
            img.src    = url;
            img.alt    = file.name;
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

    fetch(url, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
    })
    .then(res => {
        if (!res.ok) throw new Error('Error ' + res.status);
        document.getElementById('img-wrap-' + id)?.remove();
    })
    .catch(err => {
        console.error('[Panorama] error eliminando imagen', err);
        alert('No se pudo eliminar la imagen. Intenta de nuevo.');
    });
}
</script>

{{-- Activo --}}
<div class="flex items-center gap-3">
    <input type="hidden" name="activo" value="0">
    <input type="checkbox" name="activo" value="1" id="activo"
           {{ old('activo', $panorama->activo ?? true) ? 'checked' : '' }}
           class="w-4 h-4 accent-[#fc5648] rounded">
    <label for="activo" class="text-sm font-semibold text-gray-700">Visible en la web</label>
</div>
