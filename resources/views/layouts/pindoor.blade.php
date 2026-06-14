<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
    <link rel="manifest" href="/manifest.json" />
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}" />
    <meta name="theme-color" content="#fc5648" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="apple-mobile-web-app-title" content="Pindoor" />
    <link rel="preconnect" href="https://basemaps.cartocdn.com" crossorigin>
    <link rel="dns-prefetch" href="https://basemaps.cartocdn.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Lora:wght@400;500&display=swap" rel="stylesheet">
    <meta name="robots" content="@yield('robots', 'index, follow')" />
    <title>@yield('title', 'Pindoor · Guía de lugares en Valparaíso')</title>
    <meta name="description" content="@yield('description', 'Descubre restaurantes, hoteles, museos, bares y atracciones turísticas en Valparaíso. La guía local completa de Pindoor.')">
    @hasSection('canonical')
        <link rel="canonical" href="@yield('canonical')" />
    @endif
    <meta property="og:site_name" content="Pindoor" />
    <meta property="og:locale" content="es_CL" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @yield('head')
    @if(app()->environment('production'))
    <script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","wajfuymjy1");</script>
    @endif
</head>
<body class="font-sans @yield('bodyClass', 'bg-gray-100 text-gray-900')" style="padding-bottom: var(--inset-bottom, 0px);">

<div class="flex min-h-screen">
    {{-- Sidebar lateral: solo desktop --}}
    <x-nav_lateral />

    {{-- Selector de idioma (desktop, esquina superior derecha) --}}
    <div class="fixed top-3 right-3 z-50 hidden md:flex items-center gap-1 bg-white/90 backdrop-blur-sm border border-gray-200 rounded-full px-2 py-1 shadow-sm text-xs font-bold">
        <a href="{{ route('lang.switch', 'es') }}"
           class="{{ app()->getLocale() === 'es' ? 'text-[#fc5648]' : 'text-gray-400 hover:text-gray-700' }} px-1 transition">ES</a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('lang.switch', 'en') }}"
           class="{{ app()->getLocale() === 'en' ? 'text-[#fc5648]' : 'text-gray-400 hover:text-gray-700' }} px-1 transition">EN</a>
    </div>

    {{-- Contenido principal --}}
    <div class="flex-1 min-w-0 md:ml-56 pb-16 md:pb-0">
        <x-mobile.appbar>
            <x-slot:actions>
                @yield('appbar-actions')
            </x-slot:actions>
        </x-mobile.appbar>

        @yield('content')
    </div>
</div>

{{-- ── Bottom Nav mobile (global) ───────────────────────────────────────── --}}

{{-- Overlay FAB --}}
<div id="fab-overlay" onclick="closeFab()"
     class="hidden fixed inset-0 z-40 md:hidden"></div>

{{-- FAB expandido: fila horizontal --}}
<div id="fab-menu"
     class="hidden fixed left-0 right-0 z-50 justify-center gap-3 px-4 md:hidden"
     style="bottom: calc(80px + var(--inset-bottom, 0px))">

    {{-- GPS --}}
    <form id="filterForm-mobile" action="{{ route('puntos.index') }}" method="GET">
        <input type="hidden" id="lat-m" name="lat" value="{{ request('lat') }}">
        <input type="hidden" id="lng-m" name="lng" value="{{ request('lng') }}">
        <button type="button" id="btn-gps-m" onclick="geolocateFab(this)"
                class="flex flex-col items-center gap-1.5 bg-gray-900 text-white px-5 py-3.5 rounded-2xl shadow-xl min-w-19">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            {{-- Mobile --}}
            <span class="text-[10px] font-bold leading-none">Cerca de mí</span>
        </button>
    </form>

    {{-- Experiencias --}}
    <a href="{{ route('experiencias.index') }}" onclick="closeFab()"
       class="flex flex-col items-center gap-1.5 bg-[#fc5648] text-white px-5 py-3.5 rounded-2xl shadow-xl min-w-19">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-[10px] font-bold leading-none">Experiencias</span>
    </a>

    {{-- Contacto --}}
    <a href="{{ route('contacto.index') }}" onclick="closeFab()"
       class="flex flex-col items-center gap-1.5 bg-white border border-gray-200 text-gray-700 px-5 py-3.5 rounded-2xl shadow-xl min-w-19">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <span class="text-[10px] font-bold leading-none">Contáctanos</span>
    </a>

</div>

