<!DOCTYPE html>
<html lang="es">

<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-7119582402031511"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cable a Tierra - El Pionero de Valparaíso</title>
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
            <div class="flex flex-col items-center text-center">
                <p class="text-[#eba81d] font-semibold mb-2 font-mono">Plaza O'Higgins - ¿Qué pasó con el pasto?</p>
                <a href="{{ route('pdf.track', ['pdfName' => 'Plaza_Ohiggins.pdf', 'action' => 'download']) }}"
                    target="_blank">
                    <img src="/storage/Ediciones/MiniEspecial.jpeg" alt="Plaza O'Higgins - ¿Qué pasó con el pasto?"
                        class="w-80 h-auto rounded shadow-md filter  hover:scale-105 transition duration-300">
                </a>
            </div>
            <!-- Contenido principal -->
            <main class="w-full space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 border-b pb-2 mb-6">Cable a Tierra</h2>

                    @if ($articulos->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach ($articulos as $articulo)
                                <a href="{{ url('cable-a-tierra/' . $articulo->slug) }}"
                                    class="flex flex-col border rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow">
                                    @if ($articulo->imagen)
                                        <div class="w-full h-48 overflow-hidden">
                                            <img src="{{ asset('storage/' . $articulo->imagen) }}"
                                                alt="{{ $articulo->titulo }}"
                                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-300" />
                                        </div>
                                    @endif
                                    <div class="p-4">
                                        <h3 class="text-lg font-bold text-black mb-2 hover:text-[#fc5648] transition-colors">
                                            {{ $articulo->titulo }}
                                        </h3>
                                        @if ($articulo->autor)
                                            <div class="text-sm text-[#fc5648] font-semibold mb-2">
                                                Por: {{ $articulo->autor }}
                                            </div>
                                        @endif
                                        <div class="text-sm text-gray-600 line-clamp-3">
                                            {{ Str::limit($articulo->resumen, 150) }}
                                        </div>
                                        @if ($articulo->fecha_publicacion)
                                            <div class="text-xs text-gray-500 mt-3">
                                                {{ \Carbon\Carbon::parse($articulo->fecha_publicacion)->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <!-- Paginación -->
                        <div class="mt-8">
                            {{ $articulos->links() }}
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-8">No hay artículos disponibles.</p>
                    @endif
                </div>
            </main>

            <!-- Footer -->
            <x-footer />
        </div>
    </div>
</body>

</html>
