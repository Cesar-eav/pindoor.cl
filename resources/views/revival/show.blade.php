@php
    use Illuminate\Support\Str;
    $seoTitle = $revival->titulo . ' · Re-vival Panoramas | Pindoor';
    $seoDesc  = Str::limit(strip_tags($revival->contenido), 155, '');
@endphp

@extends('layouts.pindoor')

@section('title', $seoTitle)
@section('canonical', route('revival.show', $revival->slug))
@section('description', $seoDesc)
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('og_type', 'article')
@section('og_url', route('revival.show', $revival->slug))
@section('og_title', $seoTitle)
@section('og_description', $seoDesc)
@if($revival->imagen_portada_url)
    @section('og_image', $revival->imagen_portada_url)
@endif

@section('head')
    @php $canonicalUrl = route('revival.show', $revival->slug); @endphp

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "{{ addslashes($revival->titulo) }}",
        "datePublished": "{{ $revival->publicado_en?->toIso8601String() }}",
        "dateModified":  "{{ $revival->updated_at->toIso8601String() }}",
        @if($revival->imagen_portada_url)"image": "{{ $revival->imagen_portada_url }}",@endif
        @if($revival->autor)"author": { "@type": "Person", "name": "{{ addslashes($revival->autor) }}" },@endif
        "publisher": { "@type": "Organization", "name": "Pindoor" },
        "mainEntityOfPage": "{{ $canonicalUrl }}"
    }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .blogtext { font-family: 'Lora', serif; font-size: 1.125rem; line-height: 1.85; color: #374151; }
        .blogtext p    { margin: 1.1rem 0; }
        .blogtext h2   { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.4rem; font-weight: 800; color: #111827; margin: 2rem 0 0.5rem; }
        .blogtext h3   { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.15rem; font-weight: 700; color: #111827; margin: 1.5rem 0 0.4rem; }
        .blogtext strong, .blogtext b { font-weight: 700; color: #111827; }
        .blogtext em, .blogtext i { font-style: italic; }
        .blogtext u  { text-decoration: underline; }
        .blogtext a  { color: #fc5648; text-decoration: underline; }
        .blogtext a:hover { color: #d94439; }
        .blogtext ul { list-style: disc; padding-left: 1.75rem; margin: 0.75rem 0; }
        .blogtext ol { list-style: decimal; padding-left: 1.75rem; margin: 0.75rem 0; }
        .blogtext li { margin: 0.35rem 0; }
        .blogtext blockquote {
            border-left: 4px solid #fc5648;
            padding: 0.75rem 1.25rem;
            margin: 1.5rem 0;
            background: #fff5f5;
            border-radius: 0 0.75rem 0.75rem 0;
            font-style: italic;
            color: #6b7280;
        }
        .blogtext img {
            max-width: 100%;
            border-radius: 1rem;
            margin: 1.75rem auto;
            display: block;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .editorial-fig { margin: 2.5rem 0; }
        .editorial-fig img {
            width: 100%;
            border-radius: 1.25rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            display: block;
        }
        .blogtext .ql-align-center { text-align: center; }
        .blogtext .ql-align-right  { text-align: right; }
    </style>
@endsection

@section('content')
<article class="w-full">

    {{-- Portada --}}
    @if($revival->imagen_portada_url)
    <div class="w-full max-w-4xl mx-auto px-4 pt-10">
        <div class="aspect-video rounded-3xl overflow-hidden shadow-2xl shadow-gray-200">
            <img src="{{ $revival->imagen_portada_url }}" alt="{{ $revival->titulo }}"
                 class="w-full h-full object-cover">
        </div>
    </div>
    @endif

    {{-- Cuerpo del artículo --}}
    <div class="max-w-2xl mx-auto px-4 pb-10">

        {{-- Breadcrumbs --}}
        <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-8">
            <a href="{{ route('atractivos.panoramas') }}" wire:navigate class="hover:text-[#fc5648] transition">Panoramas</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('revival.index') }}" class="hover:text-[#fc5648] transition">Re-vival</a>
            <span class="text-gray-300">/</span>
            <span class="text-[#fc5648] truncate max-w-45">{{ $revival->titulo }}</span>
        </nav>

        {{-- Meta fecha + autor + compartir --}}
        <div class="flex items-start justify-between gap-2 mb-3">
            <p class="text-xs font-black uppercase tracking-widest text-[#fc5648]">
                🎬 {{ $revival->publicado_en?->translatedFormat('d \d\e F \d\e Y') }}
                @if($revival->autor)
                <span class="text-gray-400 normal-case font-semibold">· Por {{ $revival->autor }}</span>
                @endif
            </p>

            @include('partials._share_panel', [
                'shareText' => $revival->titulo . ' — ' . $canonicalUrl,
                'imageUrl'  => $revival->imagen_portada_url,
                'url'       => $canonicalUrl,
            ])
        </div>

        {{-- Título --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900
                   tracking-tight leading-[1.1] mb-8">
            {{ $revival->titulo }}
        </h1>

        {{-- Video del evento --}}
        @php
            $videoId = null;
            if ($revival->video_url && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $revival->video_url, $m)) {
                $videoId = $m[1];
            }
        @endphp
        @if($videoId)
        <div class="mb-10">
            <div class="aspect-video rounded-2xl overflow-hidden shadow-lg">
                <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                        class="w-full h-full" allowfullscreen loading="lazy"></iframe>
            </div>
        </div>
        @endif

        {{-- Separador --}}
        

        {{-- Contenido con imágenes intercaladas (mismo mecanismo que Guías) --}}
        @php
            $imagenesFmt = $revival->imagenes
                ->map(fn($img) => ['ruta' => $img->ruta, 'posicion' => $img->posicion])
                ->filter(fn($i) => !empty($i['ruta']))->values();

            // Todas las imágenes, en el orden elegido en el admin. Alimentan tanto
            // los clicks de zoom sobre las fotos intercaladas en el texto como la
            // galería completa al final del artículo — todas aparecen en ambos lados.
            $todasImagenes = $imagenesFmt->pluck('ruta')->values();
            $indicePorRuta = $todasImagenes->flip();

            $contenidoFinal = $revival->contenido ?? '';

            if ($imagenesFmt->isNotEmpty() && !empty(trim(strip_tags($contenidoFinal)))) {

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

                foreach ($imagenesFmt as $img) {
                    $pos = isset($img['posicion']) && $img['posicion'] > 0 ? (int)$img['posicion'] : null;
                    if ($pos) {
                        $posicionadas[$pos][] = $img['ruta'];
                    } else {
                        $automaticas[] = $img['ruta'];
                    }
                }

                $insertarEn = $posicionadas;
                if (!empty($automaticas)) {
                    $n          = count($automaticas);
                    $interval   = max(1, (int)floor($numBloques / ($n + 1)));
                    $autoIdx    = 0;
                    for ($p = $interval; $p <= $numBloques + 1 && $autoIdx < $n; $p++) {
                        if (!isset($insertarEn[$p])) {
                            $insertarEn[$p][] = $automaticas[$autoIdx++];
                        }
                    }
                    while ($autoIdx < $n) {
                        $insertarEn[$numBloques + 1][] = $automaticas[$autoIdx++];
                    }
                }

                $figuraHtml = function(string $ruta) use ($indicePorRuta): string {
                    $url = asset('storage/' . $ruta);
                    $idx = $indicePorRuta[$ruta] ?? 0;
                    return '<figure class="editorial-fig" @click="zoom = true; current = ' . $idx . '">'
                         . '<img src="' . e($url) . '" alt="" class="cursor-zoom-in"></figure>';
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

        <div x-data="{
                images: {{ $todasImagenes->map(fn($r) => asset('storage/' . $r))->values()->toJson() }},
                current: 0,
                zoom: false,
                prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                next() { this.current = (this.current + 1) % this.images.length; },
             }">

            <div class="blogtext">
                {!! $contenidoFinal !!}
            </div>

            {{-- Galería con todas las imágenes del re-vival, en el orden elegido en el admin --}}
            @if($todasImagenes->isNotEmpty())
            <div class="mt-10">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">📷 Galería</p>

                <div class="relative bg-gray-900 rounded-2xl overflow-hidden h-72 sm:h-96">
                    <template x-for="(src, i) in images" :key="i">
                        <img :src="src" x-show="current === i" @click="zoom = true"
                             class="w-full h-full object-cover cursor-zoom-in">
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
            </div>
            @endif

            {{-- Zoom / lightbox — cubre tanto la galería como las fotos intercaladas en el texto --}}
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
                @if($todasImagenes->count() > 1)
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

        {{-- Footer del artículo --}}
        <div class="mt-16 pt-8 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
            <a href="{{ route('revival.index') }}" wire:navigate
               class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#fc5648] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a Re-vival
            </a>
            <a href="{{ route('atractivos.panoramas') }}" wire:navigate
               class="text-sm font-bold text-[#fc5648] hover:underline">
                Ver próximos panoramas →
            </a>
        </div>
    </div>

</article>
@endsection
