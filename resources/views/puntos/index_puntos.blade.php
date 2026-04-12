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

    {{-- Leaflet --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        #mapa-principal { height: 70vh; border-radius: 1rem; z-index: 1; }
        .leaflet-popup-content-wrapper { border-radius: 0.75rem; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
        .leaflet-popup-content { margin: 0; padding: 0; width: 220px !important; }
        .pin-marker { background: #fc5648; border: 3px solid white; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); box-shadow: 0 2px 8px rgba(0,0,0,.3); }
        .pin-marker.cliente { background: #f59e0b; }
    </style>

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
                    <span class="text-red-400">P</span>i<span class="text-red-400">n</span>d<span
                        class="text-red-400">oo</span>r
                </h1>
                <p class="text-xl text-gray-700">
                    Descubre los mejores rincones y experiencias de Valparaíso
                </p>
            </section>

            {{-- Toggle Listado / Mapa --}}
            <div class="flex justify-center mb-6">
                <div class="inline-flex bg-gray-200 p-1 rounded-xl gap-1">
                    <button id="btn-listado"
                            onclick="setView('listado')"
                            class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all bg-white shadow text-[#fc5648]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Listado
                    </button>
                    <button id="btn-mapa"
                            onclick="setView('mapa')"
                            class="flex items-center gap-2 px-5 py-2 rounded-lg text-sm font-bold transition-all text-gray-500 hover:text-gray-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Mapa
                    </button>
                </div>
            </div>

            {{-- Vista Mapa --}}
            <div id="vista-mapa" class="hidden mb-8">
                <div id="mapa-principal"></div>
                <p class="text-xs text-gray-400 text-center mt-2">
                    {{ count($puntosMapData) }} puntos en el mapa · Haz clic en un marcador para ver el detalle
                </p>
            </div>

            <div id="vista-listado">
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
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Búsqueda por palabra</label>
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
                                            {{ $atractivo->categoria->nombre }}
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
            </div>{{-- /vista-listado --}}
        </div>

        {{-- <x-footer /> --}}
    </div>


    <script>
        // ── Datos del mapa ──────────────────────────────────────────────────────
        const PUNTOS_DATA = @json($puntosMapData);

        // ── Toggle Listado / Mapa ───────────────────────────────────────────────
        let mapaIniciado = false;
        let mapaLeaflet  = null;

        function setView(vista) {
            const elListado = document.getElementById('vista-listado');
            const elMapa    = document.getElementById('vista-mapa');
            const btnL      = document.getElementById('btn-listado');
            const btnM      = document.getElementById('btn-mapa');

            if (vista === 'mapa') {
                elListado.classList.add('hidden');
                elMapa.classList.remove('hidden');
                btnM.classList.add('bg-white', 'shadow', 'text-[#fc5648]');
                btnM.classList.remove('text-gray-500', 'hover:text-gray-700');
                btnL.classList.remove('bg-white', 'shadow', 'text-[#fc5648]');
                btnL.classList.add('text-gray-500', 'hover:text-gray-700');
                if (!mapaIniciado) iniciarMapa();
            } else {
                elMapa.classList.add('hidden');
                elListado.classList.remove('hidden');
                btnL.classList.add('bg-white', 'shadow', 'text-[#fc5648]');
                btnL.classList.remove('text-gray-500', 'hover:text-gray-700');
                btnM.classList.remove('bg-white', 'shadow', 'text-[#fc5648]');
                btnM.classList.add('text-gray-500', 'hover:text-gray-700');
            }
        }

        // ── Inicializar Leaflet ─────────────────────────────────────────────────
        function iniciarMapa() {
            mapaIniciado = true;

            mapaLeaflet = L.map('mapa-principal', {
                center: [-33.047, -71.612],   // Valparaíso
                zoom: 14,
                zoomControl: true,
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(mapaLeaflet);

            // Icono personalizado
            function crearIcono(esCliente) {
                return L.divIcon({
                    className: '',
                    html: `<div style="
                        width:14px; height:14px;
                        background:${esCliente ? '#f59e0b' : '#fc5648'};
                        border:2.5px solid white;
                        border-radius:50%;
                        box-shadow:0 1px 6px rgba(0,0,0,.35);
                    "></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7],
                    popupAnchor: [0, -10],
                });
            }

            const bounds = [];

            PUNTOS_DATA.forEach(function(p) {
                if (!p.lat || !p.lng) return;

                bounds.push([p.lat, p.lng]);

                const marker = L.marker([p.lat, p.lng], { icon: crearIcono(p.es_cliente) });

                const imgHtml = p.imagen
                    ? `<img src="${p.imagen}" alt="${p.title}" style="width:100%;height:110px;object-fit:cover;border-radius:0.5rem 0.5rem 0 0;">`
                    : `<div style="width:100%;height:60px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border-radius:0.5rem 0.5rem 0 0;">📍</div>`;

                const catBadge = p.categoria
                    ? `<span style="background:#fc5648;color:white;font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;padding:2px 7px;border-radius:999px;">${p.categoria}</span>`
                    : '';

                const clienteBadge = p.es_cliente
                    ? `<span style="background:#fef3c7;color:#92400e;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin-left:4px;">Negocio</span>`
                    : '';

                const popup = `
                    <div style="font-family:sans-serif;">
                        ${imgHtml}
                        <div style="padding:10px 12px 12px;">
                            <div style="margin-bottom:5px;">${catBadge}${clienteBadge}</div>
                            <div style="font-weight:700;font-size:13px;line-height:1.3;margin-bottom:3px;">${p.title}</div>
                            ${p.sector ? `<div style="font-size:11px;color:#6b7280;margin-bottom:8px;">📍 ${p.sector}</div>` : ''}
                            <a href="/lugar/${p.slug}" style="display:inline-block;background:#fc5648;color:white;font-size:11px;font-weight:700;padding:5px 12px;border-radius:8px;text-decoration:none;">
                                Ver detalle →
                            </a>
                        </div>
                    </div>`;

                marker.bindPopup(popup, { maxWidth: 230 }).addTo(mapaLeaflet);
            });

            if (bounds.length > 1) {
                mapaLeaflet.fitBounds(bounds, { padding: [40, 40] });
            }
        }

        // ── Lógica existente ────────────────────────────────────────────────────
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
