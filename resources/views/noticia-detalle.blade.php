<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $noticia->titulo }} - El Pionero de Valparaíso</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $noticia->resumen ?? Str::limit(strip_tags($noticia->cuerpo), 160) }}">
    <meta name="author" content="El Pionero de Valparaíso">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $noticia->titulo }}">
    <meta property="og:description" content="{{ $noticia->resumen ?? Str::limit(strip_tags($noticia->cuerpo), 160) }}">
    @if($noticia->imagen)
        <meta property="og:image" content="{{ asset('storage/' . $noticia->imagen) }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    <meta property="article:published_time" content="{{ $noticia->fecha_publicacion?->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $noticia->updated_at?->toIso8601String() }}">
    <meta property="article:section" content="Noticias">
    <meta property="og:site_name" content="El Pionero de Valparaíso">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $noticia->titulo }}">
    <meta name="twitter:description" content="{{ $noticia->resumen ?? Str::limit(strip_tags($noticia->cuerpo), 160) }}">
    @if($noticia->imagen)
        <meta name="twitter:image" content="{{ asset('storage/' . $noticia->imagen) }}">
    @endif

    <!-- Google News / Schema.org structured data -->
    @php
        $schemaData = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $noticia->titulo,
            'description' => $noticia->resumen ?? Str::limit(strip_tags($noticia->cuerpo), 160),
            'datePublished' => $noticia->fecha_publicacion?->toIso8601String(),
            'dateModified' => $noticia->updated_at?->toIso8601String(),
            'author' => [
                '@type' => 'Organization',
                'name' => 'El Pionero de Valparaíso',
                'url' => config('app.url')
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'El Pionero de Valparaíso',
                'url' => config('app.url'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('storage/portada.png')
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url()->current()
            ],
            'articleSection' => 'Noticias',
            'inLanguage' => 'es'
        ];

        if($noticia->imagen) {
            $schemaData['image'] = [
                '@type' => 'ImageObject',
                'url' => asset('storage/' . $noticia->imagen),
                'width' => 1200,
                'height' => 630
            ];
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (app()->environment('production'))
        <script type="text/javascript">
            (function(c, l, a, r, i, t, y) {
                c[a] = c[a] || function() {
                    (c[a].q = c[a].q || []).push(arguments)
                };
                t = l.createElement(r); t.async = 1;
                t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "rsqwi6wyvd");
        </script>
    @endif
</head>

<body class="bg-gray-100 flex items-center lg:justify-center min-h-screen flex-col">
    <div class="w-full mx-auto p-4">
        <!-- Encabezado -->
        <header class="text-center mb-1">
            <img src="{{ asset('storage/portada.png') }}" class="w-full mx-auto mb-4 rounded shadow md:block hidden" />
            <img src="{{ asset('storage/logo_m.png') }}" class="w-full mx-auto mb-4 rounded shadow block md:hidden" />
        </header>

        <x-navbar />

        <div class="bg-gray-100 w-full">
            <div class="max-w-4xl mx-auto">
                <!-- Contenido de la noticia -->
                <article class="bg-white rounded-lg shadow-lg p-6 md:p-8">
                    <!-- Fecha -->
                    <div class="text-sm text-gray-500 mb-2">
                        {{ $noticia->fecha_publicacion?->format('d/m/Y') ?? '' }}
                    </div>

                    <!-- Título -->
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        {{ $noticia->titulo }}
                    </h1>

                    <!-- Resumen (si existe) -->
                    @if($noticia->resumen)
                        <div class="text-lg text-gray-700 mb-6 font-medium border-l-4 border-gray-300 pl-4 italic">
                            {{ $noticia->resumen }}
                        </div>
                    @endif

                    <!-- Imagen (si existe) -->
                    @if($noticia->imagen)
                        <div class="mb-6">
                            <img src="{{ asset('storage/' . $noticia->imagen) }}"
                                 alt="{{ $noticia->titulo }}"
                                 class="w-full rounded-lg shadow-md">
                        </div>
                    @endif

                    <!-- Contenido completo -->
                    <div class="text-gray-800 text-base leading-relaxed prose prose-lg max-w-none">
                        {!! $noticia->cuerpo !!}
                    </div>

                    <!-- Botón volver -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ url()->previous() }}"
                           class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Volver
                        </a>
                    </div>
                </article>
            </div>
        </div>

        <!-- Footer -->
        <x-footer />
    </div>
</body>
</html>
