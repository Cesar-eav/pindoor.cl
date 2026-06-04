@extends('layouts.pindoor')

@section('title', ($item->datos['titulo'] ?? 'Exposición') . ' · ' . $punto->title . ' · Pindoor')
@section('description', Str::limit(strip_tags($item->datos['descripcion'] ?? ''), 160))
@section('bodyClass', 'bg-gray-50 text-gray-900')

@php
    use Illuminate\Support\Str;
    $tipo       = $item->datos['tipo'] ?? 'permanente';
    $esTemporal = $tipo === 'temporal';
    $fechaInicio = $item->datos['fecha_inicio'] ?? null;
    $fechaFin    = $item->datos['fecha_fin'] ?? null;
@endphp

@section('content')
<div class="max-w-2xl mx-auto px-4 pt-5 pb-16">

    {{-- Volver --}}
    <a href="{{ route('puntos.show', $punto->slug) }}#exposiciones"
       class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-700 mb-5 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ $punto->title }}
    </a>

    {{-- Afiche / imagen principal --}}
    @if($item->imagen)
    @php $imgUrl = asset('storage/' . $item->imagen); @endphp
    <div x-data="{ zoom: false }">

        {{-- Contenedor adaptativo --}}
        <button type="button" @click="zoom = true"
                class="w-full rounded-3xl overflow-hidden shadow-lg mb-6 bg-gray-100 flex items-center justify-center cursor-zoom-in relative group focus:outline-none">
            <img src="{{ $imgUrl }}"
                 alt="{{ $item->datos['titulo'] ?? 'Exposición' }}"
                 class="w-full max-h-[80vh] object-contain">
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-200 rounded-3xl flex items-center justify-center">
                <span class="opacity-0 group-hover:opacity-100 transition-opacity duration-200
                             bg-black/50 text-white rounded-full p-2.5 backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </span>
            </div>
        </button>

        {{-- Lightbox --}}
        <div x-show="zoom" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click.self="zoom = false"
             @keydown.escape.window="zoom = false"
             class="fixed inset-0 z-999 bg-black/95 flex items-center justify-center p-4">
            <button @click="zoom = false"
                    class="absolute top-4 right-4 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <img src="{{ $imgUrl }}"
                 alt="{{ $item->datos['titulo'] ?? 'Exposición' }}"
                 class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl select-none">
        </div>

    </div>
    @endif

    {{-- Badge tipo --}}
    <div class="flex items-center gap-2 mb-3">
        @if($esTemporal)
            <span class="text-[11px] font-black uppercase bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full">Temporal</span>
            @if($fechaInicio || $fechaFin)
            <span class="text-xs text-gray-400">
                📅
                {{ $fechaInicio ? \Carbon\Carbon::parse($fechaInicio)->translatedFormat('d \d\e M Y') : '—' }}
                @if($fechaFin)
                    → {{ \Carbon\Carbon::parse($fechaFin)->translatedFormat('d \d\e M Y') }}
                @endif
            </span>
            @endif
        @else
            <span class="text-[11px] font-black uppercase bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full">Permanente</span>
        @endif
    </div>

    {{-- Título --}}
    <h1 class="text-2xl font-extrabold text-gray-900 leading-tight mb-4">
        {{ $item->datos['titulo'] ?? '' }}
    </h1>

    {{-- Descripción --}}
    @if($item->datos['descripcion'] ?? null)
    <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line mb-8">
        {{ $item->datos['descripcion'] }}
    </div>
    @endif

    {{-- Museo --}}
    <a href="{{ route('puntos.show', $punto->slug) }}"
       class="flex items-center gap-3 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm hover:shadow transition mb-8">
        @if($punto->imagenPrincipal)
            <img src="{{ asset('storage/' . $punto->imagenPrincipal->ruta) }}"
                 alt="{{ $punto->title }}"
                 class="w-10 h-10 rounded-xl object-cover shrink-0">
        @else
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-lg shrink-0">🏛️</div>
        @endif
        <div class="min-w-0">
            <p class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider">Museo</p>
            <p class="text-sm font-bold text-gray-800 truncate">{{ $punto->title }}</p>
        </div>
        <svg class="w-4 h-4 text-gray-300 ml-auto shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

    {{-- Otras exposiciones --}}
    @if($otras->count())
    <div>
        <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Otras exposiciones</p>
        <div class="grid grid-cols-2 gap-3">
            @foreach($otras as $otra)
            <a href="{{ route('puntos.exposicion', [$punto->slug, $otra->id]) }}"
               class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow transition flex flex-col">
                @if($otra->imagen)
                <div class="aspect-[3/4] overflow-hidden bg-gray-100">
                    <img src="{{ asset('storage/' . $otra->imagen) }}"
                         alt="{{ $otra->datos['titulo'] ?? '' }}"
                         class="w-full h-full object-cover object-top">
                </div>
                @else
                <div class="aspect-[3/4] bg-gray-50 flex items-center justify-center text-3xl">🖼️</div>
                @endif
                <div class="p-2.5">
                    @php $otraTipo = $otra->datos['tipo'] ?? 'permanente'; @endphp
                    <span class="text-[10px] font-black uppercase px-1.5 py-0.5 rounded-full
                        {{ $otraTipo === 'temporal' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $otraTipo === 'temporal' ? 'Temporal' : 'Permanente' }}
                    </span>
                    <p class="text-xs font-bold text-gray-900 mt-1 line-clamp-2 leading-snug">{{ $otra->datos['titulo'] ?? '' }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
