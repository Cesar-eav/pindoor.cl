<!DOCTYPE html>
<html lang="es">

<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7119582402031511"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $editorial->titulo }} - El Pionero de Valparaíso</title>
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
                        <!-- Título y metadatos -->
                        <div class="mb-6">
                            <h1 class="text-3xl md:text-4xl font-bold text-black mb-4">
                                {{ $editorial->titulo }}
                            </h1>

                            @if ($editorial->revista)
                                <div class="flex items-center gap-2 text-[#fc5648] font-semibold text-lg mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span>{{ $editorial->revista->titulo }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Contenido de la editorial -->
                        {{-- ql-editor --}}
                        <div class="text-gray-800 text-base leading-relaxed prose prose-lg max-w-none">
                            {!! $editorial->contenido !!}
                        </div>

                        <!-- Botón volver -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ url('editoriales') }}"
                                class="inline-flex items-center gap-2 text-[#fc5648] hover:text-[#d94439] font-semibold transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Volver a Editoriales

                            </a>
                        </div>
                    </article>
            <div class="mt-6 bg-white border border-gray-300 rounded-lg p-6 shadow-sm">
                        <x-commenter::index :model="$editorial" /> 
                    </div>
                </main>

                <!-- Sidebar derecha -->
                <aside class="w-full md:w-1/3 space-y-6">
                    <!-- Otras editoriales -->
                    @if ($otrasEditoriales->isNotEmpty())
                        <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm">
                            <h3 class="text-xl font-semibold text-gray-800 border-b pb-1 mb-1">
                                Más Editoriales
                            </h3>
                            <div class="space-y-0">
                                @foreach ($otrasEditoriales as $otra)
                                    <a href="{{ url('editorial/' . $otra->slug) }}"
                                        class="block hover:bg-gray-50 p-3 rounded transition-colors">
                                        <div>
                                            @if ($otra->revista)
                                                <p class="text-xs text-[#fc5648] font-semibold mb-1">
                                                    {{ $otra->revista->titulo }}
                                                </p>
                                            @endif
                                            <h4 class="text-sm font-bold text-black hover:text-[#fc5648] transition-colors line-clamp-2">
                                                {{ $otra->titulo }}
                                            </h4>
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
                            Suscríbete y recibe las últimas editoriales y columnas de El Pionero.
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
