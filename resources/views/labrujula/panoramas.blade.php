@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>La Brújula - Panoramas</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (app()->environment('production'))
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "vul5oo31fn");
    </script>
    @endif
</head>

<body class="bg-gray-100 text-gray-900 font-serif text-base">
    <div class="w-full mx-auto md:p-4">
        <x-navbar_labrujula />


        <div class="max-w-7xl mx-auto px-4">
            <section class="my-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-3">
                    🧭 Panoramas
                </h1>
                <p class="text-xl text-gray-700">
                    Descubre los mejores rincones y experiencias de Valparaíso
                </p>
            </section>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border-t-4 border-[#fc5648]">
                <img src="{{ asset('storage/atractivos/panoramas/1.jpg') }}" alt="Panorama 1" class="w-full h-70 object-cover">
                
                <div class="flex flex-col gap-2 mt-2">
                <span class="text-gray-600 text-lg font-bold">Taller de ciencia ciudadana.Día de la astronomía. </span>
                <span class="text-gray-600 text-md">Ubicación: Teatro municipal de Viña del Mar.</span>
                <span class="text-gray-600 text-md">Fecha: 19-03-2026</span>
                <span class="text-gray-600 text-md">Hora: 15:30.</span>
                </div>

            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border-t-4 border-[#fc5648]">
                <img src="{{ asset('storage/atractivos/panoramas/2.jpg') }}" alt="Panorama 1" class="w-full h-70 object-cover">
                                <div class="flex flex-col gap-2 mt-2">
                <span class="text-gray-600 text-lg font-bold">Cartelera semanal</span>
                <span class="text-gray-600 text-md">Ubicación: Teatro Insomnia</span>
                </div>
            </div>
                        <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border-t-4 border-[#fc5648]">
                <img src="{{ asset('storage/atractivos/panoramas/3.jpg') }}" alt="Panorama 1" class="w-full h-70 object-cover">
                                <div class="flex flex-col gap-2 mt-2">
                <span class="text-gray-600 text-lg font-bold">Programación semanal</span>
                <span class="text-gray-600 text-md">Ubicación: Parque Cultrual de Valparaíso</span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border-t-4 border-[#fc5648]">
                <img src="{{ asset('storage/atractivos/panoramas/4.jpg') }}" alt="Panorama 1" class="w-full h-70 object-cover">
                <div class="flex flex-col gap-2 mt-2">
                    <span class="text-gray-600 text-lg font-bold">Cartelera Mensual</span>
                    <span class="text-gray-600 text-md">Ubicación: Museo Baburizza</span>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border-t-4 border-[#fc5648] h-70">
                <img src="{{ asset('storage/atractivos/panoramas/5.jpg') }}" alt="Panorama 1" class="w-full  object-cover">
                <div class="flex flex-col gap-2 mt-2">
                <span class="text-gray-600 text-lg font-bold">Luz que permanece</span>
                <span class="text-gray-600 text-md">Ubicación: Cerro Ñielol</span>
                <span class="text-gray-600 text-md">Fecha: 07-03-2026</span>
                <span class="text-gray-600 text-md">Hora: 11:00 a.m.</span>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border-t-4 border-[#fc5648]">
                <img src="{{ asset('storage/atractivos/panoramas/6.jpg') }}" alt="Panorama 2" class="w-full h-70 object-cover">     
                <div class="flex flex-col gap-2 mt-2">
                <span class="text-gray-600 text-lg font-bold">Sesión de Vinillos</span>
                <span class="text-gray-600 text-md">Ubicación: Fisher 18, Escalera de Colores, Cerro Concepción</span>
                <span class="text-gray-600 text-md">Fecha: 14-03-2026</span>
                <span class="text-gray-600 text-md">Hora: 19:00</span>
                </div>

            </div>

<div class=""></div>

        </div>


        </div>

        {{-- <x-footer /> --}}
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterForm');
        const btnGps = document.getElementById('btn-gps');
        const inputLat = document.getElementById('lat');
        const inputLng = document.getElementById('lng');

        // Lógica del botón GPS
        if (btnGps) {
            btnGps.addEventListener('click', function() {
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
                        
                        console.log("📍 Ubicación obtenida:", position.coords.latitude, position.coords.longitude);
                        
                        // Enviamos el formulario principal
                        filterForm.submit();
                    },
                    function(error) {
                        console.error("Error GPS:", error);
                        alert("No pudimos obtener tu ubicación.");
                        resetBtn();
                    },
                    { enableHighAccuracy: true, timeout: 8000 }
                );
            });
        }

        function resetBtn() {
            btnGps.disabled = false;
            btnGps.innerHTML = '📍 GPS';
        }

        // Listener para cambio de categoría (envío automático)
        document.getElementById('categoryFilter').addEventListener('change', function() {
            filterForm.submit();
        });
    });
    </script>

    {{-- Consola Debug --}}
    @if(request()->filled(['lat', 'lng']))
        <script>
            const listaAtractivos = @json($atractivos->items());
            console.log("🗺️ Resultados de la búsqueda espacial:");
            if(listaAtractivos.length > 0) {
                console.table(listaAtractivos.map(i => ({
                    Nombre: i.title,
                    Distancia: i.distancia ? (i.distancia / 1000).toFixed(2) + ' km' : 'Original'
                })));
            } else {
                console.log("Cero resultados en el radio seleccionado.");
            }
        </script>
    @endif
</body>
</html>