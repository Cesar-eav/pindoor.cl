<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>El Pionero de Valparaíso</title>

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

@php
    use Illuminate\Support\Str;
@endphp

<body class="bg-gray-100 flex items-center lg:justify-center min-h-screen flex-col">
    <div class="w-full mx-auto p-4">
        <!-- Encabezado -->
        <header class="text-center mb-1">
            <img src="{{ asset('storage/portada.png') }}" class="w-full mx-auto mb-4 rounded shadow md:block hidden" />
            <img src="{{ asset('storage/logo_m.png') }}" class="w-full mx-auto mb-4 rounded shadow block md:hidden" />
        </header>

        <x-navbar />

        <div class="bg-gray-100 w-full">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- ASIDE -->
                <aside class="hidden md:block md:col-span-1 w-full space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Noticias</h2>

                        <div class="mt-4 border-gray-300 border p-3 rounded">
                            <h4 class="text-md font-bold text-gray-700 mb-1">Parque Barón</h4>
                            <p class="text-sm text-gray-700">
                                Aún no hay modelo de gestión para el funcionamiento de la Bodega Simón Bolivar.
                            </p>
                        </div>

                        <div class="mt-4 border-gray-300 border p-3 rounded">
                            <h4 class="text-md font-bold text-gray-700 mb-1">Proponen ciclovía en Av. España</h4>
                            <a href="https://www.pucv.cl/pucv/investigadores-proponen" target="_blank" class="block">
                                <p class="text-sm text-gray-700">
                                    Académicos de la PUCV elaboran propuesta para mejorar la movilidad entre Viña del Mar y Valparaíso.
                                </p>
                            </a>
                        </div>

                        <div class="mt-4 border-gray-300 border p-3 rounded">
                            <h4 class="text-md font-bold text-gray-700 mb-1">Dos buenas noticias para Valparaíso</h4>
                            <p class="text-sm text-gray-700">
                                Pronto se inaugurará
                                <a href="https://www.instagram.com/destinovalpo/" target="_blank" class="font-bold">Destino Valparaíso</a>,
                                que albergará el Museo del Inmigrante. Además,
                                <a href="https://www.instagram.com/bancoestado/reel/DNTO6l1x1_s/" target="_blank" class="font-bold">Banco Estado</a>
                                ha remodelado el edificio patrimonial de calle Prat, con acceso mediante ascensor desde el Paseo Yugoslavo.
                            </p>
                        </div>

                        <div class="mt-4 border-gray-300 border p-3 rounded">
                            <h4 class="text-md font-bold text-gray-700 mb-1">Ruta Fiscalización Ascensores</h4>
                            <p class="text-sm text-gray-700">
                                La agrupación <a href="https://www.ascenval.cl/" target="_blank" class="font-bold">Ascenval</a>
                                realizará una nueva ruta de fiscalización este domingo 17 a las 16:00 hrs. Punto de encuentro: estación baja del ascensor Polanco.
                            </p>
                        </div>

                        <div class="mt-2">
                            <a href="https://www.instagram.com/manos_.alarte/" target="_blank">
                                <img src="{{ asset('storage/manosalarte.jpeg') }}" alt="Anuncio Mediano"
                                     class="w-full rounded border shadow" />
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- MAIN: Noticias dinámicas -->
                <main class="md:col-span-2 w-full">
                    <h2 class="mt-5 md:mt-0 text-xl font-semibold text-gray-800 border-b pb-2">Últimas noticias</h2>

                {{$noticias }}

                    @if ($noticias->count())
                        {{-- Opcional: destacar la primera noticia --}}
                        @php
                            $destacada = $noticias->first();
                            $resto = $noticias;
                        @endphp

                        {{-- Resto en grilla --}}
                        @if($resto->count())
                            <section class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
                                @foreach ($resto as $n)
                                    <a href="{{ route('noticia.show', $n->slug) }}" class="block">
                                        <article class="flex flex-col border rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow h-full">
                                            <div class="flex flex-col h-full">
                                                @if($n->imagen)
                                                    <img src="{{ asset('storage/' . $n->imagen) }}"
                                                         alt="{{ $n->titulo }}"
                                                         class="w-full h-44 object-cover">
                                                @endif

                                                <div class="p-4 flex-1 flex flex-col">
                                                    <div class="text-xs text-gray-500 mb-1">
                                                        {{ $n->fecha_publicacion?->format('d/m/Y') ?? '' }}
                                                    </div>
                                                    <h4 class="text-lg font-bold text-black mb-1 line-clamp-2">
                                                        {{ $n->titulo }}
                                                    </h4>
                                                    <p class="text-sm text-gray-700 text-justify mt-2 flex-1">
                                                        {{ Str::limit($n->resumen ?? strip_tags($n->cuerpo), 150) }}
                                                    </p>
                                                </div>
                                            </div>
                                        </article>
                                    </a>
                                @endforeach
                            </section>
                        @endif

                        <div class="mt-6">
                            {{ $noticias->links() }}
                        </div>
                    @else
                        <p class="mt-4 text-gray-600">No hay noticias publicadas aún.</p>
                    @endif
                </main>
            </div>
        </div>

        <!-- Footer -->
        <x-footer />
    </div>
</body>
</html>
