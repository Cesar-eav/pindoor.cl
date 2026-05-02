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

{{-- Fecha + Hora --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Fecha</label>
        <input type="date" name="fecha"
               value="{{ old('fecha', isset($panorama->fecha) ? $panorama->fecha->format('Y-m-d') : '') }}"
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('fecha') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Hora</label>
        <input type="text" name="hora"
               value="{{ old('hora', $panorama->hora ?? '') }}"
               placeholder="19:00 / 10:00 a.m."
               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
        @error('hora') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Orden --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Orden de aparición</label>
    <input type="number" name="orden" min="0"
           value="{{ old('orden', $panorama->orden ?? 0) }}"
           class="w-32 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
    <p class="text-xs text-gray-400 mt-1">Número menor aparece primero. 0 = sin orden preferente.</p>
</div>

{{-- Imagen --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Imagen</label>

    @if(isset($panorama->imagen) && $panorama->imagen)
        <div class="mb-3">
            <img src="{{ asset('storage/' . $panorama->imagen) }}"
                 alt="Imagen actual"
                 class="h-40 w-auto rounded-xl border border-gray-200 object-cover">
            <p class="text-xs text-gray-400 mt-1">Imagen actual. Sube una nueva para reemplazarla.</p>
        </div>
    @endif

    <input type="file" name="imagen" accept="image/*"
           class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-xl file:border-0
                  file:text-sm file:font-bold
                  file:bg-[#fff0ef] file:text-[#fc5648]
                  hover:file:bg-[#ffe0dd] cursor-pointer">
    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — máx. 4 MB</p>

    @error('imagen') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

{{-- Activo --}}
<div class="flex items-center gap-3">
    <input type="hidden" name="activo" value="0">
    <input type="checkbox" name="activo" value="1" id="activo"
           {{ old('activo', $panorama->activo ?? true) ? 'checked' : '' }}
           class="w-4 h-4 accent-[#fc5648] rounded">
    <label for="activo" class="text-sm font-semibold text-gray-700">Visible en la web</label>
</div>
