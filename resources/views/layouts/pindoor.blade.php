<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Pindoor · La Brújula de Valparaíso')</title>
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
     class="fixed top-0 right-0 bottom-0 w-72 bg-white z-50 shadow-2xl translate-x-full md:hidden flex flex-col"
     style="transition: transform .28s cubic-bezier(.4,0,.2,1);">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <span class="font-bold text-gray-800">Explorar</span>
        <button onclick="closeDrawer()" class="text-gray-400 text-xl leading-none">✕</button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 space-y-6">
        {{-- GPS --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Cerca de mí</p>
            <form id="filterForm-mobile" action="{{ route('atractivos.index') }}" method="GET">
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
                <a href="{{ route('atractivos.index', ['vista' => 'listado']) }}"
                   onclick="if(typeof setView==='function'){event.preventDefault();setView('listado');} closeDrawer();"
                   class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 border-[#fc5648] bg-[#fff0ef]">
                    <svg class="w-5 h-5 text-[#fc5648]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span class="text-xs font-bold text-[#fc5648]">Listado</span>
                </a>
                <a href="{{ route('atractivos.index', ['vista' => 'mapa']) }}"
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
                <a href="{{ route('atractivos.index') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition text-gray-700">
                    <span class="text-xl">🏠</span>
                    <span class="text-sm font-semibold">Inicio</span>
                </a>
                <a href="{{ route('atractivos.panoramas') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition text-gray-700">
                    <span class="text-xl">🖼️</span>
                    <span class="text-sm font-semibold">Panoramas</span>
                </a>
                <a href="{{ route('publicita.index') }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-xl hover:bg-gray-50 transition text-gray-700">
                    <span class="text-xl">📣</span>
                    <span class="text-sm font-semibold">Publicita tu negocio</span>
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
