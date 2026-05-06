@php
    use Illuminate\Support\Str;

    $hayFiltros = request()->anyFilled(['category', 'search', 'lat']);
@endphp

@extends('layouts.pindoor')

@section('title', 'Pindoor · Guía de lugares en Valparaíso')

@section('description', 'Explora restaurantes, cafeterías, hoteles, museos, bares y atracciones turísticas en Valparaíso. Filtra por categoría, busca por nombre o activa el GPS para ver qué tienes cerca.')

@section('canonical', route('puntos.index'))

@section('bodyClass', 'bg-gray-100 text-gray-900 font-serif')

@section('head')
    {{-- Open Graph --}}
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('puntos.index') }}" />
    <meta property="og:title" content="Pindoor · Guía de lugares en Valparaíso" />
    <meta property="og:description" content="Explora restaurantes, hoteles, museos y atracciones turísticas en Valparaíso. La guía local más completa." />
    <meta property="og:image" content="{{ asset('img/pindoor-og.jpg') }}" />
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Pindoor · Guía de lugares en Valparaíso" />
    <meta name="twitter:description" content="Explora restaurantes, hoteles, museos y atracciones turísticas en Valparaíso. La guía local más completa." />
    <meta name="twitter:image" content="{{ asset('img/pindoor-og.jpg') }}" />
    {{-- Schema.org: WebSite + Organization --}}
    <script type="application/ld+json">
    [
      {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Pindoor",
        "url": "{{ route('puntos.index') }}",
        "description": "Guía de lugares, restaurantes, hoteles y atracciones turísticas en Valparaíso, Chile.",
        "potentialAction": {
          "@type": "SearchAction",
          "target": {
            "@type": "EntryPoint",
            "urlTemplate": "{{ route('puntos.buscar') }}?search={search_term_string}"
          },
          "query-input": "required name=search_term_string"
        }
      },
      {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Pindoor",
        "url": "{{ route('puntos.index') }}",
        "logo": "{{ asset('img/pindoor-logo.png') }}",
        "contactPoint": {
          "@type": "ContactPoint",
          "contactType": "customer support",
          "availableLanguage": "Spanish"
        }
      }
    ]
    </script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #mapa-principal { height: 70vh; border-radius: 1rem; z-index: 1; }
        .leaflet-popup-content-wrapper { border-radius: .75rem; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
        .leaflet-popup-content { margin: 0; padding: 0; width: 220px !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        #drawer { transition: transform .28s cubic-bezier(.4,0,.2,1); }
    </style>
@endsection


@section('content')

{{-- ══════════════════════════════════════════════════════════════════
     MOBILE  (< md)
══════════════════════════════════════════════════════════════════ --}}
<div class="md:hidden flex flex-col min-h-screen">


            <div class="inline-flex bg-gray-200 p-1 rounded-xl gap-1 justify-center">
                <button id="btn-listado-m" onclick="setView('listado')"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all bg-white shadow text-[#fc5648]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Listado
                </button>
                <button id="btn-mapa-m" onclick="setView('mapa')"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Mapa
                </button>
            </div>
    {{-- ── Filtro de categorías (pills) ───────────────────────────────────── --}}
    <div class="overflow-x-auto no-scrollbar bg-white border-b border-gray-100 px-4 py-3">
        <div class="flex gap-2 w-max">
            <a href="{{ route('puntos.index', array_filter(['search' => request('search')])) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap
                      {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                Todos
            </a>

            @foreach($categorias as $cat)
            <a href="{{ route('puntos.index', array_filter(['category' => $cat->slug, 'search' => request('search')])) }}"
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
        <a href="{{ route('puntos.index') }}" class="text-xs text-gray-400 font-bold">✕ Borrar</a>
    </div>
    @endif

    {{-- ── Listado de resultados ────────────────────────────────────────────── --}}
    <div id="vista-listado-mobile" class="flex-1 px-3 pt-3 pb-6">
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
                    @if($atractivo->sector || $atractivo->direccion)
                    <div class="flex items-center gap-1.5 text-xs mb-2 flex-wrap">
                        @if($atractivo->sector)
                        <span class="flex items-center gap-1 text-[#fc5648] font-semibold">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $atractivo->sector }}
                        </span>
                        @endif
                        @if($atractivo->sector && $atractivo->direccion)
                        <span class="text-gray-300">·</span>
                        @endif
                        @if($atractivo->direccion)
                        <span class="text-gray-400">{{ $atractivo->direccion }}</span>
                        @endif
                    </div>
                    @endif
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
                <a href="{{ route('puntos.index') }}"
                   class="text-sm font-bold text-[#fc5648] underline">Ver todos</a>
            </div>
        @endif
    </div>

    {{-- ── Mapa mobile ─────────────────────────────────────────────────────── --}}
    <div id="vista-mapa-mobile" class="hidden flex-1">
        <div id="mapa-mobile" style="height:70vh;"></div>
    </div>
</div>{{-- /mobile --}}




{{-- ══════════════════════════════════════════════════════════════════
     DESKTOP  (md+)
══════════════════════════════════════════════════════════════════ --}}
<div class="hidden md:block w-full md:p-4">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center justify-between my-6">
            <h1 class="text-3xl font-bold text-gray-900">
                <span class="text-red-400"></span>¿Qué quieres <span class="text-red-400">conocer</span> hoy ?</span>
                
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
                <form id="filterForm" action="{{ route('puntos.index') }}" method="GET">
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
                        <a href="{{ route('puntos.index') }}"
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
                                             class="w-full h-80 object-cover hover:scale-105 transition-transform duration-500"/>
                                    @else
                                        <div class="w-full h-80 bg-gray-100 flex items-center justify-center text-4xl">📍</div>
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


                                @if($atractivo->sector || $atractivo->direccion)
                                <div class="flex items-center gap-1.5 text-xs mb-2 flex-wrap">
                                    @if($atractivo->sector)
                                    <span class="flex items-center gap-1 text-[#fc5648] font-semibold">
                                        <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $atractivo->sector }}
                                    </span>
                                    @endif
                                    @if($atractivo->sector && $atractivo->direccion)
                                    <span class="text-gray-300">·</span>
                                    @endif
                                    @if($atractivo->direccion)
                                    <span class="text-gray-400">{{ $atractivo->direccion }}</span>
                                    @endif
                                </div>
                                @endif

                                <p class="text-gray-500 text-sm leading-relaxed">
                                    {{ Str::limit(strip_tags($atractivo->description), 250) }}
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
                        <a href="{{ route('puntos.index') }}"
                           class="bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-gray-900 transition text-sm">
                            Ver todos
                        </a>
                    </div>
                @endif
            </div>
        </div>{{-- /vista-listado --}}
    </div>
