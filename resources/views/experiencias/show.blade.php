@extends('layouts.pindoor')

@section('title', $experiencia->titulo . ' — Experiencias Pindoor')
@section('canonical', route('experiencias.show', $experiencia))
@section('description', $experiencia->descripcion
    ? \Illuminate\Support\Str::limit($experiencia->descripcion, 160)
    : $experiencia->titulo . ($experiencia->proveedor ? ' por ' . $experiencia->proveedor : '') . ' en Valparaíso.')
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('og_type', 'website')
@section('og_url', route('experiencias.show', $experiencia))
@section('og_title', $experiencia->titulo . ' — Experiencias Pindoor')
@section('og_description', $experiencia->descripcion
    ? \Illuminate\Support\Str::limit($experiencia->descripcion, 160)
    : $experiencia->titulo . ($experiencia->proveedor ? ' por ' . $experiencia->proveedor : '') . ' en Valparaíso.')
@if($experiencia->imagen)
    @section('og_image', asset('storage/' . $experiencia->imagen))
@endif

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

    {{-- Volver --}}
    <a href="{{ route('experiencias.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 mb-5 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver a Experiencias
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Carrusel --}}
        @php
            $todasImagenes = collect();
            if ($experiencia->imagen) $todasImagenes->push(asset('storage/' . $experiencia->imagen));
            foreach ($experiencia->imagenes as $img) $todasImagenes->push(asset('storage/' . $img->ruta));
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
                <img :src="src" x-show="current === i"
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

        <div class="p-6 space-y-4">

            {{-- Badges --}}
            <div class="flex flex-wrap gap-2">
                @if(isset(\App\Models\Experiencia::CATEGORIAS[$experiencia->categoria]))
                @php $cat = \App\Models\Experiencia::CATEGORIAS[$experiencia->categoria]; @endphp
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#fff0ef] text-[#fc5648] text-xs font-bold">
                    {{ $cat['emoji'] }} {{ $cat['label'] }}
                </span>
                @endif
                @if($experiencia->es_gratuito)
                <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">🎟️ Gratis</span>
                @elseif($experiencia->precio)
                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold">
                    ${{ number_format($experiencia->precio, 0, ',', '.') }} CLP
                </span>
                @endif
                @if($experiencia->nivel && $experiencia->nivel !== 'todos')
                <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">
                    {{ \App\Models\Experiencia::NIVELES[$experiencia->nivel] ?? $experiencia->nivel }}
                </span>
                @endif
            </div>

            {{-- Título --}}
            <h1 class="text-2xl font-bold text-gray-900 leading-tight">{{ $experiencia->titulo }}</h1>

            {{-- Proveedor --}}
            @if($experiencia->proveedor)
            <p class="text-sm text-gray-600 font-semibold">👤 {{ $experiencia->proveedor }}</p>
            @endif

            {{-- Descripción --}}
            @if($experiencia->descripcion)
            <p class="text-sm text-gray-600 leading-relaxed">{{ $experiencia->descripcion }}</p>
            @endif

            {{-- Horario --}}
            @if(!empty($experiencia->dias_semana) || $experiencia->hora || $experiencia->duracion || $experiencia->periodo_label)
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#fff0ef] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-[#fc5648]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="space-y-0.5">
                    @if($experiencia->periodo_label)
                    <p class="text-sm font-semibold text-[#fc5648]">📅 {{ $experiencia->periodo_label }}</p>
                    @endif
                    @if(!empty($experiencia->dias_semana))
                    <p class="text-sm font-semibold text-gray-800">🔁 {{ $experiencia->dias_semana_label }}</p>
                    @endif
                    @if($experiencia->hora)
                    <p class="text-sm text-gray-500">🕐 {{ $experiencia->hora }}</p>
                    @endif
                    @if($experiencia->duracion)
                    <p class="text-sm text-gray-500">⏱ {{ $experiencia->duracion }}</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Ubicación --}}
            @if($experiencia->ubicacion)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#fff0ef] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-[#fc5648]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800">{{ $experiencia->ubicacion }}</p>
            </div>
            @endif

            {{-- Capacidad --}}
            @if($experiencia->capacidad)
            <p class="text-sm text-gray-500">👥 Máximo {{ $experiencia->capacidad }} participantes</p>
            @endif

            {{-- CTAs --}}
            @if($experiencia->whatsapp_url || $experiencia->enlace)
            <div class="flex flex-col gap-2 mt-2">
                @if($experiencia->whatsapp_url)
                <a href="{{ $experiencia->whatsapp_url }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 w-full justify-center bg-green-500 hover:bg-green-600 text-white font-bold text-sm px-5 py-3 rounded-xl transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Contactar por WhatsApp
                </a>
                @endif
                @if($experiencia->enlace)
                <a href="{{ $experiencia->enlace }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 w-full justify-center bg-[#fc5648] hover:bg-[#e04035] text-white font-bold text-sm px-5 py-3 rounded-xl transition">
                    Más información
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                @endif
            </div>
            @endif

        </div>
    </div>

</div>
@endsection
