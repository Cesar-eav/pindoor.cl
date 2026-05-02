@extends('layouts.pindoor')

@section('title', 'Panoramas — La Brújula de Valparaíso')

@section('canonical', route('atractivos.panoramas'))

@section('bodyClass', 'bg-gray-100 text-gray-900 font-serif')

@section('content')
@php
    $items = $panoramas->values();
@endphp

<div
    class="max-w-7xl mx-auto px-4 py-8"
    x-data="{
        open: false,
        current: 0,
        images: {{ $items->map(fn($p) => ['src' => $p->imagen ? asset('storage/'.$p->imagen) : null, 'titulo' => $p->titulo, 'ubicacion' => $p->ubicacion, 'fecha' => $p->fecha?->translatedFormat('d \d\e F \d\e Y'), 'hora' => $p->hora])->toJson() }},
        openAt(index) { this.current = index; this.open = true; },
        prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
        next() { this.current = (this.current + 1) % this.images.length; },
        close() { this.open = false; }
    }"
    @keydown.escape.window="close()"
    @keydown.arrow-left.window="open && prev()"
    @keydown.arrow-right.window="open && next()"
>

    <section class="my-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-3">
            🧭 Panoramas
        </h1>
        <p class="text-xl text-gray-700">
            Descubre los mejores rincones y experiencias de Valparaíso
        </p>
    </section>

    @if($panoramas->isEmpty())
        <div class="text-center py-16">
            <div class="text-5xl mb-3">🖼️</div>
            <p class="font-bold text-gray-700 mb-1">No hay panoramas publicados aún</p>
            <p class="text-sm text-gray-400">Vuelve pronto para ver las novedades.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($items as $i => $panorama)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border-t-4 border-[#fc5648] group cursor-zoom-in"
                 @click="openAt({{ $i }})">

                <div class="aspect-4/5 overflow-hidden">
                    @if($panorama->imagen)
                        <img src="{{ asset('storage/' . $panorama->imagen) }}"
                             alt="{{ $panorama->titulo }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full bg-gray-100 flex items-center justify-center text-5xl text-gray-300">📷</div>
                    @endif
                </div>

                <div class="flex flex-col gap-2 p-6">
                    <span class="text-gray-800 text-lg font-bold leading-tight">{{ $panorama->titulo }}</span>

                    @if($panorama->ubicacion)
                        <span class="text-gray-600 text-sm flex items-center gap-1">
                            📍 {{ $panorama->ubicacion }}
                        </span>
                    @endif

                    @if($panorama->fecha)
                        <span class="text-gray-600 text-sm flex items-center gap-1">
                            📅 {{ $panorama->fecha->translatedFormat('d \d\e F \d\e Y') }}
                        </span>
                    @endif

                    @if($panorama->hora)
                        <span class="text-gray-600 text-sm flex items-center gap-1">
                            🕐 {{ $panorama->hora }}
                        </span>
                    @endif
                </div>

            </div>
            @endforeach
        </div>

        {{-- Lightbox --}}
        <div
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
            @click.self="close()"
            style="display:none;"
        >
            {{-- Cerrar --}}
            <button @click="close()"
                    class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/25 rounded-full p-2.5 transition z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Anterior --}}
            <button @click="prev()"
                    class="absolute left-3 md:left-6 text-white bg-white/10 hover:bg-white/25 rounded-full p-3 transition z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            {{-- Imagen + info --}}
            <div class="flex flex-col items-center gap-4 px-16 max-w-2xl w-full">
                <template x-if="images[current].src">
                    <img :src="images[current].src"
                         :alt="images[current].titulo"
                         class="max-h-[75vh] w-auto object-contain rounded-xl shadow-2xl select-none">
                </template>
                <div class="text-center text-white space-y-1">
                    <p class="font-bold text-lg" x-text="images[current].titulo"></p>
                    <p class="text-sm text-white/70" x-show="images[current].ubicacion" x-text="'📍 ' + images[current].ubicacion"></p>
                    <div class="flex items-center justify-center gap-3 text-sm text-white/70">
                        <span x-show="images[current].fecha" x-text="'📅 ' + images[current].fecha"></span>
                        <span x-show="images[current].hora" x-text="'🕐 ' + images[current].hora"></span>
                    </div>
                </div>
                {{-- Contador --}}
                <p class="text-white/40 text-sm">
                    <span x-text="current + 1"></span> / {{ $items->count() }}
                </p>
            </div>

            {{-- Siguiente --}}
            <button @click="next()"
                    class="absolute right-3 md:right-6 text-white bg-white/10 hover:bg-white/25 rounded-full p-3 transition z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    @endif

</div>
@endsection
