<div id="pills-mapa-desktop" class="overflow-x-auto no-scrollbar bg-white border-b border-gray-100 px-4 py-3 sticky top-15 z-30 shadow-sm">
    <div class="flex gap-2 w-max">
        <a href="{{ route('puntos.index', array_filter(['search' => request('search')])) }}"
           class="px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap
                  {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
            Todos
        </a>

        @foreach($categorias as $cat)
        <a href="{{ route('puntos.index', array_filter(['category' => $cat->slug, 'search' => request('search')])) }}"
           data-slug="{{ $cat->slug }}"
           class="px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap
                  {{ request('category') == $cat->slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
            {{ $cat->nombre }}
        </a>
        @endforeach
    </div>
</div>
