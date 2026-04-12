@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pindoor.cl </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (app()->environment('production'))
        <script type="text/javascript">
            (function(c,l,a,r,i,t,y){
                c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
                t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
                y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
            })(window, document, "clarity", "script", "wajfuymjy1");
        </script>
    @endif
</head>

<body class="bg-gray-100 text-gray-900 font-serif text-base">
    <div class="w-full mx-auto md:p-4">

        <x-navbar_labrujula />

        <div class="max-w-7xl mx-auto px-4">
            <section class="my-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-3">
                    <span class="text-red-400">P</span>i<span class="text-red-400">n</span>d<span
                        class="text-red-400">oo</span>r
                </h1>
                <p class="text-xl text-gray-700">
                    Descubre los mejores rincones y experiencias de Valparaíso 
                </p>
            </section>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border-t-4 border-[#fc5648]">
                <form id="filterForm" action="{{ route('atractivos.index') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Bùsqueda por categoría</label>
                            <select id="categoryFilter" name="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#fc5648] outline-none bg-gray-50">
                                <option value="">Todas las categorías</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->slug }}" @selected(request('category') == $cat->slug)>
                                        {{ $cat->icono }} {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Búsqueda por palabra 12312321321321312321323213221232</label>
                            <div class="flex">
                                <input type="text" id="searchFilter" name="search" value="{{ request('search') }}"
                                    placeholder="Ascensor, puerta de colores, café"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#fc5648] outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">¿Qué hay cerca de mí?
                                (GPS)</label>
                            <div class="flex">
                                <select name="rango" id="rango"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-[#fc5648] outline-none">
                                    <option value="100" @selected(request('rango') == 100)>100 mts</option>
                                    <option value="300" @selected(request('rango') == 300)>300 mts</option>
                                    <option value="500" @selected(request('rango') == 500)>500 mts</option>
                                    <option value="1000" @selected(request('rango') == 1000)>1 km</option>
                                    <option value="3000" @selected(request('rango') == 3000)>3 km</option>
                                    <option value="5000" @selected(request('rango') == 5000)>5 km</option>
                                    <option value="10000" @selected(request('rango') == 10000)>10 km</option>
                                </select>

                                <input type="hidden" id="lat" name="lat" value="{{ request('lat') }}">
                                <input type="hidden" id="lng" name="lng" value="{{ request('lng') }}">

                                <button type="button" id="btn-gps"
                                    class="bg-gray-800 text-white px-5 py-2 rounded-r-lg hover:bg-black transition flex items-center gap-2">
                                    📍 <span>GPS</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <p class="text-xs text-gray-400 italic">El Pionero de Valparaíso</p>
                        @if (request()->anyFilled(['category', 'search', 'lat']))
                            <a href="{{ route('atractivos.index') }}"
                                class="text-sm font-semibold text-gray-500 hover:text-[#fc5648] transition flex items-center gap-1 underline">
                                <span>✕</span> Borrar filtros
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="atractivos-container">
                @if ($atractivos->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($atractivos as $atractivo)
                            <article
                                class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col h-full">
                                <div class="relative">
                                    <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                                        class="block overflow-hidden">
                                        @if ($atractivo->imagenPrincipal)
                                            <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                                                alt="{{ $atractivo->title }}"
                                                class="w-full h-56 object-cover transform hover:scale-105 transition-transform duration-500" />
                                        @else
                                            <div
                                                class="w-full h-56 bg-gray-200 flex items-center justify-center text-4xl">
                                                📍</div>
                                        @endif
                                    </a>

                                    @if ($atractivo->categoria)
                                        <span
                                            class="absolute top-4 left-4 bg-[#fc5648] text-white text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full shadow-lg">
                                            {{ $atractivo->categoria->icono }} {{ $atractivo->categoria->nombre }}
                                        </span>
                                    @endif

                                    {{-- Badge oferta del día --}}
                                    @if ($atractivo->es_cliente && $atractivo->oferta_del_dia)
                                        <span class="absolute top-4 right-4 bg-amber-400 text-amber-900 text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full shadow-lg">
                                            Oferta hoy
                                        </span>
                                    @endif

                                    {{-- Logo del negocio --}}
                                    @if ($atractivo->es_cliente && $atractivo->imagen_perfil)
                                        <img src="{{ asset('storage/' . $atractivo->imagen_perfil) }}"
                                             alt="Logo {{ $atractivo->title }}"
                                             class="absolute bottom-4 right-4 w-12 h-12 rounded-xl object-cover border-2 border-white shadow-lg">
                                    @endif
                                </div>

                                <div class="p-5 flex-grow">
                                    @if (isset($atractivo->distancia))
                                        <div class="mb-2">
                                            <span
                                                class="bg-orange-100 text-[#fc5648] text-xs font-bold px-2 py-1 rounded border border-orange-200">
                                                A {{ number_format($atractivo->distancia / 1000, 2) }} km de ti
                                            </span>
                                        </div>
                                    @endif

                                    <h3 class="text-xl font-bold text-gray-900 mb-1 leading-tight">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $atractivo->lat }},{{ $atractivo->lng }}"
                                            target="_blank" rel="noopener"
                                            class="text-gray-400 hover:text-[#fc5648] mr-1">
                                            📍
                                        </a>
                                        <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                                            class="hover:text-[#fc5648] transition">
                                            {{ $atractivo->title }}
                                        </a>
                                    </h3>

                                    <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                        {{ Str::limit(strip_tags($atractivo->description), 130) }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-16 mb-12 flex justify-center">
                        {{ $atractivos->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm p-20 text-center border-2 border-dashed border-gray-200">
                        <div class="text-6xl mb-6">🕵️‍♂️</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Sin resultados</h3>
                        <p class="text-gray-500 mb-6">No encontramos lugares cercanos o que coincidan con tu búsqueda.
                        </p>
                        <a href="{{ route('atractivos.index') }}"
                            class="bg-[#fc5648] text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-900 transition">
                            Ver toda La Brújula
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- <x-footer /> --}}
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("🚀 La Brújula: Script cargado y listo.");

            const filterForm = document.getElementById('filterForm');
            const btnGps = document.getElementById('btn-gps');
            const inputLat = document.getElementById('lat');
            const inputLng = document.getElementById('lng');
            const categoryFilter = document.getElementById('categoryFilter');
            const searchFilter = document.getElementById('searchFilter');

            // Guardamos el valor inicial para comparar y evitar bucles
            const initialCategory = categoryFilter ? categoryFilter.value : '';

            // 1. Lógica del Botón GPS
            if (btnGps) {
                btnGps.addEventListener('click', function() {
                    console.log("🎯 Click en botón GPS");
                    btnGps.disabled = true;
                    btnGps.innerHTML = '⌛ Localizando...';

                    if (!navigator.geolocation) {
                        alert("Tu navegador no soporta geolocalización.");
                        resetBtn();
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            inputLat.value = position.coords.latitude;
                            inputLng.value = position.coords.longitude;
                            console.log("📍 Coordenadas obtenidas:", position.coords.latitude, position
                                .coords.longitude);
                            filterForm.submit();
                        },
                        function(error) {
                            console.error("❌ Error de geolocalización:", error);
                            alert("No pudimos obtener tu ubicación.");
                            resetBtn();
                        }, {
                            enableHighAccuracy: true,
                            timeout: 8000
                        }
                    );
                });
            }

            // 2. Lógica de Cambio de Categoría (CON LOGS Y FILTRO DE CAMBIO REAL)
            if (categoryFilter) {
                categoryFilter.addEventListener('change', function(e) {
                    const newValue = e.target.value;
                    console.log("📂 Evento 'change' detectado en Categoría.");
                    console.log("   - Origen confiable (isTrusted):", e.isTrusted);
                    console.log("   - Valor anterior:", initialCategory);
                    console.log("   - Valor nuevo:", newValue);
                    // Solo enviamos si el cambio es real y provocado por el usuario
                    if (e.isTrusted && newValue !== initialCategory) {
                        console.log("✅ Cambio válido. Eliminando input de búsqueda y enviando...");
                        if (searchFilter) searchFilter.parentNode.removeChild(searchFilter);
                        filterForm.submit();
                    } else {
                        console.warn("⚠️ Cambio ignorado (posible bucle o auto-relleno del navegador).");
                    }
                });
            }

            function resetBtn() {
                if (btnGps) {
                    btnGps.disabled = false;
                    btnGps.innerHTML = '📍 GPS';
                }
            }
        });
    </script>

</body>

</html>
