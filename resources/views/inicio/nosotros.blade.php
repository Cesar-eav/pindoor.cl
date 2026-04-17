<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7119582402031511"
     crossorigin="anonymous"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Noticias - El Pionero de Valparaíso</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    @if (app()->environment('production'))
        <script type="text/javascript">
            (function(c, l, a, r, i, t, y) {
                c[a] = c[a] || function() {
                    (c[a].q = c[a].q || []).push(arguments)
                };
                t = l.createElement(r);
                t.async = 1;
                t.src = "https://www.clarity.ms/tag/" + i;
                y = l.getElementsByTagName(r)[0];
                y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "rsqwi6wyvd");
        </script>
    @endif

</head>

<body class="bg-gray-100 flex items-center lg:justify-center min-h-screen flex-col">


    <div class="w-full  mx-auto p-4">
        <x-header />


        <x-navbar />

        <div class="bg-gray-100 min-h-screen py-8">
            <div class="container mx-auto ">
                <div class="lg:flex lg:items-start lg:justify-between">



                    <div>
                        <h1
                            class="md:text-center text-center text-4xl md:text-5xl lg:text-6xl font-bold text-gray-800 mb-4">
                            El Pionero de Valparaíso</h1>
                        <h2 class="text-xl md:text-2xl text-gray-600 mb-6">¡Menos postales, más realidad!</h2>

                        <div class="mb-8 text-gray-700">
                            <p class="mb-4">El Pionero de Valparaíso nace como un esfuerzo ciudadano por abordar los
                                desafíos que enfrenta nuestra querida ciudad puerto. Cansados de la retórica vacía y las
                                soluciones superficiales, buscamos ser un catalizador para el debate profundo y la
                                propuesta
                                de ideas concretas que realmente impacten en la calidad de vida de sus habitantes.</p>
                            <p class="mb-4">Creemos firmemente en el potencial de Valparaíso, en su riqueza cultural,
                                su
                                historia y su gente.
                                Sin embargo, reconocemos los problemas que nos aquejan: el descuido de los espacios
                                públicos, el transporte público, el deterioro del patrimonio, el empleo precario
                                y muchos otros. Nuestra revista mensual se dedicará a explorar estos temas con rigor,
                                buscando voces diversas y, sobre todo, enfocándonos en las posibles vías de solución.
                            </p>

                            <p>Con la convicción de que <strong>un cambio de rumbo es esencial para construir un camino
                                    de
                                    progreso que traiga calidad de vida</strong>, aspiramos a ser un espacio donde las
                                ideas
                                se traduzcan en acciones concretas. Invitamos a expertos y académicos,
                                a líderes comunitarios y a la voz de cada habitante de Valparaíso a unirse a este
                                diálogo
                                constructivo para edificar
                                colectivamente un futuro más próspero y equitativo para nuestra querida ciudad.</p>
                        </div>


                    </div>


                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Macarena -->
                    <article
                        class="group overflow-hidden rounded-xl border bg-white shadow-sm transition hover:shadow-xl">
                        <div class="flex flex-col md:flex-row">

                            <!-- Imagen -->
                            <figure class="relative w-full md:w-1/2 md:h-80 flex-shrink-0">
                                <img src="{{ asset('storage/maca.jpeg') }}" alt="Retrato de Macarena Gómez Acevedo"
                                    class="w-full h-full object-cover filter grayscale transition duration-500 ease-out group-hover:grayscale-0 group-hover:scale-[1.02]" />
                                <figcaption
                                    class="hidden md:flex absolute inset-x-0 bottom-0 items-center justify-between gap-3 bg-gradient-to-t from-black/70 to-transparent p-4">
                                    <h3 class="text-white text-2xl font-extrabold tracking-tight">
                                        Macarena Gómez Acevedo
                                    </h3>
                                    <a href="https://www.linkedin.com/in/macarena-g%C3%B3mez-acevedo-010828b8/" target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-2 rounded-full bg-white/90 px-3 py-1 text-sm font-semibold text-gray-900 hover:bg-white transition"
                                        aria-label="LinkedIn de Macarena Gómez Acevedo" title="LinkedIn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path
                                                d="M19 0h-14C2.239 0 0 2.239 0 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5V5c0-2.761-2.238-5-5-5zm-11.75 20H4V9h3.25v11zM5.625 7.75c-1.036 0-1.875-.84-1.875-1.875S4.589 4 5.625 4 7.5 4.84 7.5 5.875 6.661 7.75 5.625 7.75zM20 20h-3.25v-6.02c0-1.437-.027-3.284-2-3.284-2.002 0-2.31 1.563-2.31 3.179V20H9.188V9H12.25v1.5h.046c.426-.807 1.468-1.657 3.021-1.657 3.233 0 3.83 2.129 3.83 4.897V20z" />
                                        </svg>
                                        LinkedIn
                                    </a>
                                </figcaption>
                            </figure>

                            <!-- Texto -->
                            <div class="w-full md:w-1/2 p-5 md:h-80 overflow-y-auto">
                                <div class="md:hidden mb-3">
                                    <h3 class="text-2xl font-extrabold text-gray-900">Macarena Gómez Acevedo</h3>
                                    <a href="https://www.linkedin.com/in/macarena-g%C3%B3mez-acevedo-010828b8/"
                                        target="_blank" rel="noopener"
                                        class="mt-2 inline-flex items-center gap-2 rounded-full bg-gray-900 text-white px-3 py-1 text-sm font-semibold"
                                        aria-label="LinkedIn de Macarena Gómez Acevedo" title="LinkedIn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path
                                                d="M19 0h-14C2.239 0 0 2.239 0 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5V5c0-2.761-2.238-5-5-5zm-11.75 20H4V9h3.25v11zM5.625 7.75c-1.036 0-1.875-.84-1.875-1.875S4.589 4 5.625 4 7.5 4.84 7.5 5.875 6.661 7.75 5.625 7.75zM20 20h-3.25v-6.02c0-1.437-.027-3.284-2-3.284-2.002 0-2.31 1.563-2.31 3.179V20H9.188V9H12.25v1.5h.046c.426-.807 1.468-1.657 3.021-1.657 3.233 0 3.83 2.129 3.83 4.897V20z" />
                                        </svg>
                                        LinkedIn
                                    </a>
                                </div>

                                <div class="space-y-4 text-gray-700 leading-relaxed">
                                    <p class="text-base">
                                        Oriunda de Playa Ancha y residente actual de Cerro Delicias, arquitecta de la
                                        Universidad de Valparaíso, socia de la Agrupación de Usuarios y Usuarias de
                                        Ascensores de Ascenval y del Colegio de Arquitectos de Valparaíso.
                                    </p>
                                    <p>
                                        Desde mi trabajo como arquitecta, me mueve comprender el fenómeno social de las
                                        comunidades y cómo estas construyen y transforman las ciudades. Creo en un
                                        urbanismo que nace de la participación y el compromiso ciudadano, capaz de
                                        construir territorios plenos y justos.
                                    </p>
                                    <p>
                                        Mi motivación es aportar a que Valparaíso recupere su esplendor, fortaleciendo
                                        su identidad y proyectándola hacia un futuro inclusivo y sostenible, respetando
                                        su valor patrimonial, incorporando innovación y tecnología con una visión de
                                        largo plazo.
                                    </p>
                                    <p>
                                        Lo que me impulsa a trabajar en El Pionero de Valparaíso es generar un espacio
                                        de diálogo para dar voz y escuchar a las comunidades, y construir en conjunto un
                                        mejor Valparaíso.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </article>

                    <!-- César -->
                    <article
                        class="group overflow-hidden rounded-xl border bg-white shadow-sm transition hover:shadow-xl">
                        <div class="flex flex-col md:flex-row">

                            <!-- Imagen -->
                            <figure class="relative w-full md:w-1/2 md:h-80 flex-shrink-0">
                                <img src="{{ asset('storage/Cesar.jpg') }}" alt="Retrato de César Andrade Valdebenito"
                                    class="w-full h-full object-cover filter grayscale transition duration-500 ease-out group-hover:grayscale-0 group-hover:scale-[1.02]" />
                                <figcaption
                                    class="hidden md:flex absolute inset-x-0 bottom-0 items-center justify-between gap-3 bg-gradient-to-t from-black/70 to-transparent p-4">
                                    <h3 class="text-white text-2xl font-extrabold tracking-tight">
                                        César Andrade Valdebenito
                                    </h3>
                                    <a href="https://www.linkedin.com/in/cesar-andrade-chile/" target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center gap-2 rounded-full bg-white/90 px-3 py-1 text-sm font-semibold text-gray-900 hover:bg-white transition"
                                        aria-label="LinkedIn de César Andrade Valdebenito" title="LinkedIn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path
                                                d="M19 0h-14C2.239 0 0 2.239 0 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5V5c0-2.761-2.238-5-5-5zm-11.75 20H4V9h3.25v11zM5.625 7.75c-1.036 0-1.875-.84-1.875-1.875S4.589 4 5.625 4 7.5 4.84 7.5 5.875 6.661 7.75 5.625 7.75zM20 20h-3.25v-6.02c0-1.437-.027-3.284-2-3.284-2.002 0-2.31 1.563-2.31 3.179V20H9.188V9H12.25v1.5h.046c.426-.807 1.468-1.657 3.021-1.657 3.233 0 3.83 2.129 3.83 4.897V20z" />
                                        </svg>
                                        LinkedIn
                                    </a>
                                </figcaption>
                            </figure>

                            <!-- Texto -->
                            <div class="w-full md:w-1/2 p-5 md:h-80 overflow-y-auto">
                                <div class="md:hidden mb-3">
                                    <h3 class="text-2xl font-extrabold text-gray-900">César Andrade Valdebenito</h3>
                                    <a href="https://www.linkedin.com/in/cesar-andrade-chile/" target="_blank" rel="noopener"
                                        class="mt-2 inline-flex items-center gap-2 rounded-full bg-gray-900 text-white px-3 py-1 text-sm font-semibold"
                                        aria-label="LinkedIn de César Andrade Valdebenito" title="LinkedIn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <path
                                                d="M19 0h-14C2.239 0 0 2.239 0 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5V5c0-2.761-2.238-5-5-5zm-11.75 20H4V9h3.25v11zM5.625 7.75c-1.036 0-1.875-.84-1.875-1.875S4.589 4 5.625 4 7.5 4.84 7.5 5.875 6.661 7.75 5.625 7.75zM20 20h-3.25v-6.02c0-1.437-.027-3.284-2-3.284-2.002 0-2.31 1.563-2.31 3.179V20H9.188V9H12.25v1.5h.046c.426-.807 1.468-1.657 3.021-1.657 3.233 0 3.83 2.129 3.83 4.897V20z" />
                                        </svg>
                                        LinkedIn
                                    </a>
                                </div>

                                <div class="space-y-4 text-gray-700 leading-relaxed">
                                    <p class="text-base">
                                        Actualmente vivo en cerro Cordillera. Llegué a Valparaíso el año 2013 al cerro
                                        Las Cañas. En el primer trabajo que conseguí me tocó conocer muchos cerros de la
                                        ciudad, especialmente las partes altas.
                                    </p>
                                    <p>
                                        De a poco me fui encantando con Valparaíso al mismo tiempo que me frustraba. Decidí que algo
                                        tenía que hacer y entré al Colectivo Acción Valparaíso, desde el cual nació la idea de generar
                                        un grupo de presión para recuperar los ascensores de la ciudad, lo que posteriormente se transformó en ASCENVAL el 2021, 
                                    agrupación ASCENVAL de la cual fui
                                        presidente hasta 2024.               </p>
                                    <p>
                                       Hoy estoy embarcado en El Pionero de Valparaíso, un espacio de debate que aporte a la ciudad con soluciones. 
                                    </p>
                                </div>
                            </div>

                        </div>
                    </article>

                </div>



            </div>

            <!-- Footer -->
            <x-footer />
    </div>

</body>

</html>