{{-- Barra inferior --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#fc5648] shadow-[0_-2px_16px_rgba(252,86,72,0.35)]">
    <div class="flex items-end justify-around px-2 pt-2" style="padding-bottom: calc(12px + var(--inset-bottom, 0px))">

        {{-- Información --}}
        <a href="#" class="flex flex-col items-center gap-1 px-2 opacity-40 pointer-events-none" title="Próximamente">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-[10px] font-semibold text-white">Info</span>
        </a>

        {{-- Lupa / Buscar --}}
        <a href="{{ route('puntos.buscar.vista') }}"
           class="flex flex-col items-center gap-1 px-2 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-[10px] font-semibold">Buscar</span>
        </a>

        {{-- Botón (+) central --}}
        <button onclick="toggleFab()" id="fab-btn"
                class="relative -mt-5 w-14 h-14 rounded-full bg-white text-[#fc5648] shadow-lg flex items-center justify-center transition-transform duration-200">
            <svg id="fab-icon-plus" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <svg id="fab-icon-close" class="w-7 h-7 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        {{-- Mapa --}}
        <button onclick="if(typeof setView==='function'){setView('mapa')}else if(typeof Livewire!=='undefined'){Livewire.navigate('{{ route('puntos.index') }}#mapa')}else{window.location='{{ route('puntos.index') }}#mapa'}"
                class="flex flex-col items-center gap-1 px-2 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span class="text-[10px] font-semibold">Mapa</span>
        </button>

        {{-- Instagram --}}
        <a href="https://www.instagram.com/pindoor.cl/" target="_blank" rel="noopener noreferrer"
           class="flex flex-col items-center gap-1 px-2 text-white">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
            <span class="text-[10px] font-semibold">Instagram</span>
        </a>

    </div>
</nav>

{{-- Drawer (mantenido para desktop, oculto en mobile) --}}
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
                <a href="{{ route('puntos.index') }}#mapa"
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

                {{-- <a href="{{ route('artistas.index') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition text-gray-600">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span class="text-sm font-semibold">Artistas </span>
                </a>
                <a href="{{ route('publicita.index') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition text-gray-600">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                    <span class="text-sm font-semibold">Registrate </span>
                </a> --}}

                <a href="{{ route('experiencias.index') }}"
                   onclick="closeDrawer()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl transition
                          {{ request()->routeIs('experiencias.*') ? 'bg-[#fff0ef] text-[#fc5648]' : 'hover:bg-gray-50 text-gray-600' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-semibold">Experiencias</span>
                </a> 

                <a href="{{ route('blog.index') }}"
                   onclick="closeDrawer()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl transition
                          {{ request()->routeIs('blog.*') ? 'bg-[#fff0ef] text-[#fc5648]' : 'hover:bg-gray-50 text-gray-600' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    <span class="text-sm font-semibold">Blog</span>
                </a>

                <a href="{{ route('contacto.index') }}"
                   onclick="closeDrawer()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl transition
                          {{ request()->routeIs('contacto.index') ? 'bg-[#fff0ef] text-[#fc5648]' : 'hover:bg-gray-50 text-gray-600' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-semibold">Contáctanos</span>
                </a>

                <a href="https://www.instagram.com/pindoor.cl/" target="_blank" rel="noopener noreferrer"
                   onclick="closeDrawer()"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl text-white font-bold text-sm transition hover:opacity-90"
                   style="background: linear-gradient(135deg, #405DE6 0%, #833AB4 30%, #E1306C 60%, #F77737 85%, #FCAF45 100%);">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span>@pindoor.cl</span>
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
        document.getElementById('drawer-overlay')?.classList.remove('hidden');
        document.getElementById('drawer')?.classList.remove('translate-x-full');
    }
    function closeDrawer() {
        document.getElementById('drawer-overlay')?.classList.add('hidden');
        document.getElementById('drawer')?.classList.add('translate-x-full');
    }
    function toggleFab() {
        const menu    = document.getElementById('fab-menu');
        const overlay = document.getElementById('fab-overlay');
        const plus    = document.getElementById('fab-icon-plus');
        const close   = document.getElementById('fab-icon-close');
        const isOpen  = !menu.classList.contains('hidden');
        if (isOpen) {
            menu.classList.add('hidden');
            menu.classList.remove('flex');
            overlay.classList.add('hidden');
            plus.classList.remove('hidden');
            close.classList.add('hidden');
        } else {
            menu.classList.remove('hidden');
            menu.classList.add('flex');
            overlay.classList.remove('hidden');
            plus.classList.add('hidden');
            close.classList.remove('hidden');
        }
    }
    function closeFab() {
        const menu = document.getElementById('fab-menu');
        menu?.classList.add('hidden');
        menu?.classList.remove('flex');
        document.getElementById('fab-overlay')?.classList.add('hidden');
        document.getElementById('fab-icon-plus')?.classList.remove('hidden');
        document.getElementById('fab-icon-close')?.classList.add('hidden');
    }
    function geolocateFab(btn) {
        if (!navigator.geolocation) { alert('Tu navegador no soporta geolocalización.'); return; }
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '⌛ Localizando…';
        navigator.geolocation.getCurrentPosition(
            pos => {
                document.getElementById('lat-m').value = pos.coords.latitude;
                document.getElementById('lng-m').value = pos.coords.longitude;
                document.getElementById('filterForm-mobile').submit();
            },
            () => {
                alert('No se pudo obtener tu ubicación.');
                btn.disabled = false;
                btn.innerHTML = orig;
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }
</script>

@yield('scripts')
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(() => {});
    });
}
</script>
@livewireScripts
</body>
</html>
