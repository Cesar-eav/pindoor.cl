<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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
    {{-- Open Graph / Twitter Card — páginas hijas pueden pisar con @section('og_*') --}}
    <meta property="og:site_name"        content="Pindoor" />
    <meta property="og:locale"           content="es_CL" />
    <meta property="og:type"             content="@yield('og_type', 'website')" />
    <meta property="og:url"              content="@yield('og_url', url()->current())" />
    <meta property="og:title"            content="@yield('og_title', 'Pindoor · Guía de lugares en Valparaíso')" />
    <meta property="og:description"      content="@yield('og_description', 'Descubre restaurantes, cafés, museos, miradores y atracciones turísticas en Valparaíso. La guía local completa.')" />
    <meta property="og:image"            content="@yield('og_image', 'https://pindoor.cl/og.jpg')" />
    <meta property="og:image:width"      content="1200" />
    <meta property="og:image:height"     content="630" />
    <meta property="og:image:alt"        content="@yield('og_title', 'Pindoor · Valparaíso')" />
    <meta name="twitter:card"            content="summary_large_image" />
    <meta name="twitter:site"            content="@pindoor_cl" />
    <meta name="twitter:title"           content="@yield('og_title', 'Pindoor · Guía de lugares en Valparaíso')" />
    <meta name="twitter:description"     content="@yield('og_description', 'Descubre restaurantes, cafés, museos, miradores y atracciones turísticas en Valparaíso.')" />
    <meta name="twitter:image"           content="@yield('og_image', 'https://pindoor.cl/og.jpg')" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @yield('head')
    @if(app()->environment('production'))
    <script>
    (function() {
        if (window.location.search.includes('no_tracking=1')) {
            document.cookie = 'no_tracking=1; path=/; max-age=31536000; SameSite=Lax';
        }
        if (document.cookie.indexOf('no_tracking=1') !== -1) return;
        (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","wajfuymjy1");
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-K7X98GZ8');
    })();
    </script>
    @endif

</head>
<body class="font-sans @yield('bodyClass', 'bg-gray-100 text-gray-900')" style="padding-bottom: var(--inset-bottom, 0px);">
@if(app()->environment('production'))
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K7X98GZ8" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

<div class="flex min-h-screen">
    {{-- Sidebar lateral: solo desktop --}}
    <x-nav_lateral />

    {{-- Selector de idioma (desktop, esquina superior derecha) --}}
    <div class="fixed top-3 right-3 z-50 hidden md:flex items-center gap-1 bg-white/90 backdrop-blur-sm border border-gray-200 rounded-full px-2 py-1 shadow-sm text-xs font-bold">
        <a href="{{ route('lang.switch', 'es') }}"
           class="{{ app()->getLocale() === 'es' ? 'text-[#fc5648]' : 'text-gray-400 hover:text-gray-700' }} px-1 transition">🇪🇸</a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('lang.switch', 'en') }}"
           class="{{ app()->getLocale() === 'en' ? 'text-[#fc5648]' : 'text-gray-400 hover:text-gray-700' }} px-1 transition">🇬🇧</a>
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
     class="hidden fixed left-0 right-0 z-50 flex-wrap justify-center gap-3 px-4 md:hidden"
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
            <span class="text-[10px] font-bold leading-none">{{ __('ui.fab.cerca') }}</span>
        </button>
    </form>

    {{-- Experiencias --}}
    <a href="{{ route('experiencias.index') }}" onclick="closeFab()"
       class="flex flex-col items-center gap-1.5 bg-[#fc5648] text-white px-5 py-3.5 rounded-2xl shadow-xl min-w-19">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-[10px] font-bold leading-none">{{ __('ui.fab.experiencias') }}</span>
    </a>

    {{-- Contacto --}}
    <a href="{{ route('contacto.index') }}" onclick="closeFab()"
       class="flex flex-col items-center gap-1.5 bg-white border border-gray-200 text-gray-700 px-5 py-3.5 rounded-2xl shadow-xl min-w-19">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        <span class="text-[10px] font-bold leading-none">{{ __('ui.fab.contacto') }}</span>
    </a>

    {{-- Artistas --}}
    <a href="{{ route('artista.index') }}" onclick="closeFab()"
       class="flex flex-col items-center gap-1.5 bg-violet-600 text-white px-5 py-3.5 rounded-2xl shadow-xl min-w-19">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
        </svg>
        <span class="text-[10px] font-bold leading-none">Escena</span>
    </a>

    {{-- Información --}}
    <a href="{{ route('puntos.info') }}" onclick="closeFab()"
       class="flex flex-col items-center gap-1.5 bg-white border border-gray-200 text-gray-700 px-5 py-3.5 rounded-2xl shadow-xl min-w-19">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span class="text-[10px] font-bold leading-none">Info</span>
    </a>

