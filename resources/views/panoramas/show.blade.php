@extends('layouts.pindoor')

@section('title', $panorama->titulo . ' — Pindoor')
@section('description', $panorama->ubicacion ? $panorama->titulo . ' en ' . $panorama->ubicacion . '. ' . \Carbon\Carbon::parse($panorama->fecha)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') : $panorama->titulo)
@section('canonical', route('panoramas.show', $panorama))
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('content')
@php use Carbon\Carbon; @endphp
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
                prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                next() { this.current = (this.current + 1) % this.images.length; },
             }"
             class="relative bg-gray-900">

            <template x-for="(src, i) in images" :key="i">
                <img :src="src"
                     x-show="current === i"
                     class="w-full max-h-80 object-cover">
            </template>

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

            {{-- Badges --}}
            <div class="flex flex-wrap gap-2 mb-4">
                @if(isset(\App\Models\Panorama::CATEGORIAS[$panorama->categoria]))
                    @php $cat = \App\Models\Panorama::CATEGORIAS[$panorama->categoria]; @endphp
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#fff0ef] text-[#fc5648] text-xs font-bold">
                        {{ $cat['emoji'] }} {{ $cat['label'] }}
                    </span>
                @endif
                @if($panorama->es_gratuito)
                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">Gratis</span>
                @endif
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
               class="inline-flex items-center gap-2 w-full justify-center bg-[#fc5648] hover:bg-[#e04035] text-white font-bold text-sm px-5 py-3 rounded-xl transition">
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
