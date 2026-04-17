<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>El Pionero - {{ $articulo->titulo }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @commenterStyles

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
            <div class="flex flex-col md:flex-row gap-6 mt-5">
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
                                @if ($articulo->columnista && $articulo->columnista->foto)
                                    <div class="flex-shrink-0">
                                        <img src="{{ asset('storage/' . $articulo->columnista->foto) }}"
                                            alt="{{ $articulo->columnista->nombre }}"
                                            class="w-20 h-20 rounded-full shadow object-cover" />
                                    </div>
                                @endif
                                <div class="flex flex-col justify-center">
                                    <h3 class="text-2xl font-bold text-black mb-1">{{ $articulo->titulo }}</h3>
                                    @if ($articulo->columnista)
                                        <p class="text-sm italic text-gray-600">Por: {{ $articulo->columnista->nombre }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="text-gray-800 text-base leading-relaxed prose prose-p:mb-4 prose-headings:mb-3 prose-headings:mt-6 prose-ul:mb-4 prose-ol:mb-4 max-w-none whitespace-pre-line">
                                {!! $articulo->contenido !!}
                            </div>
                        </article>
                    <div class="mt-6 bg-white border border-gray-300 rounded-lg p-6 shadow-sm">
                        <x-commenter::index :model="$articulo" /> 
                    </div>
                    </div>
                </main>

                <!-- Sidebar derecha -->
                <aside class="w-full md:w-2/6 space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">
                    <div>

                        <div>
                            <a href="https://www.instagram.com/manos_.alarte/" target="_blank">
                                <img src="{{ asset('storage/manosalarte.jpeg') }}" alt="Anuncio Mediano"
                                    class="w-full rounded border shadow" /></a>
                        </div>
                        <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Columnas de Opinión</h2>

                        @if ($articulos->isNotEmpty())
                            @foreach ($articulos->chunk(ceil($articulos->count() / 2)) as $columnArticulos)
                                <div class="space-y-4 mt-4">
                                    @foreach ($columnArticulos as $articulo)
                                        <div>
                                            <a href="{{ url('articulo') . '/' . $articulo->slug }}">
                                                <h4 class="text-base font-bold text-black mb-1">{{ $articulo->titulo }}
                                                </h4>
                                            </a>
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

                </aside>
            </div>

            <!-- Footer -->
            <x-footer />

            <div class="fixed bottom-2 right-2 text-xs text-gray-400">1</div>
        </div>
        @commenterScripts
    </body>

</html>
