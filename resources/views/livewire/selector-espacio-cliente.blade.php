<div>
    <div class="flex flex-col sm:flex-row gap-3 mb-4">
        <input type="text" wire:model.live.debounce.300ms="busqueda"
               placeholder="Buscar por nombre o sector..."
               class="flex-1 text-sm rounded-lg border-gray-200 focus:border-[#fc5648] focus:ring-[#fc5648]">
        <select wire:model.live="categoriaFiltro"
                class="text-sm rounded-lg border-gray-200 focus:border-[#fc5648] focus:ring-[#fc5648]">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->icono }} {{ $categoria->nombre }}</option>
            @endforeach
        </select>
    </div>

    @if($total === 0)
        <p class="text-sm text-gray-400 italic">
            @if($busqueda !== '' || $categoriaFiltro !== '')
                Ningún espacio coincide con esa búsqueda.
            @else
                Todos los puntos ya están activados como clientes.
            @endif
        </p>
    @else
        <p class="text-xs text-gray-400 mb-3">
            @if($total > $espacios->count())
                Mostrando {{ $espacios->count() }} de {{ $total }} — sigue escribiendo para afinar la búsqueda
            @else
                {{ $total }} {{ $total === 1 ? 'espacio disponible' : 'espacios disponibles' }}
            @endif
        </p>

        <div class="flex flex-wrap gap-3" wire:loading.class="opacity-50">
            @foreach($espacios as $punto)
                <a href="{{ route('admin.clientes.activar.form', $punto) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-pindoor-accent hover:text-white text-gray-700 text-sm font-medium rounded-lg transition">
                    {{ $punto->title }}
                    <span class="text-xs opacity-60">{{ $punto->sector }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
