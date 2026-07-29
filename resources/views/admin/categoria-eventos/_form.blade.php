@php $categoria = $categoria ?? null; @endphp

<div class="space-y-4">
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" required maxlength="100"
               placeholder="Ej: Música"
               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#fc5648] focus:border-[#fc5648] outline-none transition">
        @error('nombre')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Emoji</label>
            <input type="text" name="emoji" value="{{ old('emoji', $categoria->emoji ?? '') }}" maxlength="20"
                   placeholder="🎵"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#fc5648] focus:border-[#fc5648] outline-none transition">
            @error('emoji')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Orden</label>
            <input type="number" name="orden" value="{{ old('orden', $categoria->orden ?? '') }}" min="0"
                   placeholder="Automático"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#fc5648] focus:border-[#fc5648] outline-none transition">
            @error('orden')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    @if($categoria)
    <p class="text-xs text-gray-400">
        Slug actual: <span class="font-mono">{{ $categoria->slug }}</span> — si cambias el nombre, se actualiza el slug y se re-asigna automáticamente en todos los panoramas/eventos que ya la usan.
    </p>
    @endif
</div>
