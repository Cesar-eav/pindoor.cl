<!DOCTYPE html>
<html lang="es">

<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7119582402031511"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $entrevista->titulo }} - El Pionero de Valparaíso</title>
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
        <x-header />

        <div>
            <x-navbar />

            <!-- Layout principal -->
            <div class="flex flex-col md:flex-row gap-6 mt-5">
                <!-- Contenido principal -->
                <main class="w-full md:w-2/3 space-y-6">
                    <article class="bg-white border border-gray-300 rounded-lg p-6 shadow-sm">
                        <!-- Imagen destacada -->
                        @if ($entrevista->imagen || $entrevista->imagen_desktop)
                            <div class="mb-6 flex justify-center">
                                <!-- Imagen móvil -->
                                @if ($entrevista->imagen)
                                    <img src="{{ asset('storage/' . $entrevista->imagen) }}"
                                        alt="{{ $entrevista->titulo }}"
                                        class="w-full h-64 rounded-lg shadow-lg object-cover md:hidden" />
                                @endif

                                <!-- Imagen desktop -->
                                @if ($entrevista->imagen_desktop)
                                    <img src="{{ asset('storage/' . $entrevista->imagen_desktop) }}"
                                        alt="{{ $entrevista->titulo }}"
                                        class="hidden md:block max-w-full md:max-w-3xl h-auto rounded-lg shadow-lg object-cover" />
                                @elseif ($entrevista->imagen)
                                    <!-- Fallback: mostrar imagen móvil en desktop si no hay imagen_desktop -->
                                    <img src="{{ asset('storage/' . $entrevista->imagen) }}"
                                        alt="{{ $entrevista->titulo }}"
                                        class="hidden md:block w-full h-64 md:w-96 md:h-72 rounded-lg shadow-lg object-cover" />
                                @endif
                            </div>
                        @endif

                        <!-- Título y metadatos -->
                        <div class="mb-6">
                            <h1 class="text-3xl md:text-4xl font-bold text-black mb-4">
                                {{ $entrevista->titulo }}
                            </h1>

                            @if ($entrevista->entrevistado)
                                <div class="flex items-center gap-2 text-[#fc5648] font-semibold text-lg mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Entrevista a: {{ $entrevista->entrevistado }}</span>
                                </div>
                            @endif

                            @if ($entrevista->cargo)
                                <p class="text-gray-600 italic mb-3">
                                    {{ $entrevista->cargo }}
                                </p>
                            @endif

                            @if ($entrevista->fecha_publicacion)
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($entrevista->fecha_publicacion)->format('d \d\e F, Y') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Contenido de la entrevista -->
                        <div class="text-gray-800 text-base leading-relaxed prose prose-lg max-w-none">
                            {!! $entrevista->contenido !!}
                        </div>

                        <!-- Botón volver -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ url('entrevistas') }}"
                                class="inline-flex items-center gap-2 text-[#fc5648] hover:text-[#d94439] font-semibold transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Volver a Entrevistas
                            </a>
                        </div>
                    </article>
                    <div class="mt-6 bg-white border border-gray-300 rounded-lg p-6 shadow-sm">
                        <x-commenter::index :model="$entrevista" /> 
                    </div>
                </main>

                </main>

                <!-- Sidebar derecha -->
                <aside class="w-full md:w-1/3 space-y-6">
                    <!-- Otras entrevistas -->
                    @if ($otrasEntrevistas->isNotEmpty())
                        <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm">
                            <h3 class="text-xl font-semibold text-gray-800 border-b pb-2 mb-4">
                                Más Entrevistas
                            </h3>
                            <div class="space-y-4">
                                @foreach ($otrasEntrevistas as $otra)
                                    <a href="{{ url('entrevista/' . $otra->slug) }}"
                                        class="block hover:bg-gray-50 p-3 rounded transition-colors">
                                        <div class="flex gap-3">
                                            @if ($otra->imagen)
                                                <div class="flex-shrink-0">
                                                    <img src="{{ asset('storage/' . $otra->imagen) }}"
                                                        alt="{{ $otra->titulo }}"
                                                        class="w-20 h-20 object-cover rounded" />
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h4 class="text-sm font-bold text-black hover:text-[#fc5648] transition-colors line-clamp-2">
                                                    {{ $otra->titulo }}
                                                </h4>
                                                @if ($otra->entrevistado)
                                                    <p class="text-xs text-gray-600 mt-1">
                                                        {{ $otra->entrevistado }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Newsletter -->
                    <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">📬 Newsletter</h3>
                        <p class="text-sm text-gray-600 mb-4">
                            Suscríbete y recibe las últimas entrevistas y columnas de El Pionero.
                        </p>
                        <form method="POST" action="{{ route('newsletter.subscribe') }}" class="space-y-2">
                            @csrf
                            <input type="email" name="email" required
                                placeholder="Tu correo electrónico"
                                class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring focus:border-[#fc5648]" />
                            <button type="submit"
                                class="w-full bg-[#fc5648] text-white px-4 py-2 rounded hover:bg-[#d94439] transition-colors text-sm font-semibold">
                                Suscribirse
                            </button>
                        </form>
                    </div>
                </aside>
            </div>

            <!-- Footer -->
            <x-footer />
        </div>
    </div>
    @commenterScripts

</body>

</html>
