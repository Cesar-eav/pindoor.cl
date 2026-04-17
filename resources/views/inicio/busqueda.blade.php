<!DOCTYPE html>
<html lang="es">

<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7119582402031511"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Resultados de búsqueda: {{ $query }} - El Pionero de Valparaíso</title>
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
    <div class="w-full mx-auto md:p-4">
        <x-header />

        <div>
            <x-navbar />

            <!-- Resultados de búsqueda -->
            <div class="max-w-7xl mx-auto mt-8">
                <!-- Título y filtros -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold mb-2">Resultados de búsqueda</h1>
                    <p class="text-gray-600">
                        Se encontraron <span class="font-bold">{{ $totalResultados }}</span> resultados para:
                        <span class="font-bold text-[#fc5648]">"{{ $query }}"</span>
                    </p>

                    <!-- Filtros por tipo -->
                    <div class="flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('buscar', ['q' => $query, 'tipo' => 'todos']) }}"
                           class="px-4 py-2 rounded {{ $tipo === 'todos' ? 'bg-[#fc5648] text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }} border transition-colors">
                            Todos
                        </a>
                        <a href="{{ route('buscar', ['q' => $query, 'tipo' => 'columnas']) }}"
                           class="px-4 py-2 rounded {{ $tipo === 'columnas' ? 'bg-[#fc5648] text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }} border transition-colors">
                            Columnas ({{ $resultados['columnas']->count() ?? 0 }})
                        </a>
                        <a href="{{ route('buscar', ['q' => $query, 'tipo' => 'editoriales']) }}"
                           class="px-4 py-2 rounded {{ $tipo === 'editoriales' ? 'bg-[#fc5648] text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }} border transition-colors">
                            Editoriales ({{ $resultados['editoriales']->count() ?? 0 }})
                        </a>
                        <a href="{{ route('buscar', ['q' => $query, 'tipo' => 'noticias']) }}"
                           class="px-4 py-2 rounded {{ $tipo === 'noticias' ? 'bg-[#fc5648] text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }} border transition-colors">
                            Noticias ({{ $resultados['noticias']->count() ?? 0 }})
                        </a>
                        <a href="{{ route('buscar', ['q' => $query, 'tipo' => 'entrevistas']) }}"
                           class="px-4 py-2 rounded {{ $tipo === 'entrevistas' ? 'bg-[#fc5648] text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }} border transition-colors">
                            Entrevistas ({{ $resultados['entrevistas']->count() ?? 0 }})
                        </a>
                        <a href="{{ route('buscar', ['q' => $query, 'tipo' => 'cable-a-tierra']) }}"
                           class="px-4 py-2 rounded {{ $tipo === 'cable-a-tierra' ? 'bg-[#fc5648] text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }} border transition-colors">
                            Cable a Tierra ({{ $resultados['cable_a_tierra']->count() ?? 0 }})
                        </a>
                    </div>
                </div>

                @if($totalResultados === 0)
                    <!-- Sin resultados -->
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-700 mb-2">No se encontraron resultados</h2>
                        <p class="text-gray-600">Intenta con otros términos de búsqueda</p>
                    </div>
                @else
                    <!-- Resultados de Columnas -->
                    @if(isset($resultados['columnas']) && $resultados['columnas']->count() > 0)
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4 flex items-center">
                                <span class="bg-[#fc5648] text-white px-3 py-1 rounded mr-2">Columnas</span>
                                <span class="text-gray-600 text-lg">{{ $resultados['columnas']->count() }}</span>
                            </h2>
                            <div class="space-y-4">
                                @foreach($resultados['columnas'] as $columna)
                                    <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
                                        <a href="{{ route('inicio.articulo', $columna->slug) }}" class="block">
                                            <h3 class="text-xl font-bold text-gray-900 hover:text-[#fc5648] mb-2">{{ $columna->titulo }}</h3>
                                            <p class="text-gray-600 text-sm mb-2">
                                                Por {{ $columna->columnista->nombre }} | {{ $columna->revista->titulo }}
                                            </p>
                                            <p class="text-gray-700 line-clamp-2">{{ strip_tags(substr($columna->contenido, 0, 200)) }}...</p>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Resultados de Editoriales -->
                    @if(isset($resultados['editoriales']) && $resultados['editoriales']->count() > 0)
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4 flex items-center">
                                <span class="bg-[#fc5648] text-white px-3 py-1 rounded mr-2">Editoriales</span>
                                <span class="text-gray-600 text-lg">{{ $resultados['editoriales']->count() }}</span>
                            </h2>
                            <div class="space-y-4">
                                @foreach($resultados['editoriales'] as $editorial)
                                    <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
                                        <a href="{{ route('editorial.show', $editorial->slug) }}" class="block">
                                            <h3 class="text-xl font-bold text-gray-900 hover:text-[#fc5648] mb-2">{{ $editorial->titulo }}</h3>
                                            <p class="text-gray-600 text-sm mb-2">{{ $editorial->revista->titulo }}</p>
                                            <p class="text-gray-700 line-clamp-2">{{ strip_tags(substr($editorial->contenido, 0, 200)) }}...</p>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Resultados de Noticias -->
                    @if(isset($resultados['noticias']) && $resultados['noticias']->count() > 0)
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4 flex items-center">
                                <span class="bg-[#fc5648] text-white px-3 py-1 rounded mr-2">Noticias</span>
                                <span class="text-gray-600 text-lg">{{ $resultados['noticias']->count() }}</span>
                            </h2>
                            <div class="space-y-4">
                                @foreach($resultados['noticias'] as $noticia)
                                    <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
                                        <div class="block">
                                            <h3 class="text-xl font-bold text-gray-900 hover:text-[#fc5648] mb-2">{{ $noticia->titulo }}</h3>
                                            <p class="text-gray-600 text-sm mb-2">{{ $noticia->fecha_publicacion->format('d/m/Y') }}</p>
                                            <p class="text-gray-700 line-clamp-2">{{ $noticia->resumen }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Resultados de Entrevistas -->
                    @if(isset($resultados['entrevistas']) && $resultados['entrevistas']->count() > 0)
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4 flex items-center">
                                <span class="bg-[#fc5648] text-white px-3 py-1 rounded mr-2">Entrevistas</span>
                                <span class="text-gray-600 text-lg">{{ $resultados['entrevistas']->count() }}</span>
                            </h2>
                            <div class="space-y-4">
                                @foreach($resultados['entrevistas'] as $entrevista)
                                    <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
                                        <a href="{{ route('entrevista.show', $entrevista->slug) }}" class="block">
                                            <h3 class="text-xl font-bold text-gray-900 hover:text-[#fc5648] mb-2">{{ $entrevista->titulo }}</h3>
                                            <p class="text-gray-600 text-sm mb-2">{{ $entrevista->entrevistado }} - {{ $entrevista->cargo }}</p>
                                            <p class="text-gray-700 line-clamp-2">{{ strip_tags(substr($entrevista->contenido, 0, 200)) }}...</p>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Resultados de Cable a Tierra -->
                    @if(isset($resultados['cable_a_tierra']) && $resultados['cable_a_tierra']->count() > 0)
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4 flex items-center">
                                <span class="bg-[#fc5648] text-white px-3 py-1 rounded mr-2">Cable a Tierra</span>
                                <span class="text-gray-600 text-lg">{{ $resultados['cable_a_tierra']->count() }}</span>
                            </h2>
                            <div class="space-y-4">
                                @foreach($resultados['cable_a_tierra'] as $articulo)
                                    <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow">
                                        <a href="{{ route('cable-a-tierra.show', $articulo->slug) }}" class="block">
                                            <h3 class="text-xl font-bold text-gray-900 hover:text-[#fc5648] mb-2">{{ $articulo->titulo }}</h3>
                                            @if($articulo->autor)
                                                <p class="text-gray-600 text-sm mb-2">Por: {{ $articulo->autor }}</p>
                                            @endif
                                            <p class="text-gray-700 line-clamp-2">{{ $articulo->resumen }}</p>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>

            <x-footer />
        </div>
    </div>
</body>

</html>