</div>{{-- /desktop --}}


@endsection

@section('scripts')
<script>
const PUNTOS_DATA = @json($puntosMapData);

// ── Toggle search bar ───────────────────────────────────────────────────


// ── Toggle Listado / Mapa (desktop) ────────────────────────────────────
let mapaIniciado = false;
let mapaLeaflet  = null;

function setView(vista) {
    const mobile = window.innerWidth < 768;

    const elListado  = document.getElementById('vista-listado');
    const elMapa     = document.getElementById('vista-mapa');
    const elListadoM = document.getElementById('vista-listado-mobile');
    const elMapaM    = document.getElementById('vista-mapa-mobile');
    const btnL       = document.getElementById('btn-listado');
    const btnM       = document.getElementById('btn-mapa');
    const btnLM      = document.getElementById('btn-listado-m');
    const btnMM      = document.getElementById('btn-mapa-m');

    if (vista === 'mapa') {
        if (mobile) {
            elListadoM?.classList.add('hidden');
            elMapaM?.classList.remove('hidden');
            btnMM?.classList.add('bg-white','shadow','text-[#fc5648]');
            btnMM?.classList.remove('text-gray-500','hover:text-gray-700');
            btnLM?.classList.remove('bg-white','shadow','text-[#fc5648]');
            btnLM?.classList.add('text-gray-500','hover:text-gray-700');
        } else {
            elListado?.classList.add('hidden');
            elMapa?.classList.remove('hidden');
            btnM?.classList.add('bg-white','shadow','text-[#fc5648]');
            btnM?.classList.remove('text-gray-500','hover:text-gray-700');
            btnL?.classList.remove('bg-white','shadow','text-[#fc5648]');
            btnL?.classList.add('text-gray-500','hover:text-gray-700');
        }
        const containerId = mobile ? 'mapa-mobile' : 'mapa-principal';
        if (!mapaIniciado) {
            mapaIniciado = true;
            void document.getElementById(containerId)?.offsetHeight;
            iniciarMapa(containerId);
        }
    } else {
        if (mobile) {
            elMapaM?.classList.add('hidden');
            elListadoM?.classList.remove('hidden');
            btnLM?.classList.add('bg-white','shadow','text-[#fc5648]');
            btnLM?.classList.remove('text-gray-500','hover:text-gray-700');
            btnMM?.classList.remove('bg-white','shadow','text-[#fc5648]');
            btnMM?.classList.add('text-gray-500','hover:text-gray-700');
        } else {
            elMapa?.classList.add('hidden');
            elListado?.classList.remove('hidden');
            btnL?.classList.add('bg-white','shadow','text-[#fc5648]');
            btnL?.classList.remove('text-gray-500','hover:text-gray-700');
            btnM?.classList.remove('bg-white','shadow','text-[#fc5648]');
            btnM?.classList.add('text-gray-500','hover:text-gray-700');
        }
    }
}

