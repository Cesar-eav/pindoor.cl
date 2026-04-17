@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $atractivo->title }} | El Pionero de Valparaíso</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ Str::limit(strip_tags($atractivo->description), 150) }}" />

    <!-- Open Graph -->
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('atractivos.show', $atractivo->slug) }}" />
    <meta property="og:title" content="{{ $atractivo->title }}" />
    <meta property="og:description" content="{{ Str::limit(strip_tags($atractivo->description), 150) }}" />
    @if ($atractivo->image)
        <meta property="og:image" content="{{ asset('storage/' . $atractivo->image) }}" />
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

    @if (app()->environment('production'))
<script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "vul5oo31fn");
</script>
    @endif
</head>

<body class="bg-gray-100 text-gray-900 font-serif text-base">
    <div class="w-full mx-auto md:p-4">
        <x-navbar_labrujula />

        <!-- Contenido Principal -->
        <div class="max-w-4xl mx-auto">


            <!-- Card Principal -->
            <article class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <!-- Imagen Principal -->
                @if ($atractivo->image)
                    <div class="w-full">
                        <img src="{{ asset('storage/' . $atractivo->image) }}"
                             alt="{{ $atractivo->title }}"
                             class="w-full h-96 object-cover" />
                    </div>
                @else
                    <div class="w-full h-96 bg-gray-300 flex items-center justify-center">
                        <span class="text-gray-600 text-6xl">📍</span>
                    </div>
                @endif

                <!-- Contenido -->
                <div class="p-8">
                    <!-- Categoría -->
                    <div class="mb-4">
                        <span class="inline-block bg-[#fc5648] text-white px-4 py-2 rounded-full text-sm font-semibold">
                            @if ($atractivo->categoria)
                                {{ $atractivo->categoria->icono }} {{ $atractivo->categoria->nombre }}
                            @endif
                        </span>
                    </div>

                    <!-- Título -->
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        {{ $atractivo->title }}
                    </h1>

                    <!-- Metadata -->
                    <div class="flex flex-col md:flex-row gap-6 mb-6 text-gray-600 border-b pb-6">
                        @if ($atractivo->ciudad)
                            <div class="flex items-center gap-2">
                                <a href="https://maps.google.com/?q={{ $atractivo->lat }},{{ $atractivo->lng }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="text-blue-600 hover:underline">
                                <span class="text-2xl">📍</span>
                                </a>

                            <span class="text-xl text-gray-600 mt-2">
                                <a href="https://maps.google.com/?q={{ $atractivo->lat }},{{ $atractivo->lng }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="text-blue-600 hover:underline">
                                    ¿Cómo llegar?
                                </a>
                            </span>

                            <span class="text-xl text-gray-600 mt-2">
                                <div
                                   {{-- target="_blank" --}}
                                   rel="noopener"
                                   class="text-blue-600">
                                    Contratar guías
                                </div>
                            </span>
                            </div>
                        @endif

                        @if ($atractivo->show_horario && $atractivo->horario)
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🕐</span>
                                <span class="text-lg">{{ $atractivo->horario }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Descripción -->
                    <div class="prose prose-lg max-w-none mb-8">
                        {{$atractivo->description}}
                    </div>


                    <!-- Galería de imágenes real y funcional -->
                    @php
                        $galeria = $atractivo->galeria && is_array($atractivo->galeria) && count($atractivo->galeria)
                            ? $atractivo->galeria
                            : [];

                    @endphp


@if ($atractivo->show_galeria && count($galeria))
    <div class="mb-8 relative group">
        <h3 class="text-2xl font-bold mb-4">Galería de fotos</h3>
        
        <div class="relative">
            <button onclick="moveSlider('prev')" class="absolute left-0 top-1/2 -translate-y-1/2 z-10 bg-black/50 text-white p-2 rounded-r-lg hover:bg-[#fc5648] transition-colors md:flex hidden">
                ❮
            </button>

            <div id="gallery-slider" class="flex overflow-x-auto gap-4 scroll-smooth snap-x snap-mandatory no-scrollbar pb-4">
                @foreach ($galeria as $img)
                    <div class="flex-none w-[calc(50%-0.5rem)] md:w-[calc(33.333%-0.75rem)] snap-start">
                        <a href="{{ asset('storage/' . $img) }}" class="glightbox" data-gallery="atractivo-gallery">
                            <img src="{{ asset('storage/' . $img) }}" 
                                 alt="Imagen galería" 
                                 class="w-full md:h-60 h-40 object-cover rounded-lg shadow-md cursor-pointer hover:opacity-90 transition" />
                        </a>
                    </div>
                @endforeach
            </div>

            <button onclick="moveSlider('next')" class="absolute right-0 top-1/2 -translate-y-1/2 z-10 bg-black/50 text-white p-2 rounded-l-lg hover:bg-[#fc5648] transition-colors md:flex hidden">
                ❯
            </button>
        </div>
    </div>
@endif

                    <!-- Enlace externo -->
                    @if ($atractivo->show_enlace && $atractivo->enlace)
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Sitio Web</h3>
                            <a href="{{ $atractivo->enlace }}"
                               target="_blank"
                               rel="noopener"
                               class="text-green-600 hover:underline text-lg">
                                {{ $atractivo->enlace }} →
                            </a>
                        </div>
                    @endif

                </div>
            </article>

            <!-- Atractivos relacionados -->
            @php
                $relacionados = \App\Models\Atractivo::where('categoria_id', $atractivo->categoria_id)
                    ->where('id', '!=', $atractivo->id)
                    ->limit(3)
                    ->get();
            @endphp

            @if ($relacionados->count())
                <section>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Más {{ $atractivo->categoria?->nombre }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($relacionados as $relacionado)
                            <article class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                                <!-- Imagen -->
                                <a href="{{ route('atractivos.show', $relacionado->slug) }}" class="block">
                                    @if ($relacionado->image)
                                        <img src="{{ asset('storage/' . $relacionado->image) }}"
                                             alt="{{ $relacionado->title }}"
                                             class="w-full h-48 object-cover hover:opacity-90 transition-opacity" />
                                    @else
                                        <div class="w-full h-48 bg-gray-300 flex items-center justify-center">
                                            <span class="text-gray-600 text-4xl">📍</span>
                                        </div>
                                    @endif
                                </a>

                                <!-- Contenido -->
                                <div class="p-4">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                        <a href="{{ route('atractivos.show', $relacionado->slug) }}"
                                           class="hover:text-[#fc5648] transition">
                                            {{ $relacionado->title }}
                                        </a>
                                    </h3>

                                    <a href="{{ route('atractivos.show', $relacionado->slug) }}"
                                       class="inline-block text-[#fc5648] font-semibold hover:underline">
                                        Explorar
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <!-- Footer -->
        {{-- <x-footer /> --}}
    </div>
</body>

</html>


<script>

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar GLightbox
    const lightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: true,
        autoplayVideos: true
    });
});

// Tu función moveSlider existente se mantiene igual...
function moveSlider(direction) {
    const slider = document.getElementById('gallery-slider');
    const scrollAmount = slider.clientWidth; 
    if (direction === 'next') {
        slider.scrollLeft += scrollAmount;
    } else {
        slider.scrollLeft -= scrollAmount;
    }
}

    function moveSlider(direction) {
        const slider = document.getElementById('gallery-slider');
        // Calculamos cuánto desplazar: el ancho de una imagen
        const scrollAmount = slider.clientWidth; 

        if (direction === 'next') {
            slider.scrollLeft += scrollAmount;
        } else {
            slider.scrollLeft -= scrollAmount;
        }
    }
</script>

<style>
    /* Ocultar la barra de scroll pero mantener la funcionalidad */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
