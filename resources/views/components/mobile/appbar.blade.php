{{-- App Bar mobile — global para todas las vistas --}}
<header class="md:hidden sticky top-0 z-40 bg-white border-b border-gray-100 px-4 py-3 flex items-center justify-between shadow-sm">
    <a href="{{ route('atractivos.index') }}" class="text-lg font-bold tracking-tight font-sans">
        <span class="text-[#fc5648]">Pin</span>door
    </a>
    <div class="flex items-center gap-3">
        <button onclick="toggleSearch()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
        @if(isset($hayFiltros) && $hayFiltros)
        <a href="{{ route('atractivos.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#fc5648] text-white text-sm font-bold">
            ✕
        </a>
        @endif
        <button onclick="openDrawer()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</header>

{{-- Barra de búsqueda (slide) — global para todas las vistas --}}
<div id="search-bar" class="md:hidden hidden bg-white border-b border-gray-100 px-4 py-3 shadow-sm">
    <form action="{{ route('atractivos.index') }}" method="GET">
        <div class="flex gap-2">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Café, mirador, ascensor…"
                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#fc5648] outline-none bg-gray-50">
            </div>
            <button type="submit" class="bg-[#fc5648] text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                Buscar
            </button>
        </div>
    </form>
</div>
