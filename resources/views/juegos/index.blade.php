<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Juegos - El Pionero de Valparaíso</title>

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

<body class="bg-gray-100">

<div class="w-full  mx-auto md:p-4">
    <x-header />
           <div>

    <x-navbar />

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8 text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Juegos del Pionero</h1>
            <p class="text-lg text-red-600">Versión BETA 1.0</p>
            <p class="text-lg text-red-600">Los juegos pueden contener errores</p>

            <p class="text-lg text-gray-600">Diviértete con nuestros pasatiempos mientras aprendes sobre Valparaíso</p>
        </div>

        <!-- Grid de juegos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Sopa de Letras -->
            <a href="{{ route('juegos.sopa-letras') }}" class="group">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow h-full">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-700 p-8 text-white">
                        <div class="text-6xl mb-4">🔍</div>
                        <h2 class="text-2xl font-bold mb-2">Sopa de Letras</h2>
                        <p class="text-blue-100">La Joya del Pacífico</p>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 mb-4">
                            Encuentra palabras relacionadas con Valparaíso escondidas en una cuadrícula de letras.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Dificultad: Media</span>
                            <span class="text-blue-600 group-hover:text-blue-800 font-semibold">
                                Jugar →
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Trivia -->
            <a href="{{ route('juegos.trivia') }}" class="group">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow h-full">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-700 p-8 text-white">
                        <div class="text-6xl mb-4">🎯</div>
                        <h2 class="text-2xl font-bold mb-2">El Pionero Pregunta</h2>
                        <p class="text-purple-100">Trivia porteña</p>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 mb-4">
                            Pon a prueba tus conocimientos sobre la historia, cultura y actualidad de Valparaíso.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Dificultad: Variada</span>
                            <span class="text-purple-600 group-hover:text-purple-800 font-semibold">
                                Jugar →
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Portle -->
            <a href="{{ route('juegos.portle') }}" class="group">
                <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow h-full">
                    <div class="bg-gradient-to-br from-green-500 to-green-700 p-8 text-white">
                        <div class="text-6xl mb-4">🎯</div>
                        <h2 class="text-2xl font-bold mb-2">Portle</h2>
                        <p class="text-green-100">Wordle Porteño</p>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 mb-4">
                            Adivina la palabra porteña de 5 letras en 6 intentos. ¡Inspirado en Wordle!
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Dificultad: Media</span>
                            <span class="text-green-600 group-hover:text-green-800 font-semibold">
                                Jugar →
                            </span>
                        </div>
                    </div>
                </div>
            </a>

        </div>

        <!-- Estadísticas o información adicional -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">¿Por qué jugar?</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl mb-2">🧠</div>
                    <h4 class="font-semibold text-gray-900 mb-1">Aprende</h4>
                    <p class="text-sm text-gray-600">Descubre datos interesantes sobre Valparaíso</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl mb-2">⏱️</div>
                    <h4 class="font-semibold text-gray-900 mb-1">Relájate</h4>
                    <p class="text-sm text-gray-600">Toma un descanso entretenido</p>
                </div>
                <div class="text-center">
                    <div class="text-3xl mb-2">🎉</div>
                    <h4 class="font-semibold text-gray-900 mb-1">Diviértete</h4>
                    <p class="text-sm text-gray-600">Disfruta de pasatiempos variados</p>
                </div>
            </div>
        </div>

        <!-- Botón para volver -->
        <div class="mt-6 text-center">
            <a href="{{ route('inicio') }}" class="inline-block text-blue-600 hover:text-blue-800 font-medium">
                ← Volver al inicio
            </a>
        </div>
    </div>
</body>

</html>
