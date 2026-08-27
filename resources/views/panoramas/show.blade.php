@php
    $categLabel = \App\Models\CategoriaEvento::catalogo()[$panorama->categoria]['label'] ?? null;
    $fechaStr   = \Carbon\Carbon::parse($panorama->fecha)->locale('es')->isoFormat('D [de] MMMM [de] YYYY');
    $seoTitle   = $panorama->titulo
                . ($panorama->ubicacion ? ', ' . $panorama->ubicacion : ', Valparaíso')
                . ' — Pindoor';
    $seoDesc    = ($categLabel ? $categLabel . ' en Valparaíso. ' : '')
                . $panorama->titulo
                . ($panorama->ubicacion ? ' en ' . $panorama->ubicacion : '')
                . ', el ' . $fechaStr
                . ($panorama->es_gratuito ? '. Entrada gratuita.' : '.');
    // Panorama real (admin) tiene slug propio; evento de agenda de cliente o de artista usa su propia ruta.
    $panoramaUrl = $panorama->slug
                 ? route('panoramas.show', $panorama)
                 : (($origenTipo ?? 'punto') === 'artista'
                     ? route('artista.evento', ['slug' => $puntoRelacionado->slug, 'item' => $panorama->modulo_item_id])
                     : route('puntos.evento', ['slug' => $puntoRelacionado->slug, 'item' => $panorama->modulo_item_id]));
    $imgUrl = $panorama->imagen ? asset('storage/' . $panorama->imagen) : null;
@endphp

@extends('layouts.pindoor')

@section('title', $seoTitle)
@section('description', $seoDesc)
@section('canonical', $panoramaUrl)
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('og_type', 'article')
@section('og_url', $panoramaUrl)
@section('og_title', $seoTitle)
@section('og_description', $seoDesc)
@if($imgUrl)
    @section('og_image', $imgUrl)
@endif

@section('content')
@php
    use Carbon\Carbon;
    $gcHora  = $panorama->hora ? str_replace(':', '', substr($panorama->hora, 0, 5)) . '00' : null;
    $gcStart = $gcHora ? $panorama->fecha->format('Ymd') . 'T' . $gcHora : $panorama->fecha->format('Ymd');
    $gcFin   = $panorama->fecha_fin ?? $panorama->fecha;
    $gcEnd   = $gcHora ? $gcFin->format('Ymd') . 'T' . $gcHora : $gcFin->copy()->addDay()->format('Ymd');
    $gcUrl   = 'https://calendar.google.com/calendar/render?' . http_build_query([
        'action'   => 'TEMPLATE',
        'text'     => $panorama->titulo,
        'dates'    => $gcStart . '/' . $gcEnd,
        'location' => $panorama->ubicacion ?? '',
    ]);
    $waHora      = $panorama->hora ? substr($panorama->hora, 0, 5) . ' hrs' : null;
    $waFechaHora = $fechaStr . ($waHora ? ', ' . $waHora : '');
