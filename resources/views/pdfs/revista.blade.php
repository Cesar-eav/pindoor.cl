<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ now()->format('d de F de Y') }}</title>
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

<body class="bg-gray-100 text-gray-900 font-serif text-base">
    <div class="max-w-7xl mx-auto p-4">
        <!-- Encabezado -->
        <header class="text-center mb-8">
            <img src="{{ asset('storage/portada.png') }}" alt="Portada de Periódico"
                class="w-full  mx-auto mb-4 rounded shadow" />

        </header>
        <div>

    <x-navbar /> 

            <!-- Layout principal -->
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar izquierda -->
                <aside
                    class="w-full md:w-1/6 hidden md:block space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Noticias</h2>
                        <div class="mt-4">
                            <h4 class="text-sm font-bold text-gray-700">Parque Barón</h4>
                            <p class="text-xs text-gray-700">
                                Aún no hay modelo de gestión para el funcionamiento de la Bodega Simón Bolivar.
                            </p>
                        </div>
                        <div class="mt-4">
                            <h4 class="text-sm font-bold text-gray-700">Proponen ciclovía en Av. España</h4>
                            <a href="https://www.pucv.cl/pucv/investigadores-proponen" target="_blank">
                                <p class="text-xs text-gray-700">Académicos de la PUCV elaboran propuesta para mejorar
                                    la movilidad entre Viña del Mar
                                    y Valparaíso.
                                </p>
                            </a>
                        </div>

                </aside>

                <!-- Contenido principal -->
                <main class="w-full md:w-3/6 space-y-10">

                    <div class="space-y-6">

                        <article class="border-b pb-6">
                            <div class="flex items-start gap-4 mb-4">
                                @if ($articulo_portada->columnista && $articulo_portada->columnista->foto)
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('storage/' . $articulo_portada->columnista->foto) }}"
                                            alt="{{ $articulo_portada->columnista->nombre }}"
                                            class="w-20 h-20 rounded-full shadow object-cover" />
                                    </div>
                                @endif
                                <div class="flex flex-col justify-center">
                                    <h3 class="text-2xl font-bold text-black mb-1">{{ $articulo_portada->titulo }}</h3>
                                    @if ($articulo_portada->columnista)
                                        <p class="text-sm italic text-gray-600">Por: {{ $articulo_portada->columnista->nombre }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="text-gray-800 text-base leading-relaxed prose prose-p:mb-4 prose-headings:mb-3 prose-headings:mt-6 prose-ul:mb-4 prose-ol:mb-4 max-w-none whitespace-pre-line">
                                {!! $articulo_portada->contenido !!}
                            </div>
                        </article>

                    </div>
                </main>

                <!-- Sidebar derecha -->
                <aside class="w-full md:w-2/6 space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Columnas de Opinión</h2>

                        @if ($articulos->isNotEmpty())
                            @foreach ($articulos->chunk(ceil($articulos->count() / 2)) as $columnArticulos)
                                <div class="space-y-4 mt-4">
                                    @foreach ($columnArticulos as $articulo)
                                        <div>
                                            <a href="{{ url('articulo') . '/' . $articulo->id }}">
                                                <h4 class="text-base font-bold text-black mb-1">{{ $articulo->titulo }}
                                                </h4>
                                                @if ($articulo->columnista)
                                                    <div class="text-sm italic text-gray-600 flex items-center">
                                                        @if ($articulo->columnista->foto)
                                                            <img src="{{ asset('storage/' . $articulo->columnista->foto) }}"
                                                                alt="{{ $articulo->columnista->nombre }}"
                                                                class="w-8 h-8 rounded-full mr-2 shadow" />
                                                        @endif
                                                        Por: {{ $articulo->columnista->nombre }}
                                                    </div>
                                                @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <p>No hay artículos en esta revista.</p>
                        @endif
                    </div>

                    <div>
                        <a href="https://www.instagram.com/manos_.alarte/" target="_blank">
                            <img src="{{ asset('storage/manosalarte.jpeg') }}" alt="Anuncio Mediano"
                                class="w-full rounded border shadow" /></a>
                    </div>
                </aside>
            </div>



            <footer class="bg-black text-white py-10 px-6 mt-12 font-sans">
                <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-10">
                    <!-- Título -->
                    <div class="text-4xl font-extrabold tracking-wide font-serif text-center md:text-left">
                            <span class="text-[#fc5648]">RE</span><span class="text-[#eba81d]">VIS</span><span class="text-white">TAS</span>

                        <div class="hidden md:block">
                            <span class="text-[#fc5648]">RE</span><br />
                            <span class="text-[#eba81d]">VIS</span><br />
                            <span class="text-white">TAS</span>
                        </div>
                    </div>

                    <!-- Descargas -->
                    <div class="flex gap-6 flex-wrap justify-center">
                        <!-- Revista de Mayo -->
                        <div class="flex flex-col items-center text-center">
                            <p class="text-[#eba81d] font-semibold mb-2 font-mono">Mayo</p>
                            <a href="https://drive.google.com/file/d/1b304pV29d66y29Ib36fhY589WE-2fIJn/view?usp=sharing"
                                target="_blank">
                                <img src="/storage/Portada_ED1.jpg" alt="Revista Mayo"
                                    class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                            </a>
                        </div>
                        <!-- Revista de Junio -->
                        <div class="flex flex-col items-center text-center">
                            <p class="text-[#eba81d] font-semibold mb-2 font-mono">Junio</p>
                            <a href="https://drive.google.com/file/d/1qTuBM4XDMgUnSHh9mKFtj4OeNmjvQ2pd/view?usp=sharing"
                                target="_blank">
                                <img src="/storage/Portada_ED2.jpeg" alt="Revista Junio"
                                    class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                            </a>
                        </div>
                        <!-- Revista de Julio -->
                        <div class="flex flex-col items-center text-center">
                            <p class="text-[#eba81d] font-semibold mb-2 font-mono">Julio</p>
                            <a href="https://drive.google.com/file/d/1Dj_RuAkSLy-0vzvLMseaw1ggPaakpEQY/view?usp=sharing"
                                target="_blank">
                                <img src="/storage/Portada_ED3.jpeg" alt="Revista Julio"
                                    class="w-24 h-auto rounded shadow-md filter grayscale hover:grayscale-0 hover:scale-105 transition duration-300">
                            </a>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="text-sm mt-6 md:mt-0 font-light text-center md:text-right">
                        <p class="text-gray-300">&copy; {{ date('Y') }} El Pionero de Valparaíso</p>
                        <p class="text-gray-400">Todos los derechos reservados</p>
                    </div>
                </div>
            </footer>





            <!-- Pie de página -->
            <footer class="text-center text-sm text-gray-600 mt-10 pt-4 border-t border-gray-300">

            </footer>


            <div class="fixed bottom-2 right-2 text-xs text-gray-400">1</div>
        </div>
</body>

</html>
