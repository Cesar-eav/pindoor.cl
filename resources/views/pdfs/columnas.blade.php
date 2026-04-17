<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>El Pionero de Valparaíso</title>
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
    <div class="w-full  mx-auto p-4">
        <!-- Encabezado -->
        <header class="text-center mb-1">
            <img src="{{ asset('storage/portada.png') }}" alt="Portada de Periódico"
                class="w-full  mx-auto mb-4 rounded shadow" />

        </header>
        <div>

            <x-navbar />

            <!-- Newsletter inteligente con Alpine.js -->
            <section x-data="{
                abierto: localStorage.getItem('newsletterOculto') !== 'true',
                successVisible: true
            }" x-show="abierto" x-transition
                class="relative bg-white border border-gray-300 shadow-md rounded-lg my-8 p-4 md:p-6 flex flex-col md:flex-row items-center justify-between gap-4 max-w-7xl mx-auto"
                x-init="@if (session('success')) setTimeout(() => successVisible = false, 4000); @endif">
                <!-- Botón cerrar -->
                <button
                    @click="
            abierto = false;
            localStorage.setItem('newsletterOculto', 'true');
        "
                    class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-xl font-bold">
                    &times;
                </button>

                <!-- Título y texto -->
                <div class="text-center md:text-left">
                    <h2 class="text-xl font-bold text-gray-800">📬 Newsletter</h2>
                    <p class="text-gray-600 text-sm">Suscríbete y recibe las columnas más recientes de El Pionero en tu
                        correo.</p>
                </div>

                <!-- Formulario -->
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

                <!-- Mensaje de éxito -->
                @if (session('success'))
                    <div x-show="successVisible" x-transition
                        class="absolute bottom-[-1.5rem] left-4 text-green-600 text-sm mt-2 font-semibold">
                        {{ session('success') }}
                    </div>
                @endif
            </section>



            <!-- Layout principal -->
            <div class="flex flex-col md:flex-row gap-6">


                <!-- Contenido principal -->

                <main class="w-full space-y-6 bg-gray-50 border border-gray-300 rounded-lg p-4 shadow-sm">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Columnas de Opinión</h2>

                        @if ($columnas->isNotEmpty())
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                                @foreach ($columnas as $articulo)
                                    <div
                                        class="flex flex-col border rounded-lg overflow-hidden bg-white shadow hover:shadow-lg transition-shadow">
                                        <a href="{{ url('articulo/' . $articulo->slug) }}" class="flex flex-row h-full">

                                            <!-- Texto -->
                                            <div class="flex flex-col justify-center p-4 w-2/3">
                                                <div
                                                    class="md:text-xl text-sm  text-black flex items-center md:mb-5 mb-2">
                                                    {{ $articulo->revista->titulo }}
                                                </div>
                                                <h4 class="text-lg font-bold text-black mb-2">
                                                    {{ $articulo->titulo }}
                                                </h4>
                                                @if ($articulo->columnista)
                                                    <div class="text-sm italic text-gray-600 flex items-center">
                                                        {{ $articulo->columnista->nombre }}
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Imagen a la derecha -->
                                            @if ($articulo->columnista && $articulo->columnista->foto)
                                                <div class="w-1/3 flex items-center justify-center bg-gray-100">
                                                    <img src="{{ asset('storage/' . $articulo->columnista->foto) }}"
                                                        alt="{{ $articulo->columnista->nombre }}"
                                                        class="w-full h-full object-cover" />
                                                </div>
                                            @endif

                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No hay artículos en esta revista.</p>
                        @endif
                    </div>


                </main>
            </div>



            <x-footer />



            <!-- Pie de página -->
            <footer class="text-center text-sm text-gray-600 mt-10 pt-4 border-t border-gray-300">

            </footer>


            <div class="fixed bottom-2 right-2 text-xs text-gray-400">1</div>
        </div>
</body>

</html>
