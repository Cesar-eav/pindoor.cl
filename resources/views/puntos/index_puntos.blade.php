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
                ¿Qué quieres <span class="text-red-400">hacer</span> en <span class="text-red-400">Valparaíso</span>?
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

        {{-- Vista Listado — Livewire --}}
        <div id="vista-listado">
            @livewire('atractivos-grid')
        </div>{{-- /vista-listado --}}

    </div>
</div>{{-- /desktop --}}

@endsection

@section('scripts')
    @include('puntos.partials._scripts')
@endsection
