{{-- App Bar mobile —————————————————————————————————————————— --}}
<header class="md:hidden sticky top-0 z-40 bg-white border-b border-gray-100 px-4 pb-3 flex items-center justify-between shadow-sm" style="padding-top: calc(12px + var(--inset-top, 0px))">
    <a href="{{ route('puntos.index') }}" class="text-lg font-bold tracking-tight ">
        <span class="text-[#fc5648]">Pin</span>door
    </a>
    <nav class="flex items-center gap-1">
        <a href="{{ route('puntos.explorar') }}"
           class="px-3 py-1.5 text-xs font-bold transition-colors
                  {{ request()->routeIs('puntos.explorar') || request()->routeIs('puntos.buscar') ? 'bg-[#fc5648] text-white' : 'text-gray-500 hover:text-gray-800' }}">
            Atractivos
        </a>
        <a href="{{ route('atractivos.panoramas') }}"
           class="px-3 py-1.5  text-xs font-bold transition-colors
                  {{ request()->routeIs('atractivos.panoramas') ? 'bg-[#fc5648] text-white' : 'text-gray-500 hover:text-gray-800' }}">
            Panoramas
        </a>
    </nav>
</header>

{{-- Barra de búsqueda (slide) —————————————————————————————————— --}}
<div id="search-bar" class="md:hidden hidden bg-white border-b border-gray-100 px-4 py-3 shadow-sm">
    <form action="{{ route('puntos.index') }}" method="GET">
        <div class="flex gap-2">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Café, mirador, ascensor…"
                       autofocus
                       class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#fc5648] outline-none bg-gray-50">
            </div>
            <button type="submit" class="bg-[#fc5648] text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                Buscar
            </button>
        </div>
    </form>
</div>
