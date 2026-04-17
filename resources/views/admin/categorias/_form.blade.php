<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
    <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" required
           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none {{ $errors->has('nombre') ? 'border-red-400' : 'border-gray-200' }}">
    @error('nombre') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Icono (emoji)</label>
    <input type="text" name="icono" value="{{ old('icono', $categoria->icono ?? '') }}"
           placeholder="🍽️"
           class="w-24 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-center focus:ring-2 focus:ring-[#fc5648] outline-none">
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo</label>
    <input type="text" name="tipo" value="{{ old('tipo', $categoria->tipo ?? '') }}"
           placeholder="gastronomia, cultura, naturaleza…"
           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
    <textarea name="descripcion" rows="3"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
</div>