</div>

{{-- Barra inferior --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#fc5648] shadow-[0_-2px_16px_rgba(252,86,72,0.35)]">
    <div class="flex items-end justify-around px-2 pt-2" style="padding-bottom: calc(12px + var(--inset-bottom, 0px))">

        {{-- Inicio --}}
        <a href="{{ route('puntos.index') }}"
           class="flex flex-col items-center gap-1 px-2 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4a1 1 0 001-1v-4h2v4a1 1 0 001 1h4a1 1 0 001-1V10"/>
            </svg>
            <span class="text-[10px] font-semibold">{{ __('ui.nav.inicio') }}</span>
        </a>

                {{-- Lupa / Buscar --}}
        <a href="{{ route('puntos.buscar.vista') }}"
           class="flex flex-col items-center gap-1 px-2 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <span class="text-[10px] font-semibold">{{ __('ui.nav.buscar') }}</span>
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
        <button onclick="if(typeof setView==='function'){setView('mapa')}else{window.location='{{ route('puntos.index') }}?vista=mapa'}"
                class="flex flex-col items-center gap-1 px-2 text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span class="text-[10px] font-semibold">{{ __('ui.nav.mapa') }}</span>
        </button>

        {{-- Instagram --}}
        <a href="https://www.instagram.com/pindoor.cl/" target="_blank" rel="noopener noreferrer"
           class="flex flex-col items-center gap-1 px-2 text-white">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
            <span class="text-[10px] font-semibold">{{ __('ui.nav.instagram') }}</span>
        </a>

    </div>
</nav>


<script>
    function toggleSearch() {
        const bar = document.getElementById('search-bar');
        if (bar) {
            bar.classList.toggle('hidden');
            if (!bar.classList.contains('hidden')) bar.querySelector('input[type=text]')?.focus();
        }
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

<script>
function sharePanel() {
    return {
        open: false, copiado: false, text: '', image: '', file: null, fetchingFile: false,
        // Al hacer click en el ícono: si el navegador soporta compartir nativo, lo dispara
        // directo (el sheet del sistema ya prioriza las apps más usadas por la persona).
        // Si no lo soporta (desktop), abre el panel de fallback (WhatsApp / Copiar enlace).
        click() {
            if ('share' in navigator) {
                this.nativo();
            } else {
                this.toggle();
            }
        },
        toggle() { this.open = !this.open; },
        async prefetchImage() {
            this.fetchingFile = true;
            try {
                const res = await fetch(this.image);
                const blob = await res.blob();
                this.file = new File([blob], 'pindoor.jpg', { type: blob.type || 'image/jpeg' });
            } catch (e) {
                this.file = null;
            }
            this.fetchingFile = false;
        },
        nativo() {
            this.open = false;
            if (this.file && navigator.canShare && navigator.canShare({ files: [this.file] })) {
                navigator.share({ text: this.text, files: [this.file] })
                    .catch(() => navigator.share({ text: this.text }).catch(() => {}));
                return;
            }
            navigator.share({ text: this.text }).catch(() => {});
        },
        wa() { window.location.href = 'whatsapp://send?text=' + encodeURIComponent(this.text); this.open = false; },
        copiar() {
            navigator.clipboard?.writeText(this.text).then(() => {
                this.copiado = true;
                setTimeout(() => { this.copiado = false; this.open = false; }, 1500);
            });
        },
    };
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
