@php
    use Illuminate\Support\Str;
    $hayFiltros = request()->anyFilled(['category', 'search', 'lat']);
@endphp

@extends('layouts.pindoor')

@section('title', 'Pindoor · Guía de lugares en Valparaíso')
@section('description', 'Explora restaurantes, cafeterías, hoteles, museos, bares y atracciones turísticas en Valparaíso. Filtra por categoría, busca por nombre o activa el GPS para ver qué tienes cerca.')
@section('canonical', route('puntos.index'))
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('head')
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('puntos.index') }}" />
    <meta property="og:title" content="Pindoor · Guía de lugares en Valparaíso" />
    <meta property="og:description" content="Explora restaurantes, hoteles, museos y atracciones turísticas en Valparaíso. La guía local más completa." />
    <meta property="og:image" content="{{ asset('img/pindoor-og.jpg') }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Pindoor · Guía de lugares en Valparaíso" />
    <meta name="twitter:description" content="Explora restaurantes, hoteles, museos y atracciones turísticas en Valparaíso. La guía local más completa." />
    <meta name="twitter:image" content="{{ asset('img/pindoor-og.jpg') }}" />
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
          "target": { "@type": "EntryPoint", "urlTemplate": "{{ route('puntos.buscar') }}?search={search_term_string}" },
          "query-input": "required name=search_term_string"
        }
      },
      {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Pindoor",
        "url": "{{ route('puntos.index') }}",
        "logo": "{{ asset('img/pindoor-logo.png') }}",
        "contactPoint": { "@type": "ContactPoint", "contactType": "customer support", "availableLanguage": "Spanish" }
      }
    ]
    </script>
    @vite('resources/js/leaflet.js')
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

{{-- ══ MOBILE (< md) ══════════════════════════════════════════════════════ --}}
<div class="md:hidden flex flex-col min-h-screen">

    @include('puntos.partials._listado_mobile')

    {{-- Mapa mobile --}}
    <div id="vista-mapa-mobile" class="hidden flex-1 flex-col">

        {{-- Pills de categoría sobre el mapa --}}
        <div class="overflow-x-auto no-scrollbar bg-white border-b border-gray-100 px-3 py-2 shrink-0"
             style="-ms-overflow-style:none;scrollbar-width:none;">
            <div class="flex gap-2 w-max">
                <button data-slug="" onclick="filtrarMapa('')"
                        class="pill-mapa px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                               bg-gray-900 text-white border-gray-900 whitespace-nowrap">
                    Todos
                </button>
                @foreach($categorias as $cat)
                <button data-slug="{{ $cat->slug }}" onclick="filtrarMapa('{{ $cat->slug }}')"
                        class="pill-mapa px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                               bg-white text-gray-500 border-gray-300 whitespace-nowrap">
                    {{ $cat->nombre }}
                </button>
                @endforeach
            </div>
        </div>

        <div id="mapa-mobile" class="flex-1" style="min-height:0;"></div>
    </div>

</div>{{-- /mobile --}}