// ── Leaflet ─────────────────────────────────────────────────────────────
function iniciarMapa(containerId) {
    delete L.Icon.Default.prototype._getIconUrl;

    mapaLeaflet = L.map(containerId, {
        center: [-33.039156, -71.621014],
        zoom: 14,
        minZoom: 5,
        maxZoom: 19,
        zoomControl: false,
        attributionControl: false,
    });
        mapaLeaflet.invalidateSize();
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd',
    }).addTo(mapaLeaflet);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd',
        pane: 'shadowPane',
    }).addTo(mapaLeaflet);

const EMOJI_CAT = {
    // 1: Miradores (Telescopio / Binoculares)
    1: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M10 19H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-6"/>
        <path d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
        <path d="M12 19v3M9 22h6"/>
    </svg>`, 

    // 2: Cafetería (Taza de café humeante)
    2: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#854d0e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M17 8h1a4 4 0 1 1 0 8h-1"/>
        <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/>
        <path d="M6 2v2M10 2v2M14 2v2"/>
    </svg>`, 

    // 3: Street Art (Tarro de spray / Graffiti)
    3: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d946ef" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M12 2v3m-3-3h6M9 9h6v11a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V9Z"/>
        <path d="M9 13h6M17 5a2 2 0 0 1 2 2M18 10a1 1 0 0 1 1 1"/>
    </svg>`, 

    // 4: Monumentos (Arco de triunfo / Hito monumental)
    4: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bolt-icon lucide-bolt"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><circle cx="12" cy="12" r="4"/></svg>`, 

    // 5: Centro Cultural (Máscaras de teatro / Artes)
    5: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"/>
        <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
        <path d="M9 9h.01M15 9h.01"/>
    </svg>`,

    // 6: Naturaleza (Árbol / Parque)
    6: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M12 22V12m0 0a5 5 0 1 0-5-5M12 12a5 5 0 1 1 5-5"/>
        <path d="M12 12a5 5 0 0 1-5 5M12 12a5 5 0 0 0 5 5"/>
    </svg>`, 

    // 7: Museo (Edificio clásico con columnas)
    7: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="m3 9 9-7 9 7"/>
        <path d="M5 22V11M9 22V11M15 22V11M19 22V11"/>
        <path d="M2 22h20"/>
    </svg>`, 

    // 8: Restaurante (Plato, tenedor y cuchillo)
    8: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bottle-wine-icon lucide-bottle-wine"><path d="M10 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a6 6 0 0 0 1.2 3.6l.6.8A6 6 0 0 1 17 13v8a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-8a6 6 0 0 1 1.2-3.6l.6-.8A6 6 0 0 0 10 5z"/><path d="M17 13h-4a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h4"/></svg>`, 

    // 9: Arquitectura (Compás de precisión / Regla)
    9: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M2 22h20"/>
        <path d="M12 2v2"/>
        <path d="m12 4-4 14"/>
        <path d="m12 4 4 14"/>
        <path d="M9 14h6"/>
    </svg>`, 

    // 10: Bar / Coctelería / Vinos (Copa de vino)
    10: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#be123c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M8 22h8M12 15v7M12 15a5 5 0 0 0 5-5V2H7v8a5 5 0 0 0 5 5Z"/>
        <path d="M7 8h10"/>
    </svg>`,

    // 11: Alojamiento (Cama de hotel)
    11: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v3a2 2 0 0 0 2 2h3"/>
    </svg>`, 

    // 12: Estatua (Estatua de la Libertad / Silueta de monumento)
    12: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M7 21a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2"/>
        <path d="M10 17v4h4v-4"/>
        <path d="M8 8.5V11a4 4 0 0 0 4 4 4 4 0 0 0 4-4V8.5Z"/>
        <path d="M18 10V5a2 2 0 0 0-2-2h-2"/>
        <path d="M12 2l-1 2h2z" fill="currentColor"/>
        <path d="M10 7a2 2 0 1 1 4 0"/>
        <path d="M8.5 5.5L10 7M15.5 5.5L14 7M12 4.5V7M8.1 7L10 7M15.9 7L14 7" stroke-width="1"/>
    </svg>`, 

    // 13: Patrimonio Inmaterial (Manos protegiendo una estrella / Cultura viva)
    13: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px; display: block;">
        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"/>
        <path d="m12 14-3 3.5 1-4.5-4-1.5 4.5-.5L12 7l1.5 4 4.5.5-4 1.5 1 4.5Z"/>
    </svg>`, 
};

    function crearIcono(p) {
        const emoji  = EMOJI_CAT[p.categoria_id] || (p.es_cliente ? '🏪' : '📍');
        const border = p.es_cliente ? '#fc5648' : '#9ca3af';
        return L.divIcon({
            className: '',
            html: `
                <div style="display:flex;flex-direction:column;align-items:center;">
                    <div style="
                        background:white;
                        border:2.5px solid ${border};
                        border-radius:50%;
                        width:34px;height:34px;
                        display:flex;align-items:center;justify-content:center;
                        font-size:17px;
                        box-shadow:0 2px 10px rgba(0,0,0,.22);
                        line-height:1;">
                        ${emoji}
                    </div>
                    <div style="
                        width:0;height:0;
                        border-left:5px solid transparent;
                        border-right:5px solid transparent;
                        border-top:7px solid ${border};
                        margin-top:-1px;">
                    </div>
                </div>`,
            iconSize:   [34, 41],
            iconAnchor: [17, 41],
            popupAnchor:[0, -44],
        });
    }

    const GPS_LAT = {{ request('lat') ? (float) request('lat') : 'null' }};
    const GPS_LNG = {{ request('lng') ? (float) request('lng') : 'null' }};

    // Si hay GPS, centrar en el usuario con zoom de barrio
    if (GPS_LAT && GPS_LNG) {
        mapaLeaflet.setView([GPS_LAT, GPS_LNG], 15);
        L.circleMarker([GPS_LAT, GPS_LNG], {
            radius: 8, color: '#fc5648', fillColor: '#fc5648',
            fillOpacity: 1, weight: 3,
        }).addTo(mapaLeaflet).bindPopup('Estás aquí');
    }

    const bounds = [];
    PUNTOS_DATA.forEach(p => {
        if (!p.lat || !p.lng) return;
        bounds.push([p.lat, p.lng]);
        const catBadge    = p.categoria ? `<span style="background:#fc5648;color:white;font-size:9px;font-weight:700;text-transform:uppercase;padding:2px 7px;border-radius:999px;">${p.categoria}</span>` : '';
        const clienteBadge= p.es_cliente ? `<span style="background:#fef3c7;color:#92400e;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin-left:4px;">Negocio</span>` : '';
        const catEmoji    = EMOJI_CAT[p.categoria_id] || (p.es_cliente ? '🏪' : '📍');
        const imgHtml     = p.imagen ? `<img src="${p.imagen}" style="width:100%;height:110px;object-fit:cover;border-radius:.5rem .5rem 0 0;">` : `<div style="width:100%;height:60px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border-radius:.5rem .5rem 0 0;">${catEmoji}</div>`;
        L.marker([p.lat, p.lng], { icon: crearIcono(p) })
            .bindPopup(`<div style="font-family:sans-serif;">${imgHtml}<div style="padding:10px 12px 12px;"><div style="margin-bottom:5px;">${catBadge}${clienteBadge}</div><div style="font-weight:700;font-size:13px;line-height:1.3;margin-bottom:8px;">${p.title}</div><a href="/lugar/${p.slug}" style="background:#fc5648;color:white;font-size:11px;font-weight:700;padding:5px 12px;border-radius:8px;text-decoration:none;display:inline-block;">Ver detalle →</a></div></div>`, { maxWidth: 230 })
            .addTo(mapaLeaflet);
    });

    // Sin GPS: ajustar para ver todos los puntos
    // if (!GPS_LAT && bounds.length > 1) {
    //     mapaLeaflet.fitBounds(bounds, { padding: [40, 40] });
    // }
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
@endsection
