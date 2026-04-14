@php
    use Illuminate\Support\Str;

    $hayFiltros = request()->anyFilled(['category', 'search', 'lat']);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pindoor · La Brújula de Valparaíso</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        #mapa-principal { height: 70vh; border-radius: 1rem; z-index: 1; }
        .leaflet-popup-content-wrapper { border-radius: .75rem; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
        .leaflet-popup-content { margin: 0; padding: 0; width: 220px !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        /* Drawer lateral */
        #drawer { transition: transform .28s cubic-bezier(.4,0,.2,1); }
    </style>

    @if(app()->environment('production'))
    <script>(function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script","wajfuymjy1");</script>
    @endif
</head>

<body class="bg-gray-100 text-gray-900 font-serif">

{{-- ══════════════════════════════════════════════════════════════════
     MOBILE  (< md)
══════════════════════════════════════════════════════════════════ --}}
<div class="md:hidden flex flex-col min-h-screen">

    {{-- ── App Bar sticky ──────────────────────────────────────────────────── --}}
    <header class="sticky top-0 z-40 bg-white border-b border-gray-100 px-4 py-3 flex items-center justify-between shadow-sm">
        <a href="{{ route('atractivos.index') }}" class="text-lg font-bold tracking-tight">
            <span class="text-[#fc5648]">Pin</span>door
        </a>

        <div class="flex items-center gap-3">
            {{-- Buscar --}}
            <button onclick="toggleSearch()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>

            {{-- Indicador de filtro activo --}}
            @if($hayFiltros)
            <a href="{{ route('atractivos.index') }}"
               class="w-9 h-9 flex items-center justify-center rounded-xl bg-[#fc5648] text-white text-sm font-bold">
                ✕
            </a>
            @endif

            {{-- Hamburguesa --}}
            <button onclick="openDrawer()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </header>

    {{-- ── Barra de búsqueda (slide) ───────────────────────────────────────── --}}
    <div id="search-bar" class="hidden bg-white border-b border-gray-100 px-4 py-3 shadow-sm">
        <form action="{{ route('atractivos.index') }}" method="GET">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="flex gap-2">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" autofocus
                           placeholder="Café, mirador, ascensor…"
                           class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-[#fc5648] outline-none bg-gray-50">
                </div>
                <button type="submit"
                        class="bg-[#fc5648] text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                    Buscar
                </button>
            </div>
        </form>
    </div>

    {{-- ── Filtro de categorías (pills) ───────────────────────────────────── --}}
    <div class="overflow-x-auto no-scrollbar bg-white border-b border-gray-100 px-4 py-3">
        <div class="flex gap-2 w-max">
            <a href="{{ route('atractivos.index', array_filter(['search' => request('search')])) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap
                      {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                Todos
            </a>

            @foreach($categorias as $cat)
            <a href="{{ route('atractivos.index', array_filter(['category' => $cat->slug, 'search' => request('search')])) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap
                      {{ request('category') == $cat->slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                {{ $cat->nombre }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── Indicador de búsqueda activa ────────────────────────────────────── --}}
    @if($hayFiltros)
    <div class="mx-3 mb-2 px-3 py-2 bg-[#fff0ef] rounded-xl flex items-center justify-between">
        <span class="text-xs text-[#fc5648] font-semibold">
            @if(request('search')) "{{ request('search') }}"
            @elseif(request('category')) {{ $categorias->firstWhere('slug', request('category'))?->nombre }}
            @elseif(request('lat')) Cerca de ti
            @endif
        </span>
        <a href="{{ route('atractivos.index') }}" class="text-xs text-gray-400 font-bold">✕ Borrar</a>
    </div>
    @endif

    {{-- ── Listado de resultados ────────────────────────────────────────────── --}}
    <div class="flex-1 px-3 pt-3 pb-6">
        @if($atractivos->count())
            <div class="grid grid-cols-1 gap-4">
            @foreach($atractivos as $atractivo)
            <article class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
                <div class="relative">
                    <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}" class="block overflow-hidden">
                        @if($atractivo->imagenPrincipal)
                            <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                                 alt="{{ $atractivo->title }}"
                                 class="w-full h-96 object-cover">
                        @else
                            <div class="w-full h-44 bg-gray-100 flex items-center justify-center text-4xl">📍</div>
                        @endif
                    </a>

                    @if($atractivo->categoria)
                    <span class="absolute top-3 left-3 bg-[#fc5648] text-white text-[9px] uppercase tracking-widest font-bold px-2.5 py-1 rounded-full shadow">
                        {{ $atractivo->categoria->nombre }}
                    </span>
                    @endif

                    @if($atractivo->es_cliente && $atractivo->oferta_del_dia && $atractivo->oferta_activa)
                    <span class="absolute top-3 right-3 bg-amber-400 text-amber-900 text-[9px] uppercase font-bold px-2.5 py-1 rounded-full shadow">
                        Oferta hoy
                    </span>
                    @endif

                    @if($atractivo->sector)
                    <span class="absolute bottom-3 left-3 bg-white text-[#fc5648] text-[9px] uppercase font-bold px-2.5 py-1 rounded-full shadow">
                       📍 {{ $atractivo->sector }}
                    </span>
                    @endif

                    @if($atractivo->es_cliente && $atractivo->imagen_perfil)
                    <img src="{{ asset('storage/' . $atractivo->imagen_perfil) }}"
                         class="absolute bottom-3 right-3 w-10 h-10 rounded-xl object-cover border-2 border-white shadow">
                    @endif
                </div>

                <div class="px-4 py-3">
                    @if(isset($atractivo->distancia))
                    <span class="inline-block mb-1.5 text-[10px] font-bold text-[#fc5648] bg-orange-50 border border-orange-100 px-2 py-0.5 rounded-lg">
                        A {{ number_format($atractivo->distancia / 1000, 1) }} km de ti
                    </span>
                    @endif
                    <h3 class="text-base font-bold text-gray-900 leading-tight mb-1">
                        <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                           class="hover:text-[#fc5648] transition">{{ $atractivo->title }}</a>
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        {{ Str::limit(strip_tags($atractivo->description), 250) }}
                    </p>
                </div>
            </article>
            @endforeach
            </div>

            <div class="mt-8 flex justify-center">
                {{ $atractivos->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <div class="text-5xl mb-3">🕵️‍♂️</div>
                <p class="font-bold text-gray-700 mb-1">Sin resultados</p>
                <p class="text-sm text-gray-400 mb-4">Prueba con otra búsqueda</p>
                <a href="{{ route('atractivos.index') }}"
                   class="text-sm font-bold text-[#fc5648] underline">Ver todos</a>
            </div>
        @endif
    </div>
</div>{{-- /mobile --}}


{{-- ── Drawer hamburguesa (mobile) ────────────────────────────────────── --}}
<div id="drawer-overlay"
     onclick="closeDrawer()"
     class="hidden fixed inset-0 bg-black/40 z-50 md:hidden">
</div>

<div id="drawer"
     class="fixed top-0 right-0 bottom-0 w-72 bg-white z-50 shadow-2xl translate-x-full md:hidden flex flex-col">
    {{-- Header drawer --}}
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <span class="font-bold text-gray-800">Explorar</span>
        <button onclick="closeDrawer()" class="text-gray-400 text-xl leading-none">✕</button>
    </div>

    <div class="flex-1 overflow-y-auto p-5 space-y-6">
        {{-- GPS --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Cerca de mí</p>
            <form id="filterForm-mobile" action="{{ route('atractivos.index') }}" method="GET">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
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

        {{-- Mapa --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Vista</p>
            <div class="grid grid-cols-2 gap-2">
                <button onclick="setView('listado'); closeDrawer();"
                        class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 border-[#fc5648] bg-[#fff0ef]">
                    <svg class="w-5 h-5 text-[#fc5648]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span class="text-xs font-bold text-[#fc5648]">Listado</span>
                </button>
                <button onclick="setView('mapa'); closeDrawer();"
                        class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 border-gray-200 bg-gray-50">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    <span class="text-xs font-bold text-gray-500">Mapa</span>
                </button>
            </div>
        </div>

        {{-- Enlaces --}}
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Explorar</p>
            <div class="space-y-1">
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


{{-- ══════════════════════════════════════════════════════════════════
     DESKTOP  (md+)
══════════════════════════════════════════════════════════════════ --}}
<div class="hidden md:block w-full md:p-4">
    <x-navbar_labrujula />

    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center justify-between my-6">
            <h1 class="text-3xl font-bold text-gray-900">
                <span class="text-red-400">P</span>i<span class="text-red-400">n</span>d<span class="text-red-400">oo</span>r
                
            </h1>
            <div class="inline-flex bg-gray-200 p-1 rounded-xl gap-1">
                <button id="btn-listado" onclick="setView('listado')"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all bg-white shadow text-[#fc5648]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Listado
                </button>
                <button id="btn-mapa" onclick="setView('mapa')"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Mapa
                </button>
            </div>
        </div>

        {{-- Vista Mapa --}}
        <div id="vista-mapa" class="hidden mb-8">
            <div id="mapa-principal"></div>
            <p class="text-xs text-gray-400 text-center mt-2">
                {{ count($puntosMapData) }} puntos · Clic en un marcador para ver el detalle
            </p>
        </div>

        <div id="vista-listado">
            {{-- Filtros desktop --}}
            <div class="bg-white rounded-2xl shadow-sm p-5 mb-8 border border-gray-100">
                <form id="filterForm" action="{{ route('atractivos.index') }}" method="GET">
                    <div class="grid grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Categoría</label>
                            <select id="categoryFilter" name="category"
                                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fc5648] outline-none bg-gray-50 text-sm">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->slug }}" @selected(request('category') == $cat->slug)>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Buscar</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="searchFilter" name="search" value="{{ request('search') }}"
                                       placeholder="Ascensor, café, mirador…"
                                       class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fc5648] outline-none text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Cerca de mí</label>
                            <div class="flex">
                                <input type="hidden" id="lat" name="lat" value="{{ request('lat') }}">
                                <input type="hidden" id="lng" name="lng" value="{{ request('lng') }}">
                                <button type="button" id="btn-gps"
                                        class="w-full bg-gray-900 text-white py-2.5 rounded-xl hover:bg-black transition flex items-center justify-center gap-2 text-sm font-bold">
                                    📍 Buscar cerca de mí
                                </button>
                            </div>
                        </div>
                    </div>

                    @if($hayFiltros)
                    <div class="flex justify-end mt-3 pt-3 border-t border-gray-50">
                        <a href="{{ route('atractivos.index') }}"
                           class="text-sm font-semibold text-gray-400 hover:text-[#fc5648] transition flex items-center gap-1">
                            ✕ Borrar filtros
                        </a>
                    </div>
                    @endif
                </form>
            </div>

            {{-- Grid desktop --}}
            <div class="pb-12">
                @if($atractivos->count())
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($atractivos as $atractivo)
                        <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col">
                            <div class="relative">
                                <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}" class="block overflow-hidden">
                                    @if($atractivo->imagenPrincipal)
                                        <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                                             alt="{{ $atractivo->title }}"
                                             class="w-full h-48 object-cover hover:scale-105 transition-transform duration-500"/>
                                    @else
                                        <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-4xl">📍</div>
                                    @endif
                                </a>
                                @if($atractivo->categoria)
                                <span class="absolute top-3 left-3 bg-[#fc5648] text-white text-[9px] uppercase tracking-widest font-bold px-2.5 py-1 rounded-full">
                                    {{ $atractivo->categoria->nombre }}
                                </span>
                                @endif
                                @if($atractivo->es_cliente && $atractivo->oferta_del_dia && $atractivo->oferta_activa)
                                <span class="absolute top-3 right-3 bg-amber-400 text-amber-900 text-[9px] uppercase tracking-widest font-bold px-2.5 py-1 rounded-full">
                                    Oferta hoy
                                </span>
                                @endif
                                @if($atractivo->es_cliente && $atractivo->imagen_perfil)
                                <img src="{{ asset('storage/' . $atractivo->imagen_perfil) }}"
                                     alt="Logo {{ $atractivo->title }}"
                                     class="absolute bottom-3 right-3 w-10 h-10 rounded-xl object-cover border-2 border-white shadow">
                                @endif
                            </div>

                            <div class="p-4 flex-grow">
                                @if(isset($atractivo->distancia))
                                <span class="inline-block mb-2 bg-orange-50 text-[#fc5648] text-xs font-bold px-2 py-1 rounded-lg border border-orange-100">
                                    A {{ number_format($atractivo->distancia / 1000, 2) }} km
                                </span>
                                @endif
                                <h3 class="text-base font-bold text-gray-900 mb-1 leading-tight">
                                    <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                                       class="hover:text-[#fc5648] transition">{{ $atractivo->title }}</a>
                                </h3>
                                <p class="text-gray-500 text-sm leading-relaxed">
                                    {{ Str::limit(strip_tags($atractivo->description), 100) }}
                                </p>
                            </div>

                        </article>
                        @endforeach
                    </div>

                    <div class="mt-12 mb-4 flex justify-center">
                        {{ $atractivos->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm p-16 text-center border-2 border-dashed border-gray-200">
                        <div class="text-5xl mb-4">🕵️‍♂️</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Sin resultados</h3>
                        <p class="text-gray-400 mb-5 text-sm">No encontramos lugares que coincidan.</p>
                        <a href="{{ route('atractivos.index') }}"
                           class="bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-gray-900 transition text-sm">
                            Ver todos
                        </a>
                    </div>
                @endif
            </div>
        </div>{{-- /vista-listado --}}
    </div>
</div>{{-- /desktop --}}


{{-- ══════════════════════════════════════════════════════════════════
     MAPA (compartido mobile/desktop — se inserta fuera del flujo)
══════════════════════════════════════════════════════════════════ --}}
{{-- El div#mapa-principal ya está dentro del bloque desktop --}}


<script>
const PUNTOS_DATA = @json($puntosMapData);

// ── Drawer hamburguesa ──────────────────────────────────────────────────
function openDrawer() {
    document.getElementById('drawer-overlay').classList.remove('hidden');
    document.getElementById('drawer').classList.remove('translate-x-full');
}
function closeDrawer() {
    document.getElementById('drawer-overlay').classList.add('hidden');
    document.getElementById('drawer').classList.add('translate-x-full');
}

// ── Toggle search bar ───────────────────────────────────────────────────
function toggleSearch() {
    const bar = document.getElementById('search-bar');
    bar.classList.toggle('hidden');
    if (!bar.classList.contains('hidden')) {
        bar.querySelector('input[type=text]').focus();
    }
}

// ── Toggle Listado / Mapa (desktop) ────────────────────────────────────
let mapaIniciado = false;
let mapaLeaflet  = null;

function setView(vista) {
    const elListado = document.getElementById('vista-listado');
    const elMapa    = document.getElementById('vista-mapa');
    const btnL      = document.getElementById('btn-listado');
    const btnM      = document.getElementById('btn-mapa');

    if (vista === 'mapa') {
        elListado?.classList.add('hidden');
        elMapa?.classList.remove('hidden');
        btnM?.classList.add('bg-white','shadow','text-[#fc5648]');
        btnM?.classList.remove('text-gray-500','hover:text-gray-700');
        btnL?.classList.remove('bg-white','shadow','text-[#fc5648]');
        btnL?.classList.add('text-gray-500','hover:text-gray-700');
        if (!mapaIniciado) iniciarMapa();
    } else {
        elMapa?.classList.add('hidden');
        elListado?.classList.remove('hidden');
        btnL?.classList.add('bg-white','shadow','text-[#fc5648]');
        btnL?.classList.remove('text-gray-500');
        btnM?.classList.remove('bg-white','shadow','text-[#fc5648]');
        btnM?.classList.add('text-gray-500','hover:text-gray-700');
    }
}

// ── Leaflet ─────────────────────────────────────────────────────────────
function iniciarMapa() {
    mapaIniciado = true;
    mapaLeaflet = L.map('mapa-principal', { center: [-33.047, -71.612], zoom: 14 });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(mapaLeaflet);

    function crearIcono(esCliente) {
        return L.divIcon({
            className: '',
            html: `<div style="width:14px;height:14px;background:${esCliente?'#f59e0b':'#fc5648'};border:2.5px solid white;border-radius:50%;box-shadow:0 1px 6px rgba(0,0,0,.35);"></div>`,
            iconSize:[14,14], iconAnchor:[7,7], popupAnchor:[0,-10],
        });
    }

    const bounds = [];
    PUNTOS_DATA.forEach(p => {
        if (!p.lat || !p.lng) return;
        bounds.push([p.lat, p.lng]);
        const catBadge    = p.categoria ? `<span style="background:#fc5648;color:white;font-size:9px;font-weight:700;text-transform:uppercase;padding:2px 7px;border-radius:999px;">${p.categoria}</span>` : '';
        const clienteBadge= p.es_cliente ? `<span style="background:#fef3c7;color:#92400e;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin-left:4px;">Negocio</span>` : '';
        const imgHtml     = p.imagen ? `<img src="${p.imagen}" style="width:100%;height:110px;object-fit:cover;border-radius:.5rem .5rem 0 0;">` : `<div style="width:100%;height:60px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border-radius:.5rem .5rem 0 0;">📍</div>`;
        L.marker([p.lat, p.lng], { icon: crearIcono(p.es_cliente) })
            .bindPopup(`<div style="font-family:sans-serif;">${imgHtml}<div style="padding:10px 12px 12px;"><div style="margin-bottom:5px;">${catBadge}${clienteBadge}</div><div style="font-weight:700;font-size:13px;line-height:1.3;margin-bottom:8px;">${p.title}</div><a href="/lugar/${p.slug}" style="background:#fc5648;color:white;font-size:11px;font-weight:700;padding:5px 12px;border-radius:8px;text-decoration:none;display:inline-block;">Ver detalle →</a></div></div>`, { maxWidth: 230 })
            .addTo(mapaLeaflet);
    });
    if (bounds.length > 1) mapaLeaflet.fitBounds(bounds, { padding: [40, 40] });
}

// ── GPS ─────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {

    function geolocate(latInput, lngInput, form, btn) {
        if (!navigator.geolocation) { alert("Tu navegador no soporta geolocalización."); return; }
        btn.disabled = true;
        const orig = btn.innerHTML;
        btn.innerHTML = btn.tagName === 'BUTTON' ? '⌛ Localizando…' : orig;
        navigator.geolocation.getCurrentPosition(
            pos => { latInput.value = pos.coords.latitude; lngInput.value = pos.coords.longitude; form.submit(); },
            ()  => { alert("No pudimos obtener tu ubicación."); btn.disabled = false; btn.innerHTML = orig; },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    // Desktop GPS
    const btnGps   = document.getElementById('btn-gps');
    const fForm    = document.getElementById('filterForm');
    if (btnGps && fForm) {
        btnGps.addEventListener('click', () => geolocate(
            document.getElementById('lat'),
            document.getElementById('lng'),
            fForm, btnGps
        ));
    }

    // Mobile GPS
    const btnGpsM  = document.getElementById('btn-gps-m');
    const fFormM   = document.getElementById('filterForm-mobile');
    if (btnGpsM && fFormM) {
        btnGpsM.addEventListener('click', () => geolocate(
            document.getElementById('lat-m'),
            document.getElementById('lng-m'),
            fFormM, btnGpsM
        ));
    }

    // Desktop: auto-submit al cambiar categoría
    const categoryFilter = document.getElementById('categoryFilter');
    const searchFilter   = document.getElementById('searchFilter');
    const initialCat     = categoryFilter?.value ?? '';
    if (categoryFilter) {
        categoryFilter.addEventListener('change', e => {
            if (e.isTrusted && e.target.value !== initialCat) {
                searchFilter?.parentNode.removeChild(searchFilter);
                fForm.submit();
            }
        });
    }
});
</script>

</body>
</html>
