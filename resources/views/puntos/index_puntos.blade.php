@php
    use Illuminate\Support\Str;
    $hayFiltros = request()->anyFilled(['category', 'search', 'lat']);
    $categoriaActiva = request()->filled('category')
        ? $categorias->firstWhere('slug', request('category'))
        : null;
@endphp

@extends('layouts.pindoor')

@if($categoriaActiva)
@section('title', $categoriaActiva->nombre . ' en Valparaíso · Pindoor')
@section('description', 'Descubre ' . $categoriaActiva->nombre . ' en Valparaíso: dirección, horarios, fotos y ubicación en el mapa. Guía actualizada de Pindoor.')
@section('canonical', route('puntos.index', ['category' => $categoriaActiva->slug]))
@else
@section('title', 'Pindoor · Guía de lugares en Valparaíso')
@section('description', 'Explora restaurantes, cafeterías, hoteles, museos, bares y atracciones turísticas en Valparaíso. Filtra por categoría, busca por nombre o activa el GPS para ver qué tienes cerca.')
@section('canonical', route('puntos.index'))
@endif
@if(request()->anyFilled(['search', 'lat', 'vista', 'page']))
@section('robots', 'noindex, follow')
@endif
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('head')
    @php
        $ogTitle = $categoriaActiva ? $categoriaActiva->nombre . ' en Valparaíso · Pindoor' : 'Pindoor · Guía de lugares en Valparaíso';
        $ogDesc  = $categoriaActiva
            ? 'Descubre ' . $categoriaActiva->nombre . ' en Valparaíso: dirección, horarios, fotos y ubicación en el mapa.'
            : 'Explora restaurantes, hoteles, museos y atracciones turísticas en Valparaíso. La guía local más completa.';
    @endphp
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $categoriaActiva ? route('puntos.index', ['category' => $categoriaActiva->slug]) : route('puntos.index') }}" />
    <meta property="og:title" content="{{ $ogTitle }}" />
    <meta property="og:description" content="{{ $ogDesc }}" />
    <meta property="og:image" content="{{ asset('img/pindoor-og.jpg') }}" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $ogTitle }}" />
    <meta name="twitter:description" content="{{ $ogDesc }}" />
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
        /* GPS marker compuesto */
        .gps-marker { position: relative; width: 40px; height: 40px; }
        .gps-pulse-ring { position: absolute; inset: 0; border-radius: 50%; background: rgba(252,86,72,.2); animation: gps-pulse 2s ease-out infinite; }
        .gps-core { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 14px; height: 14px; background: #fc5648; border: 3px solid white; border-radius: 50%; box-shadow: 0 2px 8px rgba(252,86,72,.6); z-index: 2; }
        .gps-cone-wrap { position: absolute; inset: 0; display: flex; align-items: flex-start; justify-content: center; padding-top: 2px; opacity: 0; transform-origin: center; transition: transform 0.4s ease-out, opacity 0.3s; z-index: 1; }
        @keyframes gps-pulse { 0% { transform: scale(1); opacity: .8; } 100% { transform: scale(2.8); opacity: 0; } }
        @keyframes gps-spin { to { transform: rotate(360deg); } }
        .gps-buscando { animation: gps-spin 1s linear infinite; transform-origin: center; }
        /* Botón GPS control */
        .leaflet-gps-btn { background: white; border: 2px solid rgba(0,0,0,.08); border-radius: 10px; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 3px 12px rgba(0,0,0,.2); transition: all .2s; color: #6b7280; }
        .leaflet-gps-btn:hover { background: #fff0ef; color: #fc5648; border-color: #fca49f; }
        .leaflet-gps-btn.activo { background: #fff0ef; color: #fc5648; border-color: #fc5648; }
        /* Botón recentrar (aparece cuando usuario arrastra) */
        .leaflet-recenter-btn { background: white; border-radius: 50%; width: 42px; height: 42px; box-shadow: 0 3px 12px rgba(0,0,0,.25); display: flex; align-items: center; justify-content: center; color: #fc5648; cursor: pointer; border: 2px solid rgba(0,0,0,.08); margin-bottom: 6px; transition: background .2s; }
        .leaflet-recenter-btn:hover { background: #fff0ef; }
    </style>
@endsection


@section('content')

{{-- ══ MOBILE (< md) ══════════════════════════════════════════════════════ --}}
<div class="md:hidden flex flex-col min-h-screen">

    <div id="vista-listado-mobile" class="{{ request('vista') === 'mapa' ? 'hidden' : '' }}">
        @include('puntos.partials._listado_mobile')
    </div>

    {{-- Mapa mobile --}}
    <div id="vista-mapa-mobile" class="{{ request('vista') === 'mapa' ? 'flex' : 'hidden' }} flex-1 flex-col">

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

        <div class="relative flex-1" style="min-height:0;">
            <div id="mapa-mobile" class="absolute inset-0"></div>
            <button id="gps-btn-mobile" onclick="toggleUbicacion(this)"
                    class="leaflet-gps-btn absolute z-999"
                    style="top:12px;right:12px;"
                    title="{{ __('ui.mapa.estas_aqui') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <circle cx="12" cy="12" r="5"/>
                    <circle cx="12" cy="12" r="1.5" fill="currentColor"/>
                    <line x1="12" y1="2" x2="12" y2="3"/>
                    <line x1="12" y1="21" x2="12" y2="22"/>
                    <line x1="2" y1="12" x2="3" y2="12"/>
                    <line x1="21" y1="12" x2="22" y2="12"/>
                </svg>
            </button>
        </div>
    </div>

</div>{{-- /mobile --}}


{{-- ══ DESKTOP (md+) ══════════════════════════════════════════════════════ --}}
<div class="hidden md:block w-full md:p-4">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex items-center justify-between my-6">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ $categoriaActiva ? $categoriaActiva->nombre . ' en Valparaíso' : __('ui.home.titulo') }}
            </h1>
            <div class="inline-flex bg-gray-200 p-1 rounded-xl gap-1">
                <button id="btn-listado" onclick="setView('listado')"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all bg-white shadow text-[#fc5648]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    {{ __('ui.nav.listado') }}
                </button>
                <button id="btn-mapa" onclick="setView('mapa')"
                        class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    {{ __('ui.nav.mapa') }}
                </button>
            </div>
        </div>

        {{-- Vista Mapa --}}
        <div id="vista-mapa" class="{{ request('vista') === 'mapa' ? '' : 'hidden' }} mb-8">
            {{-- Pills de categoría --}}
            <div id="pills-mapa-desktop" class="flex flex-wrap gap-2 mb-4">
                <button data-slug=""
                        class="pill-mapa px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors
                               {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                    {{ __('ui.home.todos_label') }}
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
                <span id="mapa-contador">{{ count($puntosMapData) }}</span> {{ __('ui.mapa.puntos') }} · {{ __('ui.mapa.clic_marcador') }}
            </p>
        </div>

        {{-- Vista Listado — Livewire --}}
        <div id="vista-listado" class="{{ request('vista') === 'mapa' ? 'hidden' : '' }}">
            @livewire('atractivos-grid')
        </div>{{-- /vista-listado --}}

    </div>
</div>{{-- /desktop --}}

@endsection

@section('scripts')
    @include('puntos.partials._mapa')
@endsection
