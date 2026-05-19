<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Lora:wght@400;500&display=swap" rel="stylesheet">
    <meta name="robots" content="index, follow" />
    <title>@yield('title', 'Pindoor · Guía de lugares en Valparaíso')</title>
    <meta name="description" content="@yield('description', 'Descubre restaurantes, hoteles, museos, bares y atracciones turísticas en Valparaíso. La guía local completa de Pindoor.')">
    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')" />
    @endif
    <meta property="og:site_name" content="Pindoor" />
    <meta property="og:locale" content="es_CL" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
    @if(app()->environment('production'))
    <script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","wajfuymjy1");</script>
    @endif
</head>
<body class="@yield('bodyClass', 'bg-gray-100 text-gray-900 font-serif')">

<div class="flex min-h-screen">
    {{-- Sidebar lateral: solo desktop --}}
    <x-nav_lateral />

    {{-- Contenido principal --}}
    <div class="flex-1 min-w-0 md:ml-56">
        <x-mobile.appbar>
            <x-slot:actions>
                @yield('appbar-actions')
            </x-slot:actions>
        </x-mobile.appbar>

        @yield('content')
    </div>
</div>

{{-- ── Drawer mobile (global) ──────────────────────────────────────────── --}}
<div id="drawer-overlay"
     onclick="closeDrawer()"
     class="hidden fixed inset-0 bg-black/40 z-50 md:hidden">
</div>

<div id="drawer"
     class="fixed top-0 right-0 bottom-0 w-72 bg-white z-50 shadow-2xl translate-x-full md:hidden flex flex-col font-sans"
     style="transition: transform .28s cubic-bezier(.4,0,.2,1);">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <span class="font-bold text-gray-800">Explorar</span>
        <button onclick="closeDrawer()" class="text-gray-400 text-xl leading-none">✕</button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 space-y-6">
        {{-- GPS --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Cerca de mí</p>
            <form id="filterForm-mobile" action="{{ route('puntos.index') }}" method="GET">
                <input type="hidden" id="lat-m" name="lat" value="{{ request('lat') }}">
                <input type="hidden" id="lng-m" name="lng" value="{{ request('lng') }}">
                <button type="button" id="btn-gps-m"
                        class="w-full flex items-center justify-center gap-2 bg-gray-900 text-white py-3 rounded-xl font-bold text-sm">
                    📍 Buscar cerca de mí
                </button>
                @if(request('lat'))
                <p class="text-xs text-green-600 text-center mt-2 font-semibold">✓ Mostrando por cercanía</p>
                @endif
            </form>
        </div>
        {{-- Vista listado/mapa --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Vista</p>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('puntos.index', ['vista' => 'listado']) }}"
                   onclick="if(typeof setView==='function'){event.preventDefault();setView('listado');} closeDrawer();"
                   class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 border-[#fc5648] bg-[#fff0ef]">
                    <svg class="w-5 h-5 text-[#fc5648]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span class="text-xs font-bold text-[#fc5648]">Listado</span>
                </a>
                <a href="{{ route('puntos.index', ['vista' => 'mapa']) }}"
                   onclick="if(typeof setView==='function'){event.preventDefault();setView('mapa');} closeDrawer();"
                   class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 border-gray-200 bg-gray-50">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span class="text-xs font-bold text-gray-500">Mapa</span>
                </a>
            </div>
        </div>

        {{-- Navegación --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Explorar</p>
            <div class="space-y-1">
                <a href="{{ route('puntos.index') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition text-gray-600">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="text-sm font-semibold">Inicio</span>
                </a>

                {{-- Panoramas con subcategorías --}}
                @php
                    $panoramasActivos = \App\Models\Panorama::activos()->get();
                    $conteoCategoriasDrawer = $panoramasActivos->groupBy('categoria')->map->count();
                    $conteoGratisDrawer     = $panoramasActivos->where('es_gratuito', true)->count();
                    $totalPanoramasDrawer   = $panoramasActivos->count();
                @endphp
                <div x-data="{ open: {{ request()->routeIs('atractivos.panoramas') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="w-full flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition text-gray-600">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-semibold flex-1 text-left">Panoramas</span>
                        @if($totalPanoramasDrawer > 0)
                        <span class="text-[10px] font-bold bg-[#fc5648] text-white rounded-full px-1.5 py-0.5 leading-none">{{ $totalPanoramasDrawer }}</span>
                        @endif
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="pl-8 pb-1 space-y-0.5">
                        <a href="{{ route('atractivos.panoramas') }}"
                           onclick="closeDrawer()"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition
                                  {{ !request('categoria') && request()->routeIs('atractivos.panoramas') ? 'text-[#fc5648] bg-[#fff0ef]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span class="flex-1">Todos</span>
                            @if($totalPanoramasDrawer > 0)
                            <span class="text-[10px] text-gray-400 font-normal">{{ $totalPanoramasDrawer }}</span>
                            @endif
                        </a>
                        @foreach(\App\Models\Panorama::CATEGORIAS as $slug => $cat)
                        @if(($conteoCategoriasDrawer[$slug] ?? 0) > 0)
                        <a href="{{ route('atractivos.panoramas', ['categoria' => $slug]) }}"
                           onclick="closeDrawer()"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition
                                  {{ request('categoria') === $slug ? 'text-[#fc5648] bg-[#fff0ef]' : 'text-gray-600 hover:bg-gray-50' }}">
                            <span>{{ $cat['emoji'] }}</span>
                            <span class="flex-1">{{ $cat['label'] }}</span>
                            <span class="text-[10px] text-gray-400 font-normal">{{ $conteoCategoriasDrawer[$slug] }}</span>
                        </a>
                        @endif
                        @endforeach
                        @if($conteoGratisDrawer > 0)
                        <a href="{{ route('atractivos.panoramas', ['categoria' => 'gratuito']) }}"
                           onclick="closeDrawer()"
                           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition
                                  {{ request('categoria') === 'gratuito' ? 'text-green-700 bg-green-50' : 'text-green-700 hover:bg-green-50' }}">
                            <span>🎟️</span>
                            <span class="flex-1">Gratis</span>
                            <span class="text-[10px] font-normal opacity-70">{{ $conteoGratisDrawer }}</span>
                        </a>
                        @endif
                    </div>
                </div>

                <a href="{{ route('publicita.index') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition text-gray-600">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span class="text-sm font-semibold">Registrate</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSearch() {
        const bar = document.getElementById('search-bar');
        if (bar) {
            bar.classList.toggle('hidden');
            if (!bar.classList.contains('hidden')) bar.querySelector('input[type=text]')?.focus();
        }
    }
    function openDrawer() {
        document.getElementById('drawer-overlay').classList.remove('hidden');
        document.getElementById('drawer').classList.remove('translate-x-full');
    }
    function closeDrawer() {
        document.getElementById('drawer-overlay').classList.add('hidden');
        document.getElementById('drawer').classList.add('translate-x-full');
    }
</script>

@yield('scripts')
</body>
</html>
