@extends('layouts.pindoor')

@php $planInfo = $recomendacion->plan_info; @endphp

@section('title', $recomendacion->titulo . ' — Pindoor Recomienda')
@section('canonical', route('recomienda.show', $recomendacion->slug))
@section('description', \Illuminate\Support\Str::limit($recomendacion->resumen ?: strip_tags($recomendacion->contenido ?? ''), 160)
    ?: $recomendacion->titulo . ' — ' . $recomendacion->negocio)
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('head')
@if($recomendacion->imagen_portada)
<meta property="og:image" content="{{ asset('storage/' . $recomendacion->imagen_portada) }}" />
@endif
<style>
    .resenatext { font-size: 1rem; line-height: 1.85; color: #374151; }
    .resenatext p    { margin: 1.1rem 0; }
    .resenatext h2   { font-size: 1.25rem; font-weight: 800; color: #111827; margin: 1.75rem 0 0.5rem; }
    .resenatext h3   { font-size: 1.1rem; font-weight: 700; color: #111827; margin: 1.4rem 0 0.4rem; }
    .resenatext strong, .resenatext b { font-weight: 700; color: #111827; }
    .resenatext em, .resenatext i { font-style: italic; }
    .resenatext u  { text-decoration: underline; }
    .resenatext a  { color: #fc5648; text-decoration: underline; }
    .resenatext a:hover { color: #d94439; }
    .resenatext ul { list-style: disc; padding-left: 1.5rem; margin: 0.75rem 0; }
    .resenatext ol { list-style: decimal; padding-left: 1.5rem; margin: 0.75rem 0; }
    .resenatext li { margin: 0.3rem 0; }
    .resenatext blockquote {
        border-left: 4px solid #fc5648;
        padding: 0.65rem 1rem;
        margin: 1.25rem 0;
        background: #fff5f5;
        border-radius: 0 0.75rem 0.75rem 0;
        font-style: italic;
        color: #6b7280;
    }
    .resena-fig { margin: 1.5rem 0; }
    .resena-fig img {
        width: 100%;
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        display: block;
    }
    .resenatext .ql-align-center { text-align: center; }
    .resenatext .ql-align-right  { text-align: right; }
</style>
@endsection

@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">

    {{-- Volver --}}
    <a href="{{ route('puntos.index') }}" wire:navigate
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 mb-5 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Volver a Pindoor
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Cabecera: video si está activado puntualmente, si no la imagen de portada --}}
        @if($recomendacion->video_en_cabecera && $recomendacion->video_youtube_id)
        <div class="bg-gray-900 aspect-video">
            <iframe src="https://www.youtube.com/embed/{{ $recomendacion->video_youtube_id }}?rel=0&modestbranding=1&iv_load_policy=3&fs=0"
                    class="w-full h-full" loading="lazy"></iframe>
        </div>
        @elseif($recomendacion->imagen_portada)
        <div class="bg-gray-900">
            <img src="{{ asset('storage/' . $recomendacion->imagen_portada) }}" alt="{{ $recomendacion->titulo }}"
                 class="w-full max-h-80 object-cover">
        </div>
        @endif

        <div class="p-6 space-y-4">

            {{-- Badges + compartir --}}
            <div class="flex items-start justify-between gap-2">
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#fff0ef] text-[#fc5648] text-xs font-bold">
                        {{ $planInfo['emoji'] }} {{ $planInfo['label'] }}
                    </span>
                    {{-- @if($recomendacion->destacado_portada && (!$recomendacion->destacado_hasta || $recomendacion->destacado_hasta->isFuture()))
                    <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold">🥇 Recomendado Pindoor</span>
                    @endif --}}
                    @if($recomendacion->rubro)
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">{{ $recomendacion->rubro }}</span>
                    @endif
                </div>

                @include('partials._share_panel', [
                    'shareText' => $recomendacion->titulo . ' — ' . route('recomienda.show', $recomendacion->slug),
                    'imageUrl'  => $recomendacion->imagen_portada ? asset('storage/' . $recomendacion->imagen_portada) : null,
                    'url'       => route('recomienda.show', $recomendacion->slug),
                ])
            </div>

            {{-- Título --}}
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-[1.1]">
                {{ $recomendacion->titulo }}
            </h1>

            {{-- Negocio --}}
            <div class="flex items-center gap-2.5">
                @if($recomendacion->puntoInteres?->imagen_perfil)
                <img src="{{ asset('storage/' . $recomendacion->puntoInteres->imagen_perfil) }}"
                     alt="Logo de {{ $recomendacion->negocio }}"
                     class="w-8 h-8 rounded-full object-cover border border-gray-100 shrink-0">
                @else
                <span class="shrink-0">🏪</span>
                @endif
                <p class="text-sm text-gray-600 font-semibold">{{ $recomendacion->negocio }}</p>
            </div>

            {{-- Resumen destacado --}}
            @if($recomendacion->resumen)
            <p class="text-lg text-gray-500 leading-relaxed border-l-4 border-[#fc5648] pl-5"
               style="font-family:'Lora',serif; font-style:italic;">
                {{ $recomendacion->resumen }}
            </p>
            @endif

            {{-- Contenido con imágenes intercaladas --}}
            @php
                $contenidoFinal = $recomendacion->contenido ?? '';
                $imagenesGaleria = $recomendacion->imagenes;
                // La galería final siempre muestra TODAS las fotos, se hayan intercalado
                // en el texto o no — no solo las que sobraron.
                $imagenesAlFinal = $imagenesGaleria->pluck('ruta');

                if ($imagenesGaleria->isNotEmpty() && trim(strip_tags($contenidoFinal)) !== '') {

                    $marcado = $contenidoFinal;
                    foreach (['</p>','</h2>','</h3>','</h4>','</blockquote>','</ul>','</ol>'] as $tag) {
                        $marcado = str_replace($tag, $tag . '¶', $marcado);
                    }
                    $bloques    = array_values(array_filter(
                        array_map('trim', explode('¶', $marcado)),
                        fn($b) => trim(strip_tags($b)) !== ''
                    ));
                    $numBloques = count($bloques);

                    $posicionadas = [];
                    $automaticas  = [];

                    foreach ($imagenesGaleria as $img) {
                        if ($img->posicion) {
                            $posicionadas[$img->posicion][] = $img->ruta;
                        } else {
                            $automaticas[] = $img->ruta;
                        }
                    }

                    $insertarEn = $posicionadas;
                    if (!empty($automaticas)) {
                        $n        = count($automaticas);
                        $interval = max(1, (int) floor($numBloques / ($n + 1)));
                        $autoIdx  = 0;
                        for ($p = $interval; $p <= $numBloques + 1 && $autoIdx < $n; $p++) {
                            if (!isset($insertarEn[$p])) {
                                $insertarEn[$p][] = $automaticas[$autoIdx++];
                            }
                        }
                        while ($autoIdx < $n) {
                            $insertarEn[$numBloques + 1][] = $automaticas[$autoIdx++];
                        }
                    }

                    $figuraHtml = function (string $ruta): string {
                        $url = asset('storage/' . $ruta);
                        return '<figure class="resena-fig"><img src="' . e($url) . '" alt=""></figure>';
                    };

                    $out = '';
                    foreach ($bloques as $i => $bloque) {
                        $out .= $bloque;
                        $parNum = $i + 1;
                        foreach ($insertarEn[$parNum] ?? [] as $ruta) {
                            $out .= $figuraHtml($ruta);
                        }
                    }
                    $contenidoFinal = $out;
                }
            @endphp

            @if($contenidoFinal)
            <div class="resenatext">
                {!! $contenidoFinal !!}
            </div>
            @endif

            {{-- Carrusel de imágenes restantes --}}
            @if($imagenesAlFinal->isNotEmpty())
            <div x-data="{
                    images: {{ $imagenesAlFinal->map(fn($r) => asset('storage/' . $r))->values()->toJson() }},
                    current: 0,
                    zoom: false,
                    prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                    next() { this.current = (this.current + 1) % this.images.length; },
                 }">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2"></p>

                <div class="relative bg-gray-900 rounded-2xl overflow-hidden h-72 sm:h-80">
                    <template x-for="(src, i) in images" :key="i">
                        <img :src="src" x-show="current === i" @click="zoom = true"
                             class="w-full h-full object-cover cursor-zoom-in">
                    </template>
                    @if($imagenesAlFinal->count() > 1)
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

                {{-- Zoom / lightbox --}}
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
                    <template x-for="(src, i) in images" :key="'zoom-' + i">
                        <img :src="src" x-show="current === i" @click.stop
                             class="max-w-full max-h-full object-contain rounded-2xl shadow-2xl select-none">
                    </template>
                    @if($imagenesAlFinal->count() > 1)
                    <button @click.stop="prev()"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button @click.stop="next()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            @endif

            {{-- Ubicación --}}
            @if($recomendacion->direccion)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#fff0ef] flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-[#fc5648]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-gray-800">{{ $recomendacion->direccion }}</p>
            </div>
            @endif

            {{-- Entrevista / reportaje en video (Plan Premium, salvo que ya se muestre en la cabecera) --}}
            @if($recomendacion->plan === 'premium' && $recomendacion->video_youtube_id && !$recomendacion->video_en_cabecera)
            <div class="pt-2">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">🎥 Entrevista / reportaje</p>
                <div class="aspect-video rounded-2xl overflow-hidden shadow-lg">
                    <iframe src="https://www.youtube.com/embed/{{ $recomendacion->video_youtube_id }}?rel=0&modestbranding=1&iv_load_policy=3&fs=0"
                            class="w-full h-full" loading="lazy"></iframe>
                </div>
            </div>
            @endif

            {{-- Video Reel (Instagram / TikTok) --}}
            @if($recomendacion->video_url)
            <a href="{{ $recomendacion->video_url }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 text-sm font-bold text-[#fc5648] hover:underline">
                🎬 Ver video reel
            </a>
            @endif

            {{-- CTAs --}}
            @if($recomendacion->puntoInteres || $recomendacion->whatsapp_url || $recomendacion->enlace)
            <div class="flex flex-col gap-2 mt-2">
                @if($recomendacion->puntoInteres)
                <a href="{{ route('puntos.show', $recomendacion->puntoInteres->slug) }}"
                   class="inline-flex items-center gap-2 w-full justify-center bg-gray-900 hover:bg-black text-white font-bold text-sm px-5 py-3 rounded-xl transition">
                    Ver ficha de {{ $recomendacion->negocio }} en Pindoor
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endif
                @if($recomendacion->whatsapp_url)
                <a href="{{ $recomendacion->whatsapp_url }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 w-full justify-center bg-green-500 hover:bg-green-600 text-white font-bold text-sm px-5 py-3 rounded-xl transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Contactar por WhatsApp
                </a>
                @endif
                @if($recomendacion->enlace)
                <a href="{{ $recomendacion->enlace }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 w-full justify-center bg-[#fc5648] hover:bg-[#e04035] text-white font-bold text-sm px-5 py-3 rounded-xl transition">
                    Reservar / más información
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                @endif
            </div>
            @endif

            {{-- Footer: autor + fecha --}}
            @if($recomendacion->autor || $recomendacion->publicado_en)
            <p class="text-xs text-gray-400 pt-2 border-t border-gray-100 mt-2">
                {{ $recomendacion->autor ?? 'Equipo Pindoor' }}
                @if($recomendacion->publicado_en)
                    · {{ $recomendacion->publicado_en->translatedFormat('d \d\e F \d\e Y') }}
                @endif
            </p>
            @endif

        </div>
    </div>

</div>
@endsection
