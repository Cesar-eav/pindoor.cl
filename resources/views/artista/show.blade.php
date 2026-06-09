@extends('layouts.pindoor')

@section('title', $artista->nombre . ' — Artista en Pindoor')
@section('canonical', route('artista.show', $artista->slug))
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-10">

    {{-- Cabecera --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">

        {{-- Banner degradado --}}
        <div class="h-24 w-full" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 60%, #4f46e5 100%)"></div>

        <div class="px-6 pb-6">
            {{-- Avatar --}}
            <div class="-mt-12 mb-4">
                @if($artista->imagen_perfil)
                    <img src="{{ asset('storage/' . $artista->imagen_perfil) }}"
                         alt="{{ $artista->nombre }}"
                         class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                @else
                    <div class="w-24 h-24 rounded-full bg-violet-100 border-4 border-white shadow-md flex items-center justify-center text-4xl">🎨</div>
                @endif
            </div>

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $artista->nombre }}</h1>
                    <p class="text-sm font-semibold text-violet-600 mt-0.5">{{ $artista->disciplinaLabel() }}</p>
                    @if($artista->ciudad)
                        <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $artista->ciudad }}
                        </p>
                    @endif
                </div>

                {{-- Redes sociales --}}
                <div class="flex items-center gap-2 flex-wrap justify-end">
                    @if($artista->enlace_instagram)
                        <a href="{{ $artista->enlace_instagram }}" target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-full bg-gray-100 hover:bg-violet-100 flex items-center justify-center text-base transition" title="Instagram">📸</a>
                    @endif
                    @if($artista->enlace_spotify)
                        <a href="{{ $artista->enlace_spotify }}" target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-full bg-gray-100 hover:bg-violet-100 flex items-center justify-center text-base transition" title="Spotify">🎧</a>
                    @endif
                    @if($artista->enlace_youtube)
                        <a href="{{ $artista->enlace_youtube }}" target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-full bg-gray-100 hover:bg-violet-100 flex items-center justify-center text-base transition" title="YouTube">▶️</a>
                    @endif
                    @if($artista->enlace_facebook)
                        <a href="{{ $artista->enlace_facebook }}" target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-full bg-gray-100 hover:bg-violet-100 flex items-center justify-center text-base transition" title="Facebook">👤</a>
                    @endif
                    @if($artista->enlace_web)
                        <a href="{{ $artista->enlace_web }}" target="_blank" rel="noopener"
                           class="w-9 h-9 rounded-full bg-gray-100 hover:bg-violet-100 flex items-center justify-center text-base transition" title="Sitio web">🌐</a>
                    @endif
                </div>
            </div>

            {{-- Descripción --}}
            @if($artista->descripcion)
                <p class="mt-4 text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $artista->descripcion }}</p>
            @endif

            {{-- Contacto --}}
            @if($artista->email_contacto || $artista->telefono)
                <div class="mt-5 pt-5 border-t border-gray-100 flex flex-wrap gap-3">
                    @if($artista->email_contacto)
                        <a href="mailto:{{ $artista->email_contacto }}"
                           class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                            ✉️ Contactar por email
                        </a>
                    @endif
                    @if($artista->telefono)
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $artista->telefono) }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                            📱 WhatsApp
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Portafolio --}}
    @if($artista->imagenes->isNotEmpty())
    @php
        $imgs = $artista->imagenes->map(fn($img) => asset('storage/' . $img->ruta))->values()->toArray();
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6"
         x-data="{
             images: {{ json_encode($imgs) }},
             current: 0,
             lightbox: false,
             prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
             next() { this.current = (this.current + 1) % this.images.length; },
             go(i) { this.current = i; },
         }"
         @keydown.arrow-left.window="if(lightbox) prev()"
         @keydown.arrow-right.window="if(lightbox) next()"
         @keydown.escape.window="lightbox = false">

        <h2 class="font-bold text-gray-800 mb-4">Portafolio</h2>

        {{-- Imagen principal --}}
        <div class="relative rounded-xl overflow-hidden bg-gray-100 aspect-4/3 cursor-zoom-in"
             @click="lightbox = true">
            <template x-for="(src, i) in images" :key="i">
                <img :src="src"
                     alt="{{ $artista->nombre }}"
                     x-show="current === i"
                     class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300">
            </template>

            {{-- Flechas --}}
            @if(count($imgs) > 1)
            <button @click.stop="prev()"
                    class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click.stop="next()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Contador --}}
            <div class="absolute bottom-2 right-2 bg-black/50 text-white text-xs font-semibold px-2 py-0.5 rounded-full"
                 x-text="(current + 1) + ' / ' + images.length"></div>
            @endif
        </div>

        {{-- Miniaturas --}}
        @if(count($imgs) > 1)
        <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
            <template x-for="(src, i) in images" :key="i">
                <button @click="go(i)"
                        class="shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition"
                        :class="current === i ? 'border-violet-500' : 'border-transparent opacity-60 hover:opacity-100'">
                    <img :src="src" alt="" class="w-full h-full object-cover">
                </button>
            </template>
        </div>
        @endif

        {{-- Lightbox --}}
        <div x-show="lightbox"
             x-transition:enter="transition duration-150"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center"
             @click.self="lightbox = false"
             style="display:none">

            <button @click="lightbox = false"
                    class="absolute top-4 right-4 text-white/70 hover:text-white transition">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <button @click="prev()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <template x-for="(src, i) in images" :key="i">
                <img :src="src"
                     x-show="current === i"
                     class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl select-none">
            </template>

            <button @click="next()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/60 text-sm"
                 x-text="(current + 1) + ' / ' + images.length"></div>
        </div>

    </div>
    @endif

</div>
@endsection
