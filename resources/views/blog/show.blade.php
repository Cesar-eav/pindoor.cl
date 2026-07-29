@php
    use Illuminate\Support\Str;
    $seoTitle = $post->titulo . ' · Blog Valparaíso | Pindoor';
    $seoDesc  = $post->resumen
        ? Str::limit($post->resumen, 155, '')
        : Str::limit(strip_tags($post->contenido), 155, '');
@endphp

@extends('layouts.pindoor')

@section('title', $seoTitle)
@section('canonical', route('blog.show', $post->slug))
@section('description', $seoDesc)
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('head')
    @php $canonicalUrl = route('blog.show', $post->slug); @endphp
    <meta property="og:type"        content="article" />
    <meta property="og:url"         content="{{ $canonicalUrl }}" />
    <meta property="og:title"       content="{{ $seoTitle }}" />
    <meta property="og:description" content="{{ $seoDesc }}" />
    @if($post->imagen_portada_url)
    <meta property="og:image"       content="{{ $post->imagen_portada_url }}" />
    @endif
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:title"       content="{{ $seoTitle }}" />
    <meta name="twitter:description" content="{{ $seoDesc }}" />
    @if($post->imagen_portada_url)
    <meta name="twitter:image"       content="{{ $post->imagen_portada_url }}" />
    @endif

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "{{ addslashes($post->titulo) }}",
        "datePublished": "{{ $post->publicado_en?->toIso8601String() }}",
        "dateModified":  "{{ $post->updated_at->toIso8601String() }}",
        @if($post->imagen_portada_url)"image": "{{ $post->imagen_portada_url }}",@endif
        "publisher": { "@type": "Organization", "name": "Pindoor" },
        "mainEntityOfPage": "{{ $canonicalUrl }}"
        @if($post->lugares->isNotEmpty())
        ,"mentions": [
            @foreach($post->lugares as $lugar)
            {
                "@type": "LocalBusiness",
                "name": "{{ addslashes($lugar->title) }}",
                "url": "{{ route('puntos.show', $lugar->slug) }}"
                @if($lugar->direccion), "address": "{{ addslashes($lugar->direccion) }}"@endif
            }@if(!$loop->last),@endif
            @endforeach
        ]
        @endif
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
        .blog-fig {
            margin: 2.5rem 0;
        }
        .blog-fig img {
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
    @if($post->imagen_portada_url)
    <div class="w-full max-w-4xl mx-auto px-4 pt-10">
        <div class="aspect-video rounded-3xl overflow-hidden shadow-2xl shadow-gray-200">
            <img src="{{ $post->imagen_portada_url }}" alt="{{ $post->titulo }}"
                 class="w-full h-full object-cover">
        </div>
    </div>
    @endif

    {{-- Cuerpo del artículo --}}
    <div class="max-w-2xl mx-auto px-4 py-10">

        {{-- Breadcrumbs --}}
        <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-8">
            <a href="{{ route('puntos.index') }}" class="hover:text-[#fc5648] transition">Pindoor</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('blog.index') }}" class="hover:text-[#fc5648] transition">Blog</a>
            <span class="text-gray-300">/</span>
            <span class="text-[#fc5648] truncate max-w-45">{{ $post->titulo }}</span>
        </nav>

        {{-- Meta fecha --}}
        <p class="text-xs font-black uppercase tracking-widest text-[#fc5648] mb-3">
            {{ $post->publicado_en?->translatedFormat('d \d\e F \d\e Y') }}
        </p>

        {{-- Título --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900
                   tracking-tight leading-[1.1] mb-8">
            {{ $post->titulo }}
        </h1>

        {{-- Resumen destacado --}}
        @if($post->resumen)
        <p class="text-xl text-gray-500 leading-relaxed border-l-4 border-[#fc5648] pl-5 mb-10"
           style="font-family:'Lora',serif; font-style:italic;">
            {{ $post->resumen }}
        </p>
        @endif

        {{-- Separador --}}
        <hr class="border-gray-100 mb-10">

        {{-- Contenido con imágenes intercaladas --}}
        @php
            // Normalizar formato: soporta strings antiguos y objetos {ruta, posicion}
            $imagenesFmt = collect($post->imagenes ?? [])->map(function($img) {
                if (is_string($img)) return ['ruta' => $img, 'posicion' => null];
                return ['ruta' => $img['ruta'] ?? '', 'posicion' => $img['posicion'] ?? null];
            })->filter(fn($i) => !empty($i['ruta']))->values()->toArray();

            $contenidoFinal = $post->contenido ?? '';
            $imagenesAlFinal = collect();

            if (!empty($imagenesFmt) && !empty(trim(strip_tags($contenidoFinal)))) {

                // Dividir contenido en bloques por etiquetas de cierre
                $marcado = $contenidoFinal;
                foreach (['</p>','</h2>','</h3>','</h4>','</blockquote>','</ul>','</ol>'] as $tag) {
                    $marcado = str_replace($tag, $tag . '¶', $marcado);
                }
                $bloques    = array_values(array_filter(
                    array_map('trim', explode('¶', $marcado)),
                    fn($b) => trim(strip_tags($b)) !== ''
                ));
                $numBloques = count($bloques);

                // Separar imágenes posicionadas de automáticas
                $posicionadas = [];  // número de párrafo (1-based) → [rutas]
                $automaticas  = [];

                foreach ($imagenesFmt as $img) {
                    $pos = isset($img['posicion']) && $img['posicion'] > 0 ? (int)$img['posicion'] : null;
                    if ($pos) {
                        $posicionadas[$pos][] = $img['ruta'];
                    } else {
                        $automaticas[] = $img['ruta'];
                    }
                }

                // Calcular posiciones para las automáticas, evitando colisiones con manuales
                $insertarEn = $posicionadas;  // copia mutable
                if (!empty($automaticas)) {
                    $n          = count($automaticas);
                    $interval   = max(1, (int)floor($numBloques / ($n + 1)));
                    $autoIdx    = 0;
                    for ($p = $interval; $p <= $numBloques + 1 && $autoIdx < $n; $p++) {
                        if (!isset($insertarEn[$p])) {
                            $insertarEn[$p][] = $automaticas[$autoIdx++];
                        }
                    }
                    // Las que no cupieron en el bucle, al final
                    while ($autoIdx < $n) {
                        $insertarEn[$numBloques + 1][] = $automaticas[$autoIdx++];
                    }
                }

                // Renderizar
                $figuraHtml = function(string $ruta): string {
                    $url = asset('storage/' . $ruta);
                    return '<figure class="blog-fig"><img src="' . e($url) . '" alt=""></figure>';
                };

                $out = '';
                foreach ($bloques as $i => $bloque) {
                    $out .= $bloque;
                    $parNum = $i + 1;
                    foreach ($insertarEn[$parNum] ?? [] as $ruta) {
                        $out .= $figuraHtml($ruta);
                    }
                }
                // Imágenes con posición mayor al total de párrafos → carrusel final
                foreach ($insertarEn as $p => $rutas) {
                    if ($p > $numBloques) {
                        foreach ($rutas as $ruta) $imagenesAlFinal->push($ruta);
                    }
                }

                $contenidoFinal = $out;
            } elseif (!empty($imagenesFmt)) {
                // Sin texto: todas las fotos van al carrusel final
                $imagenesAlFinal = collect($imagenesFmt)->pluck('ruta');
            }
        @endphp

        <div class="blogtext">
            {!! $contenidoFinal !!}
        </div>

        {{-- Carrusel de imágenes restantes --}}
        @if($imagenesAlFinal->isNotEmpty())
        <div class="mt-10" x-data="{
                images: {{ $imagenesAlFinal->map(fn($r) => asset('storage/' . $r))->values()->toJson() }},
                current: 0,
                zoom: false,
                prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                next() { this.current = (this.current + 1) % this.images.length; },
             }">
            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">📷 Galería</p>

            <div class="relative bg-gray-900 rounded-2xl overflow-hidden h-72 sm:h-96">
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

        {{-- Bloque dinámico de locales recomendados --}}
        @if($post->lugares->isNotEmpty())
        <div class="mt-14 pt-8 border-t border-gray-100">
            <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4">
                📍 {{ $post->dynamic_block_title ?: 'Locales recomendados en Pindoor para esta ruta' }}
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($post->lugares as $lugar)
                <a href="{{ route('puntos.show', $lugar->slug) }}"
                   class="flex items-center gap-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all p-3">
                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                        @if($lugar->imagenPrincipal)
                            <img src="{{ asset('storage/' . $lugar->imagenPrincipal->ruta) }}" alt="{{ $lugar->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl text-gray-300">📍</div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-800 text-sm truncate">{{ $lugar->title }}</p>
                        @if($lugar->sector)
                        <p class="text-xs text-gray-400 truncate">{{ $lugar->sector }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>

            {{-- CTA: registro de negocios --}}
            <div class="mt-5 flex items-center justify-between gap-4 flex-wrap bg-[#fff0ef] border border-[#fc5648]/20 rounded-2xl px-5 py-4">
                <p class="text-sm text-gray-700">
                    ¿Tienes un local en Valparaíso? Registra tu negocio gratis en Pindoor.cl y aparece en nuestras guías.
                </p>
                <a href="{{ route('register') }}"
                   class="shrink-0 inline-flex items-center gap-2 bg-[#fc5648] hover:bg-[#e04035] text-white text-sm font-bold px-5 py-2.5 rounded-xl transition">
                    Registrar mi espacio →
                </a>
            </div>
        </div>
        @endif

        {{-- Footer del artículo --}}
        <div class="mt-16 pt-8 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
            <a href="{{ route('blog.index') }}"
               class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#fc5648] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver al blog
            </a>
            <a href="{{ route('puntos.index') }}"
               class="text-sm font-bold text-[#fc5648] hover:underline">
                Explorar Valparaíso →
            </a>
        </div>
    </div>

</article>
@endsection
