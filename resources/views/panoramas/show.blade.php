@extends('layouts.pindoor')

@section('title', $panorama->titulo . ' — Pindoor')
@section('description', $panorama->ubicacion ? $panorama->titulo . ' en ' . $panorama->ubicacion . '. ' . \Carbon\Carbon::parse($panorama->fecha)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : $panorama->titulo)
@section('canonical', route('panoramas.show', $panorama))
@section('bodyClass', 'bg-gray-100 text-gray-900')

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
    $waUrl = 'https://wa.me/?text=' . urlencode($panorama->titulo . ' — ' . route('panoramas.show', $panorama));
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
                @if(isset(\App\Models\Panorama::CATEGORIAS[$panorama->categoria]))
                    @php $cat = \App\Models\Panorama::CATEGORIAS[$panorama->categoria]; @endphp
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
                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 hover:bg-blue-100 transition text-lg leading-none">
                        📅 
                        
                    </a>

                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                       title="Compartir por WhatsApp"
                       class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-50 hover:bg-green-100 transition">
                        <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Título --}}
            <h1 class="text-2xl font-bold text-gray-900 leading-tight mb-4">{{ $panorama->titulo }}</h1>

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


        </div>
    </div>

</div>
@endsection
