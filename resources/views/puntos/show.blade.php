@php use Illuminate\Support\Str; @endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $punto->title }} — Pindoor.cl</title>
    <meta name="description" content="{{ Str::limit(strip_tags($punto->description), 160) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Lora:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .serif-text { font-family: 'Lora', serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-[#f9fafb] text-gray-900 leading-relaxed">
    <div class="w-full mx-auto">
        <x-navbar_labrujula />

        {{-- Componente principal Alpine --}}
        <main
            x-data="{ vista: 'contenido' }"
            class="max-w-7xl mx-auto px-4 py-8 md:py-12"
        >
            {{-- Breadcrumbs --}}
            <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-8 overflow-x-auto whitespace-nowrap">
                <a href="{{ route('atractivos.index') }}" class="hover:text-pindoor-accent transition">Pindoor</a>
                <span class="text-gray-300">/</span>
                @if($punto->categoria)
                    <span class="text-gray-500">{{ $punto->categoria->nombre }}</span>
                    <span class="text-gray-300">/</span>
                @endif
                <span class="text-pindoor-accent">{{ $punto->title }}</span>
            </nav>

            {{-- Tabs móvil (solo si tiene carta) --}}
            @if($punto->tieneCarta())
            <div class="flex lg:hidden gap-2 mb-6">
                <button
                    @click="vista = 'contenido'"
                    :class="vista === 'contenido' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-1 py-2.5 rounded-xl text-sm font-bold transition">
                    Descripción
                </button>
                <button
                    @click="vista = 'carta'"
                    :class="vista === 'carta' ? 'bg-pindoor-accent text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-1 py-2.5 rounded-xl text-sm font-bold transition">
                    Ver carta
                </button>
            </div>
            @endif

            {{-- Grid 3 columnas --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- COLUMNA IZQUIERDA: navegación (solo escritorio) --}}
                <aside class="hidden lg:flex lg:col-span-2 flex-col gap-3 pt-2">

                    <button
                        @click="vista = 'contenido'"
                        :class="vista === 'contenido'
                            ? 'bg-gray-900 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>📖</span> Descripción
                    </button>

                    @if($punto->tieneCarta())
                    <button
                        @click="vista = 'carta'"
                        :class="vista === 'carta'
                            ? 'bg-pindoor-accent text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-pindoor-accent'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🍽️</span> Ver carta
                    </button>
                    @endif

                    @if($punto->lat && $punto->lng)
                    <a href="https://www.google.com/maps?q={{ $punto->lat }},{{ $punto->lng }}"
                       target="_blank" rel="noopener"
                       class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2 bg-white text-gray-600 border border-gray-200 hover:border-gray-400">
                        <span>📍</span> Ir al mapa
                    </a>
                    @endif
                </aside>

                {{-- COLUMNA CENTRAL: contenido dinámico --}}
                <div class="lg:col-span-6">

                    {{-- PANEL: Contenido principal --}}
                    <div x-show="vista === 'contenido'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

                        {{-- Galería --}}
                        @php
                            $imagenes = $punto->imagenes->sortBy('orden');
                            $principal = $imagenes->firstWhere('es_principal', true) ?? $imagenes->first();
                        @endphp

                        <div class="mb-10">
                            @if($imagenes->count())
                                <div x-data="{ active: '{{ $principal ? asset('storage/' . $principal->ruta) : '' }}' }">
                                    <div class="aspect-[16/10] md:aspect-[16/9] rounded-3xl overflow-hidden shadow-2xl shadow-gray-200 mb-4">
                                        <img :src="active" alt="{{ $punto->title }}"
                                             class="w-full h-full object-cover transition duration-500" />
                                    </div>
                                    @if($imagenes->count() > 1)
                                        <div class="flex gap-3 overflow-x-auto pb-2 snap-x">
                                            @foreach($imagenes as $img)
                                                @php $url = asset('storage/' . $img->ruta) @endphp
                                                <div @click="active = '{{ $url }}'"
                                                     class="flex-none w-20 h-20 md:w-24 md:h-24 snap-start rounded-2xl overflow-hidden border-2 transition cursor-pointer"
                                                     :class="active === '{{ $url }}' ? 'border-pindoor-accent' : 'border-transparent hover:border-gray-300'">
                                                    <img src="{{ $url }}" alt="{{ $punto->title }}"
                                                         class="w-full h-full object-cover" />
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="aspect-video bg-gray-100 rounded-3xl flex items-center justify-center text-6xl shadow-inner italic text-gray-300">
                                    📍 No hay fotos aún
                                </div>
                            @endif
                        </div>

                        {{-- Texto --}}
                        <section class="space-y-6">
                            <div class="space-y-2">
                                @if($punto->categoria)
                                    <span class="inline-flex items-center gap-2 bg-pindoor-accent/10 text-pindoor-accent text-[11px] uppercase font-black px-4 py-1.5 rounded-full">
                                        <span class="text-base">{{ $punto->categoria->icono }}</span>
                                        {{ $punto->categoria->nombre }}
                                    </span>
                                @endif

                                <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 tracking-tight leading-[1.1]">
                                    {{ $punto->title }}
                                </h1>

                                @if($punto->autor)
                                    <div class="flex items-center gap-2 text-gray-500 font-medium italic">
                                        <span class="w-8 h-[1px] bg-gray-300"></span>
                                        <span>Obra de {{ $punto->autor }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="serif-text text-lg text-gray-700 leading-relaxed space-y-4">
                                {!! nl2br(e($punto->description)) !!}
                            </div>

                            @if($punto->tags && count($punto->tags))
                                <div class="flex flex-wrap gap-2 pt-4">
                                    @foreach($punto->tags as $tag)
                                        <span class="bg-white text-gray-500 text-[11px] font-bold uppercase tracking-wider px-4 py-2 rounded-xl border border-gray-100 shadow-sm">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Video YouTube --}}
                            @if($punto->video_url)
                                @php
                                    $videoId = null;
                                    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $punto->video_url, $m)) {
                                        $videoId = $m[1];
                                    }
                                @endphp
                                @if($videoId)
                                    <div class="pt-6">
                                        <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Video</h3>
                                        <div class="aspect-video rounded-2xl overflow-hidden shadow-lg">
                                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                                    class="w-full h-full" allowfullscreen loading="lazy"></iframe>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </section>
                    </div>

                    {{-- PANEL: Carta / Menú --}}
                    @if($punto->tieneCarta())
                    <div x-show="vista === 'carta'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                @if($punto->imagen_perfil)
                                    <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                         alt="Logo {{ $punto->title }}"
                                         class="w-12 h-12 rounded-xl object-cover border border-gray-100 inline-block mr-3">
                                @endif
                                <span class="text-2xl font-extrabold text-gray-900">Carta</span>
                            </div>

                            @if($punto->carta_pdf)
                                <a href="{{ asset('storage/' . $punto->carta_pdf) }}"
                                   target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-bold rounded-xl hover:bg-pindoor-accent transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Descargar PDF
                                </a>
                            @endif
                        </div>

                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                            <div class="serif-text text-gray-700 leading-relaxed whitespace-pre-line text-base">
                                {{ $punto->carta }}
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- COLUMNA DERECHA: sidebar --}}
                <aside class="lg:col-span-4">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/50 border border-white sticky top-8">

                        {{-- Logo del negocio (solo si es cliente) --}}
                        @if($punto->es_cliente && $punto->imagen_perfil)
                            <div class="flex items-center gap-3 mb-6 pb-6 border-b border-gray-100">
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-14 h-14 rounded-2xl object-cover border border-gray-100 shrink-0">
                                <div>
                                    <p class="font-bold text-gray-900 leading-tight">{{ $punto->title }}</p>
                                    <p class="text-xs text-gray-400">{{ $punto->categoria?->nombre }}</p>
                                </div>
                            </div>
                        @endif

                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 mb-6">Ubicación</h2>

                        <div class="space-y-4 mb-8">
                            <div class="flex items-start gap-4">
                                <div class="bg-pindoor-accent/10 p-3 rounded-2xl text-pindoor-accent">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xl font-bold text-gray-900">{{ $punto->sector }}</p>
                                    @if($punto->direccion)
                                        <p class="text-gray-500 text-sm leading-snug mt-1">{{ $punto->direccion }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Mapa --}}
                        @if($punto->lat && $punto->lng)
                            <div id="mini-mapa" class="h-48 rounded-3xl overflow-hidden border border-gray-100 shadow-inner"></div>
                            <a href="https://www.google.com/maps?q={{ $punto->lat }},{{ $punto->lng }}"
                               target="_blank" rel="noopener"
                               class="mt-2 flex items-center justify-center gap-2 w-full py-2 rounded-xl text-xs font-bold text-gray-600 border border-gray-200 hover:bg-pindoor-accent hover:text-white hover:border-pindoor-accent transition-all duration-300">
                                Abrir en Google Maps
                            </a>
                        @endif

                        {{-- Horario --}}
                        @if($punto->horario)
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Horario</h3>
                                <p class="text-gray-700 text-sm font-medium">{{ $punto->horario }}</p>
                            </div>
                        @endif

                        {{-- Oferta del día --}}
                        @if($punto->es_cliente && $punto->oferta_del_dia)
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-amber-500 mb-2">Oferta de hoy</h3>
                                <p class="text-gray-700 text-sm leading-relaxed">{{ $punto->oferta_del_dia }}</p>
                            </div>
                        @endif

                        {{-- Enlace --}}
                        @if($punto->enlace)
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Más información</h3>
                                <a href="{{ $punto->enlace }}" target="_blank" rel="noopener"
                                   class="flex items-center gap-2 text-sm font-bold text-pindoor-accent hover:underline break-all">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    {{ parse_url($punto->enlace, PHP_URL_HOST) ?? $punto->enlace }}
                                </a>
                            </div>
                        @endif

                        <div class="mt-8">
                            <a href="{{ route('atractivos.index') }}"
                               class="group flex items-center justify-center gap-2 w-full bg-gray-900 text-white py-4 rounded-2xl font-bold hover:bg-pindoor-accent transition-all duration-300 shadow-xl shadow-gray-900/10">
                                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Explorar más
                            </a>
                        </div>
                    </div>
                </aside>

            </div>
        </main>
    </div>

    {{-- Botones flotantes móvil --}}
    <div class="lg:hidden fixed bottom-10 left-6 z-50 flex flex-col gap-2">
        @if($punto->tieneCarta())
        <button
            x-data
            @click="$dispatch('toggle-carta')"
            class="inline-flex items-center gap-2 px-5 py-3 bg-pindoor-accent text-white rounded-full text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            🍽️ <span>Ver carta</span>
        </button>
        @endif

        @if($punto->lat && $punto->lng)
        <a href="https://www.google.com/maps?q={{ $punto->lat }},{{ $punto->lng }}"
           target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 px-5 py-3 bg-white text-black rounded-full border-red-400 border-2 text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            📍 <span>Ir al mapa</span>
        </a>
        @endif
    </div>

    {{-- Escuchar toggle desde botón flotante en móvil --}}
    @if($punto->tieneCarta())
    <script>
        document.addEventListener('alpine:init', () => {
            window.addEventListener('toggle-carta', () => {
                const main = document.querySelector('[x-data]');
                if (main && main._x_dataStack) {
                    const data = main._x_dataStack[0];
                    data.vista = data.vista === 'carta' ? 'contenido' : 'carta';
                }
            });
        });
    </script>
    @endif

    @if($punto->lat && $punto->lng)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('mini-mapa', {
                zoomControl: false, dragging: true,
                scrollWheelZoom: false, attributionControl: false
            }).setView([{{ $punto->lat }}, {{ $punto->lng }}], 15);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#fc5648;width:12px;height:12px;border-radius:50%;border:2px solid white;box-shadow:0 0 10px rgba(0,0,0,0.2);'></div>",
                iconSize: [12, 12], iconAnchor: [6, 6]
            });
            L.marker([{{ $punto->lat }}, {{ $punto->lng }}], { icon: customIcon }).addTo(map);
        });
    </script>
    @endif

</body>
</html>
