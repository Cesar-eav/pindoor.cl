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
    </style>
</head>

<body class="bg-[#f9fafb] text-gray-900 leading-relaxed">
    <div class="w-full mx-auto">
        <x-navbar_labrujula />

        <main class="max-w-5xl mx-auto px-4 py-8 md:py-12">
            {{-- Breadcrumbs Minimalistas --}}
            <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-8 overflow-x-auto whitespace-nowrap">
                <a href="{{ route('atractivos.index') }}" class="hover:text-pindoor-accent transition">Pindoor</a>
                <span class="text-gray-300">/</span>
                @if($punto->categoria)
                    <span class="text-gray-500">{{ $punto->categoria->nombre }}</span>
                    <span class="text-gray-300">/</span>
                @endif
                <span class="text-pindoor-accent">{{ $punto->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                {{-- COLUMNA IZQUIERDA: IMÁGENES Y CONTENIDO --}}
                <div class="lg:col-span-8">
                    
                    {{-- Galería Estilo Pro --}}
                    @php 
                        $imagenes = $punto->imagenes->sortBy('orden'); 
                        $principal = $imagenes->firstWhere('es_principal', true) ?? $imagenes->first(); 
                    @endphp

                    <div class="mb-10">
                        @if($imagenes->count())
                            <div class="relative group">
                                {{-- Imagen Principal con sombra suave --}}
                                <div class="aspect-[16/10] md:aspect-[16/9] rounded-3xl overflow-hidden shadow-2xl shadow-gray-200 mb-4">
                                    <img src="{{ asset('storage/' . $principal->ruta) }}"
                                         alt="{{ $punto->title }}"
                                         class="w-full h-full object-cover transition duration-700 group-hover:scale-105" />
                                </div>

                                {{-- Miniaturas con scroll horizontal en móvil --}}
                                @if($imagenes->count() > 1)
                                    <div class="flex gap-3 overflow-x-auto pb-2 snap-x">
                                        @foreach($imagenes as $img)
                                            <div class="flex-none w-20 h-20 md:w-24 md:h-24 snap-start rounded-2xl overflow-hidden border-2 {{ $img->es_principal ? 'border-pindoor-accent' : 'border-transparent' }} hover:border-pindoor-accent transition cursor-pointer">
                                                <img src="{{ asset('storage/' . $img->ruta) }}"
                                                     alt="{{ $punto->title }}"
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

                    {{-- Texto y Títulos --}}
                    <section class="space-y-6">
                        <div class="space-y-2">
                            @if($punto->categoria)
                                <span class="inline-flex items-center gap-2 bg-pindoor-accent/10 text-pindoor-accent text-[11px] uppercase font-black px-4 py-1.5 rounded-full">
                                    <span class="text-base">{{ $punto->categoria->icono }}</span>
                                    {{ $punto->categoria->nombre }}
                                </span>
                            @endif
                            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight leading-[1.1]">
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

                        {{-- Tags Estilo Chips --}}
                        @if($punto->tags && count($punto->tags))
                            <div class="flex flex-wrap gap-2 pt-6">
                                @foreach($punto->tags as $tag)
                                    <span class="bg-white text-gray-500 text-[11px] font-bold uppercase tracking-wider px-4 py-2 rounded-xl border border-gray-100 shadow-sm">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </div>

                {{-- COLUMNA DERECHA: SIDEBAR --}}
                <aside class="lg:col-span-4 space-y-6">
                    
                    {{-- Card de Ubicación --}}
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/50 border border-white sticky top-8">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-gray-400 mb-6">Ubicación</h2>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex items-start gap-4">
                                <div class="bg-pindoor-accent/10 p-3 rounded-2xl text-pindoor-accent">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2" stroke-linecap="round"/></svg>
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
                            <div class="relative group">
                                <div id="mini-mapa" class="h-48 rounded-3xl overflow-hidden border border-gray-100 shadow-inner z-0"></div>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $punto->lat }},{{ $punto->lng }}"
                                   target="_blank"
                                   class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-sm text-center py-2 rounded-xl text-xs font-bold text-gray-900 shadow-lg hover:bg-pindoor-accenthover:text-white transition-all duration-300">
                                    Abrir en Google Maps
                                </a>
                            </div>
                        @endif

                        {{-- Horario --}}
                        @if($punto->horario)
                            <div class="mt-8 pt-8 border-t border-gray-50">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Cuándo visitar</h3>
                                <p class="text-gray-600 text-sm font-medium">{{ $punto->horario }}</p>
                            </div>
                        @endif

                        <div class="mt-8">
                            <a href="{{ route('atractivos.index') }}"
                               class="group flex items-center justify-center gap-2 w-full bg-gray-900 text-white py-4 rounded-2xl font-bold hover:bg-pindoor-accenttransition-all duration-300 shadow-xl shadow-gray-900/10">
                                <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                Explorar más
                            </a>
                        </div>
                    </div>
                </aside>

            </div>
        </main>
    </div>

    @if($punto->lat && $punto->lng)
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Estilo de mapa más limpio
            const map = L.map('mini-mapa', { 
                zoomControl: false, 
                dragging: true, 
                scrollWheelZoom: false,
                attributionControl: false 
            }).setView([{{ $punto->lat }}, {{ $punto->lng }}], 15);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
            
            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#fc5648; width:12px; height:12px; border-radius:50%; border:2px solid white; box-shadow:0 0 10px rgba(0,0,0,0.2);'></div>",
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            L.marker([{{ $punto->lat }}, {{ $punto->lng }}], { icon: customIcon }).addTo(map);
        });
    </script>
    @endif

</body>
</html>