<!DOCTYPE html>
<html lang="es">

<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7119582402031511"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Revistas - El Pionero de Valparaíso</title>
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
    <div class="w-full mx-auto p-4">
        <x-header />

        <div>
            <x-navbar />

            <!-- Newsletter -->
            <section x-data="{
                abierto: localStorage.getItem('newsletterOculto') !== 'true',
                successVisible: true
            }" x-show="abierto" x-transition
                class="relative bg-white border border-gray-300 shadow-md rounded-lg my-8 p-4 md:p-6 flex flex-col md:flex-row items-center justify-between gap-4 max-w-7xl mx-auto"
                x-init="@if (session('success')) setTimeout(() => successVisible = false, 4000); @endif">
                <button
                    @click="abierto = false; localStorage.setItem('newsletterOculto', 'true');"
                    class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">
                    &times;
                </button>

                <div class="text-center md:text-left">
                    <h2 class="text-xl font-bold text-gray-800">📬 Newsletter</h2>
                    <p class="text-gray-600 text-sm">Suscríbete y recibe las columnas más recientes de El Pionero en tu
                        correo.</p>
                </div>

                <form method="POST" action="{{ route('newsletter.subscribe') }}"
                    class="flex flex-col sm:flex-row items-center gap-2 w-full md:w-auto">
                    @csrf
                    <input type="email" name="email" required placeholder="Tu correo electrónico"
                        class="w-full sm:w-72 px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-[#fc5648]" />
                    <button type="submit"
                        class="bg-[#fc5648] text-white px-5 py-2 rounded hover:bg-[#d94439] transition-colors">
                        Suscribirse
                    </button>
                </form>

                @if (session('success'))
                    <div x-show="successVisible" x-transition
                        class="absolute bottom-[-1.5rem] left-4 text-green-600 text-sm mt-2 font-semibold">
                        {{ session('success') }}
                    </div>
                @endif
            </section>

            <!-- Contenido principal -->
            <main class="w-full space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Revistas Publicadas</h2>

                    @if ($revistas->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            @foreach ($revistas as $revista)
                                <article
                                    class="flex flex-col border rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow">
                                    @if ($revista->portada)
                                        <div class="w-full aspect-[3/4] overflow-hidden bg-gray-100">
                                            <img src="{{ asset('storage/' . $revista->portada) }}"
                                                alt="Portada {{ $revista->titulo }}"
                                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                                        </div>
                                    @else
                                        <div class="w-full aspect-[3/4] bg-gradient-to-br from-[#fc5648] to-[#eba81d] flex items-center justify-center">
                                            <span class="text-white text-4xl font-bold">📰</span>
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-lg font-bold text-black mb-2">
                                            {{ $revista->titulo }}
                                        </h3>
                                        @if ($revista->fecha_publicacion)
                                            <div class="text-sm text-[#fc5648] font-semibold mb-2">
                                                {{ \Carbon\Carbon::parse($revista->fecha_publicacion)->format('F Y') }}
                                            </div>
                                        @endif
                                        @if ($revista->descripcion)
                                            <div class="text-sm text-gray-600 line-clamp-2 mb-3">
                                                {{ $revista->descripcion }}
                                            </div>
                                        @endif
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <span>{{ $revista->articulos_count ?? 0 }} artículos</span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <!-- Paginación -->
                        <div class="mt-8">
                            {{ $revistas->links() }}
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-8">No hay revistas disponibles.</p>
                    @endif
                </div>
            </main>

            <!-- Footer -->
            <x-footer />
        </div>
    </div>
</body>

</html>
