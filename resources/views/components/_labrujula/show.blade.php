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
    <meta property="og:url" content="{{ route('atractivos.show', $atractivo->id) }}" />
    <meta property="og:title" content="{{ $atractivo->title }}" />
    <meta property="og:description" content="{{ Str::limit(strip_tags($atractivo->description), 150) }}" />
    @if ($atractivo->image)
        <meta property="og:image" content="{{ asset('storage/' . $atractivo->image) }}" />
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900 font-serif text-base">
    <div class="w-full mx-auto md:p-4">
        <x-header />
        <x-navbar />

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
                            {{ ucfirst($atractivo->category) }}
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
                                <span class="text-2xl">📍</span>


                            <span class="text-xl text-gray-600 mt-2">
                                <a href="https://maps.google.com/?q={{ $atractivo->lat }},{{ $atractivo->lng }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="text-blue-600 hover:underline">
                                    ¿Cómo llegar?
                                </a>
                            </span>





                            </div>
                        @endif

                        @if ($atractivo->horario)
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

                    <!-- Enlace externo -->
                    @if ($atractivo->enlace)
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
                $relacionados = \App\Models\Atractivo::where('category', $atractivo->category)
                    ->where('id', '!=', $atractivo->id)
                    ->limit(3)
                    ->get();
            @endphp

            @if ($relacionados->count())
                <section>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Más {{ $atractivo->category }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($relacionados as $relacionado)
                            <article class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                                <!-- Imagen -->
                                <a href="{{ route('atractivos.show', $relacionado->id) }}" class="block">
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
                                        <a href="{{ route('atractivos.show', $relacionado->id) }}"
                                           class="hover:text-[#fc5648] transition">
                                            {{ $relacionado->title }}
                                        </a>
                                    </h3>

                                    <a href="{{ route('atractivos.show', $relacionado->id) }}"
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
        <x-footer />
    </div>
</body>

</html>