{{-- ══ DESKTOP (md+) ══════════════════════════════════════════════════════ --}}
<div class="hidden md:block w-full md:p-4">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center justify-between my-6">
            <h1 class="text-3xl font-bold text-gray-900">
                ¿Qué quieres <span class="text-red-400">hacer</span> hoy?
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
            {{-- Pills de categoría --}}
            <div id="pills-mapa-desktop" class="flex flex-wrap gap-2 mb-4">
                <button data-slug=""
                        class="pill-mapa px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors
                               {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                    Todos
                </button>
                @foreach($categorias as $cat)
                <button data-slug="{{ $cat->slug }}"
                        class="pill-mapa px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors
                               {{ request('category') == $cat->slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                    {{ $cat->nombre }}
                </button>
                @endforeach
            </div>

            <div id="mapa-principal"></div>
            <p class="text-xs text-gray-400 text-center mt-2">
                <span id="mapa-contador">{{ count($puntosMapData) }}</span> puntos · Clic en un marcador para ver el detalle
            </p>
        </div>

        {{-- Vista Listado --}}
        <div id="vista-listado">
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

            {{-- Panoramas y Blog: solo sin filtros activos --}}
            @if(!$hayFiltros)

                {{-- Próximos panoramas --}}
                @if(isset($proximosPanoramas) && $proximosPanoramas->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-1 h-5 rounded-full bg-[#fc5648] shrink-0"></span>
                        <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">Panoramas</h2>
                        <span class="flex-1 h-px bg-gray-200"></span>
                        <a href="{{ route('atractivos.panoramas') }}" class="text-sm font-semibold text-[#fc5648] hover:underline shrink-0">Ver todos →</a>
                    </div>
                    <div class="flex gap-4 overflow-x-auto pb-2 snap-x snap-mandatory scrollbar-hide">
                        @foreach($proximosPanoramas->take(15) as $p)
                        <a href="{{ route('panoramas.show', $p) }}"
                           class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group shrink-0 w-44 snap-start">
                            @if($p->imagen)
                                <img src="{{ asset('storage/' . $p->imagen) }}"
                                     alt="{{ $p->titulo }}"
                                     class="w-full h-32 object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-32 bg-[#fff0ef] flex items-center justify-center text-3xl">🗓</div>
                            @endif
                            <div class="p-3">
                                <p class="text-xs font-bold text-[#fc5648]">
                                    {{ \Carbon\Carbon::parse($p->fecha)->locale('es')->isoFormat('D MMM') }}
                                </p>
                                <p class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mt-0.5">{{ $p->titulo }}</p>
                                @if($p->ubicacion)
                                    <p class="text-xs text-gray-400 truncate mt-1">📍 {{ $p->ubicacion }}</p>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Últimas entradas del blog --}}
                @if(isset($ultimosPosts) && $ultimosPosts->isNotEmpty())
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-1 h-5 rounded-full bg-[#fc5648] shrink-0"></span>
                        <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">Blog</h2>
                        <span class="flex-1 h-px bg-gray-200"></span>
                        <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-[#fc5648] hover:underline shrink-0">Ver todos →</a>
                    </div>

                    <div class="flex gap-3 overflow-x-auto snap-x snap-mandatory pb-2"
                         style="scrollbar-width:none">
                        @foreach($ultimosPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}"
                           class="group relative shrink-0 w-72 rounded-2xl overflow-hidden shadow-sm h-52 snap-start">
                            @if($post->imagen_portada)
                                <img src="{{ asset('storage/' . $post->imagen_portada) }}"
                                     alt="{{ $post->titulo }}"
                                     class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="absolute inset-0 bg-gray-800"></div>
                            @endif
                            <div class="absolute inset-0 bg-linear-to-t from-black/85 via-black/30 to-transparent"></div>
                            <div class="relative z-10 h-full flex flex-col justify-end p-4">
                                <h3 class="text-sm font-extrabold text-white leading-snug line-clamp-3">{{ $post->titulo }}</h3>
                                @if($post->resumen)
                                    <p class="text-xs text-white/70 mt-1 line-clamp-2">{{ $post->resumen }}</p>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Cabecera sección atractivos --}}
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-1 h-5 rounded-full bg-gray-800 shrink-0"></span>
                    <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">Atractivos</h2>
                    <span class="flex-1 h-px bg-gray-200"></span>
                </div>

            @endif

            <div class="pb-12">
                @if($atractivos->count())
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($atractivos as $atractivo)
                            @include('puntos.partials._card_desktop')
                        @endforeach
                    </div>
                    <div class="mt-12 mb-4 flex justify-center">
                        {{ $atractivos->links() }}
                    </div>
                @elseif(empty($panoramas) || $panoramas->isEmpty())
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

                {{-- Panoramas en búsqueda --}}
                @if(isset($panoramas) && $panoramas->isNotEmpty())
                <div class="mt-10">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-gray-700">
                            🗓 Panoramas con "{{ request('search') }}"
                        </h2>
                        <a href="{{ route('atractivos.panoramas') }}" class="text-xs text-[#fc5648] font-semibold hover:underline">
                            Ver todos →
                        </a>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($panoramas as $panorama)
                        <a href="{{ route('panoramas.show', $panorama) }}"
                           class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group">
                            @if($panorama->imagen)
                                <img src="{{ asset('storage/' . $panorama->imagen) }}"
                                     alt="{{ $panorama->titulo }}"
                                     class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-36 bg-linear-to-br from-[#fff0ef] to-[#ffe4e1] flex items-center justify-center text-3xl">🗓</div>
                            @endif
                            <div class="p-3">
                                <p class="font-bold text-gray-900 text-sm leading-snug line-clamp-2">{{ $panorama->titulo }}</p>
                                <p class="text-xs text-[#fc5648] font-semibold mt-1">
                                    {{ \Carbon\Carbon::parse($panorama->fecha)->locale('es')->isoFormat('D MMM') }}
                                    @if($panorama->fecha_fin && $panorama->fecha_fin !== $panorama->fecha)
                                        — {{ \Carbon\Carbon::parse($panorama->fecha_fin)->locale('es')->isoFormat('D MMM') }}
                                    @endif
                                </p>
                                @if($panorama->ubicacion)
                                    <p class="text-xs text-gray-400 mt-0.5 truncate">📍 {{ $panorama->ubicacion }}</p>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>{{-- /vista-listado --}}

    </div>
</div>{{-- /desktop --}}

@endsection

@section('scripts')
    @include('puntos.partials._scripts')
@endsection
