@php
    $enHome = request()->routeIs('puntos.index');
    $enMapa = $enHome && request('vista') === 'mapa';
    $gruposCategoria = \App\Models\CategoriaGrupo::orderBy('orden')->get();
@endphp

{{-- Barra unificada: logo + grupos + buscar + panoramas/listado/mapa/idioma. Ocupa todo el ancho, por encima del sidebar. Se compacta al bajar.
     Fixed (no flex): no debe ocupar espacio como hermano del contenido en el "flex min-h-screen" del layout. --}}
<div class="hidden md:block fixed inset-x-0 top-0 z-50 bg-white/95 backdrop-blur-sm border-b transition-all duration-300 px-6"
     x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 60"
     :class="scrolled ? 'py-2 border-gray-200 shadow-sm' : 'py-4 border-transparent'">

        <div class="grid grid-cols-3 items-center gap-4">
            {{-- Izquierda: logo + íconos de grupo --}}
            <div class="justify-self-start flex items-center gap-3 shrink-0">
                <a href="{{ route('puntos.index') }}" class="shrink-0 flex items-center">
                    <span class="text-xl font-black tracking-tight leading-none">
                        <span class="text-[#fc5648]">Pin</span><span class="text-gray-900">door</span>
                    </span>
                </a>

                {{-- Íconos de grupo (sin texto) — en el inicio filtran Listado (Livewire) y Mapa a la vez;
                     en el resto del sitio navegan al inicio ya filtrados. --}}
                <div class="flex items-center gap-1.5 border-l border-gray-200 pl-3">
                    @if($enHome)
                        <button data-grupo="" title="{{ __('ui.home.todos_label') }}"
                                class="grupo-pill w-9 h-9 flex items-center justify-center rounded-xl border transition-colors
                                       {{ !request('grupo') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                            <i class="fa-solid fa-border-all"></i>
                        </button>
                        @foreach($gruposCategoria as $grupo)
                        <button data-grupo="{{ $grupo->slug }}" title="{{ $grupo->nombre }}"
                                class="grupo-pill w-9 h-9 flex items-center justify-center rounded-xl border transition-colors
                                       {{ request('grupo') == $grupo->slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                            <i class="fa-solid fa-{{ $grupo->icono ?: 'tag' }}"></i>
                        </button>
                        @endforeach
                    @else
                        <a href="{{ route('puntos.index') }}" title="{{ __('ui.home.todos_label') }}"
                           class="grupo-pill w-9 h-9 flex items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-500 hover:border-gray-500 transition-colors">
                            <i class="fa-solid fa-border-all"></i>
                        </a>
                        @foreach($gruposCategoria as $grupo)
                        <a href="{{ route('puntos.index', ['grupo' => $grupo->slug]) }}" title="{{ $grupo->nombre }}"
                           class="grupo-pill w-9 h-9 flex items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-500 hover:border-gray-500 transition-colors">
                            <i class="fa-solid fa-{{ $grupo->icono ?: 'tag' }}"></i>
                        </a>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Buscar (centro) --}}
            @if($enHome)
            <div id="barra-buscar-wrap" class="relative w-full max-w-md justify-self-center">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" id="barra-buscar-input" oninput="onBuscarInput(this.value)"
                       value="{{ request('search') }}"
                       placeholder="{{ __('ui.home.buscar_placeholder') }}"
                       class="w-full pl-9 pr-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fc5648] outline-none text-sm transition-all duration-300"
                       :class="scrolled ? 'py-1.5' : 'py-2.5'">
            </div>
            @else
            <form action="{{ route('puntos.index') }}" method="GET" id="barra-buscar-wrap" class="relative w-full max-w-md justify-self-center">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" id="barra-buscar-input"
                       placeholder="{{ __('ui.home.buscar_placeholder') }}"
                       class="w-full pl-9 pr-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fc5648] outline-none text-sm transition-all duration-300"
                       :class="scrolled ? 'py-1.5' : 'py-2.5'">
            </form>
            @endif

            {{-- Derecha: Panoramas + toggle Listado/Mapa + idioma --}}
            <div class="justify-self-end flex items-center gap-3 shrink-0">
                <a href="{{ route('atractivos.panoramas') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-bold transition-colors
                          {{ request()->routeIs('atractivos.panoramas') ? 'text-[#fc5648] bg-[#fff0ef]' : 'text-gray-500 hover:text-[#fc5648] hover:bg-[#fff0ef]' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ __('ui.nav.panoramas') }}
                </a>
                <div class="inline-flex bg-gray-200 p-1 rounded-xl gap-1">
                    @if($enHome)
                        <button id="btn-listado" onclick="setView('listado')"
                                class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all
                                       {{ !$enMapa ? 'bg-white shadow text-[#fc5648]' : 'text-gray-500 hover:text-gray-700' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            {{ __('ui.nav.listado') }}
                        </button>
                        <button id="btn-mapa" onclick="setView('mapa')"
                                class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all
                                       {{ $enMapa ? 'bg-white shadow text-[#fc5648]' : 'text-gray-500 hover:text-gray-700' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            {{ __('ui.nav.mapa') }}
                        </button>
                    @else
                        <a href="{{ route('puntos.index') }}"
                           class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            {{ __('ui.nav.listado') }}
                        </a>
                        <a href="{{ route('puntos.index', ['vista' => 'mapa']) }}"
                           class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            {{ __('ui.nav.mapa') }}
                        </a>
                    @endif
                </div>
                <div class="flex items-center gap-1 border-l border-gray-200 pl-3 text-xs font-bold">
                    <a href="{{ route('lang.switch', 'es') }}"
                       class="{{ app()->getLocale() === 'es' ? 'text-[#fc5648]' : 'text-gray-400 hover:text-gray-700' }} px-1 transition">🇪🇸</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('lang.switch', 'en') }}"
                       class="{{ app()->getLocale() === 'en' ? 'text-[#fc5648]' : 'text-gray-400 hover:text-gray-700' }} px-1 transition">🇬🇧</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('lang.switch', 'fr') }}"
                       class="{{ app()->getLocale() === 'fr' ? 'text-[#fc5648]' : 'text-gray-400 hover:text-gray-700' }} px-1 transition">🇫🇷</a>
                </div>
            </div>
        </div>
    </div>