@endphp
<div class="max-w-2xl mx-auto px-4 py-8">

    {{-- Volver --}}
    <a href="{{ route('atractivos.panoramas') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 mb-5 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver a Panoramas
    </a>

    @if(($panorama->fecha_fin ?? $panorama->fecha)->lt(\Carbon\Carbon::today()))
    <div class="mb-4 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl px-4 py-3 text-sm font-semibold">
        <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Este evento ya finalizó. La información se mantiene como referencia.
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Imagen principal / carrusel --}}
        @php
            $todasImagenes = collect();
            if ($panorama->imagen) {
                $todasImagenes->push(asset('storage/' . $panorama->imagen));
            }
            foreach ($panorama->imagenes as $img) {
                $todasImagenes->push(asset('storage/' . $img->ruta));
            }
        @endphp

        @if($todasImagenes->isNotEmpty())
        <div x-data="{
                images: {{ $todasImagenes->values()->toJson() }},
                current: 0,
                zoomed: false,
                prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                next() { this.current = (this.current + 1) % this.images.length; },
             }"
             class="relative bg-gray-900">

            <template x-for="(src, i) in images" :key="i">
                <img :src="src"
                     x-show="current === i"
                     @click="zoomed = true"
                     class="w-full max-h-80 object-cover cursor-zoom-in">
            </template>

            {{-- Lightbox zoom --}}
            <div x-show="zoomed"
                 x-transition.opacity
                 @click="zoomed = false"
                 @keydown.escape.window="zoomed = false"
                 class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center cursor-zoom-out"
                 style="display:none">
                <img :src="images[current]" class="max-h-screen max-w-screen object-contain p-4">

                @if($todasImagenes->count() > 1)
                <button @click.stop="prev()"
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button @click.stop="next()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                <div class="absolute bottom-4 right-4 bg-black/50 text-white text-xs font-semibold px-2 py-0.5 rounded-full"
                     x-text="(current + 1) + ' / ' + images.length"></div>
                @endif
            </div>

            @if($todasImagenes->count() > 1)
            <button @click="prev()"
                    class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="next()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <div class="absolute bottom-2 right-3 bg-black/50 text-white text-xs font-semibold px-2 py-0.5 rounded-full"
                 x-text="(current + 1) + ' / ' + images.length"></div>
            @endif
        </div>
        @endif

        <div class="p-6">

            {{-- Badges + acciones --}}
            <div class="flex flex-wrap items-center gap-2 mb-4">
                @if(isset(\App\Models\CategoriaEvento::catalogo()[$panorama->categoria]))
                    @php $cat = \App\Models\CategoriaEvento::catalogo()[$panorama->categoria]; @endphp
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#fff0ef] text-[#fc5648] text-xs font-bold">
                        {{ $cat['emoji'] }} {{ $cat['label'] }}
                    </span>
                @endif
                @if($panorama->es_gratuito)
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Gratis</span>
                @endif
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ $gcUrl }}" target="_blank" rel="noopener noreferrer"
                       title="Agregar al calendario"
                       onclick="registrarCompartido('{{ $panoramaUrl }}', 'calendario')"
                       class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-blue-50 hover:bg-blue-100 transition text-xs font-bold text-blue-700 whitespace-nowrap">
                        Agregar calendario
                    </a>

                    @include('partials._share_panel', [
                        'shareText' => $panorama->titulo . ' — ' . $waFechaHora . ' — ' . $panoramaUrl,
                        'imageUrl' => $panorama->imagen ? asset('storage/' . $panorama->imagen) : null,
                        'url' => $panoramaUrl,
                    ])
                </div>
            </div>

            {{-- Título --}}
            <h1 class="text-2xl font-bold text-gray-900 leading-tight mb-3">{{ $panorama->titulo }}</h1>

            {{-- Descripción --}}
            @if($panorama->getTranslation('descripcion', app()->getLocale(), false))
            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line mb-4">{{ $panorama->descripcion }}</p>
            @endif

            {{-- Fecha y hora --}}
            <div class="flex items-start gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-[#fff0ef] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-[#fc5648]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ Carbon::parse($panorama->fecha)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                        @if($panorama->fecha_fin && $panorama->fecha_fin->toDateString() !== $panorama->fecha->toDateString())
                            — {{ Carbon::parse($panorama->fecha_fin)->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                        @endif
                    </p>
                    @if($panorama->hora)
                        <p class="text-sm text-gray-500 mt-0.5">🕐 {{ $panorama->hora }}</p>
                    @endif
                </div>
            </div>

            {{-- Ubicación --}}
            @if($panorama->ubicacion)
            <div class="flex items-center gap-3 mb-5">
                <div class="w-9 h-9 rounded-xl bg-[#fff0ef] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-[#fc5648]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800">{{ $panorama->ubicacion }}</p>
            </div>
            @endif

            {{-- CTA enlace externo --}}
            @if($panorama->enlace)
            <a href="{{ $panorama->enlace }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 w-full justify-center bg-[#fc5648] hover:bg-[#e04035] text-white font-bold text-sm px-5 py-3 rounded-xl transition mb-4">
                Más información
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
            @endif

            {{-- Agenda del negocio cliente o del artista --}}
            @if($puntoRelacionado)
            <a href="{{ ($origenTipo ?? 'punto') === 'artista' ? route('artista.show', $puntoRelacionado->slug) : route('puntos.show', $puntoRelacionado->slug) . '#agenda' }}"
               class="inline-flex items-center gap-2 w-full justify-center bg-gray-900 hover:bg-gray-800 text-white font-bold text-sm px-5 py-3 rounded-xl transition">
                Ver agenda de {{ $puntoRelacionado->title ?? $puntoRelacionado->nombre }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endif

        </div>
    </div>

    {{-- Más panoramas de esta categoría --}}
    @if(isset($panoramasRelacionados) && $panoramasRelacionados->isNotEmpty())
    <div class="mt-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
            <h2 class="text-sm font-bold text-gray-900 tracking-tight">
                Más {{ $categLabel ? mb_strtolower($categLabel) : 'panoramas' }} en Valparaíso
            </h2>
        </div>
        <div class="space-y-3">
            @foreach($panoramasRelacionados as $rel)
            <a href="{{ route('panoramas.show', $rel) }}"
               class="flex items-center gap-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition p-3">
                @if($rel->imagen)
                    <img src="{{ asset('storage/' . $rel->imagen) }}" alt="{{ $rel->titulo }}"
                         class="w-16 h-16 rounded-xl object-cover shrink-0">
                @else
                    <div class="w-16 h-16 rounded-xl bg-[#fff0ef] flex items-center justify-center text-2xl shrink-0">🗓</div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 leading-snug line-clamp-2">{{ $rel->titulo }}</p>
                    <p class="text-xs text-[#fc5648] font-semibold mt-1">
                        {{ $rel->fecha_proxima->locale('es')->isoFormat('D MMM') }}
                        @if($rel->hora) · {{ $rel->hora }} @endif
                    </p>
                    @if($rel->ubicacion)
                    <p class="text-xs text-gray-400 truncate mt-0.5">📍 {{ $rel->ubicacion }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        <a href="{{ route('atractivos.panoramas', ['categoria' => $panorama->categoria]) }}"
           class="mt-4 inline-flex items-center gap-1.5 text-sm font-bold text-[#fc5648] hover:underline">
            Ver más eventos relacionados
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    @endif

</div>
@endsection
