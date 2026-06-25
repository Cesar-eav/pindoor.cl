@extends('layouts.pindoor')

@section('title', $artista->nombre . ' — Artista en Pindoor')
@section('canonical', route('artista.show', $artista->slug))
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('content')
@php
    $videoId = null;
    if ($artista->enlace_youtube_video) {
        preg_match('/(?:v=|youtu\.be\/|shorts\/)([a-zA-Z0-9_-]{11})/', $artista->enlace_youtube_video, $m);
        $videoId = $m[1] ?? null;
    }
@endphp

<div class="max-w-5xl mx-auto px-4 py-10">
    <div class="lg:flex lg:gap-6 lg:items-start">

        {{-- Columna principal --}}
        <div class="flex-1 min-w-0 space-y-6">

            {{-- Cabecera / perfil --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Banner --}}
                <div class="h-28 w-full" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 60%, #4f46e5 100%)"></div>

                <div class="px-6 pb-6">
                    {{-- Avatar + nombre en fila --}}
                    <div class="flex items-end justify-between gap-4 -mt-12 mb-4">
                        <div>
                            @if($artista->imagen_perfil)
                                <img src="{{ asset('storage/' . $artista->imagen_perfil) }}"
                                     alt="{{ $artista->nombre }}"
                                     class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                            @else
                                <div class="w-24 h-24 rounded-full bg-violet-100 border-4 border-white shadow-md flex items-center justify-center text-4xl">🎨</div>
                            @endif
                        </div>

                        {{-- Redes sociales --}}
                        <div class="flex items-center gap-2 flex-wrap justify-end pb-1">
                            @if($artista->enlace_instagram)
                                <a href="{{ $artista->enlace_instagram }}" target="_blank" rel="noopener" title="Instagram"
                                   class="w-9 h-9 rounded-full flex items-center justify-center transition hover:scale-110"
                                   style="background:linear-gradient(135deg,#f9a825,#e91e63,#9c27b0)">
                                    <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </a>
                            @endif
                            @if($artista->enlace_spotify)
                                <a href="{{ $artista->enlace_spotify }}" target="_blank" rel="noopener" title="Spotify"
                                   class="w-9 h-9 rounded-full flex items-center justify-center transition hover:scale-110"
                                   style="background:#1DB954">
                                    <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.361.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.26 11.28-1.02 15.721 1.621.539.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/></svg>
                                </a>
                            @endif
                            @if($artista->enlace_youtube)
                                <a href="{{ $artista->enlace_youtube }}" target="_blank" rel="noopener" title="Canal YouTube"
                                   class="w-9 h-9 rounded-full flex items-center justify-center transition hover:scale-110"
                                   style="background:#FF0000">
                                    <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            @endif
                            @if($artista->enlace_facebook)
                                <a href="{{ $artista->enlace_facebook }}" target="_blank" rel="noopener" title="Facebook"
                                   class="w-9 h-9 rounded-full flex items-center justify-center transition hover:scale-110"
                                   style="background:#1877F2">
                                    <svg class="w-4.5 h-4.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            @endif
                            @if($artista->enlace_web)
                                <a href="{{ $artista->enlace_web }}" target="_blank" rel="noopener" title="Sitio web"
                                   class="w-9 h-9 rounded-full bg-gray-700 hover:bg-gray-900 flex items-center justify-center transition hover:scale-110">
                                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>

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

                    @if($artista->descripcion)
                        <p class="mt-4 text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $artista->descripcion }}</p>
                    @endif

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

            {{-- Video en móvil (entre perfil y portafolio) --}}
            @if($videoId)
                <div class="lg:hidden bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="aspect-video">
                        <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                title="Video de {{ $artista->nombre }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                class="w-full h-full"></iframe>
                    </div>
                    @if($artista->enlace_youtube)
                        <div class="px-4 py-3 border-t border-gray-100">
                            <a href="{{ $artista->enlace_youtube }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700 transition">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                Ver canal de YouTube
                            </a>
                        </div>
                    @endif
                </div>
            @endif

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

                <div class="relative rounded-xl overflow-hidden bg-gray-100 aspect-4/3 cursor-zoom-in"
                     @click="lightbox = true">
                    <template x-for="(src, i) in images" :key="i">
                        <img :src="src" alt="{{ $artista->nombre }}"
                             x-show="current === i"
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300">
                    </template>

                    @if(count($imgs) > 1)
                    <button @click.stop="prev()"
                            class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click.stop="next()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div class="absolute bottom-2 right-2 bg-black/50 text-white text-xs font-semibold px-2 py-0.5 rounded-full"
                         x-text="(current + 1) + ' / ' + images.length"></div>
                    @endif
                </div>

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
                     x-transition:enter="transition duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="transition duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 bg-black/90 flex items-center justify-center"
                     @click.self="lightbox = false" style="display:none">
                    <button @click="lightbox = false" class="absolute top-4 right-4 text-white/70 hover:text-white transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <template x-for="(src, i) in images" :key="i">
                        <img :src="src" x-show="current === i" class="max-h-[90vh] max-w-[90vw] object-contain rounded-lg shadow-2xl select-none">
                    </template>
                    <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white/60 text-sm" x-text="(current + 1) + ' / ' + images.length"></div>
                </div>

            </div>
            @endif

        </div>{{-- fin columna principal --}}

        {{-- Sidebar derecha: video en desktop --}}
        @if($videoId)
        <div class="hidden lg:block lg:w-96 shrink-0 sticky top-6 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="aspect-video">
                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                            title="Video de {{ $artista->nombre }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                            class="w-full h-full"></iframe>
                </div>
                <div class="p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Video de presentación</p>
                    @if($artista->enlace_youtube)
                        <a href="{{ $artista->enlace_youtube }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:text-red-700 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            Ver canal de YouTube
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
