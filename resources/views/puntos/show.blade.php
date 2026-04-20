@php use Illuminate\Support\Str; @endphp

@extends('layouts.pindoor')

@section('title', $punto->title . ' — Pindoor.cl')

@section('canonical', route('atractivos.show', $punto->slug ?? $punto->id))

@section('bodyClass', 'bg-[#f9fafb] text-gray-900 leading-relaxed')

@section('head')
    <meta name="description" content="{{ Str::limit(strip_tags($punto->description), 160) }}">
    @php
        $imagenPrincipal = $punto->imagenes->firstWhere('es_principal', true) ?? $punto->imagenes->first();
        $imagenUrl = $imagenPrincipal ? asset('storage/' . $imagenPrincipal->ruta) : null;
        $canonicalUrl = route('atractivos.show', $punto->slug ?? $punto->id);
    @endphp
    <meta property="og:type" content="place" />
    <meta property="og:url" content="{{ $canonicalUrl }}" />
    <meta property="og:title" content="{{ $punto->title }} — Pindoor.cl" />
    <meta property="og:description" content="{{ Str::limit(strip_tags($punto->description), 160) }}" />
    @if($imagenUrl)
    <meta property="og:image" content="{{ $imagenUrl }}" />
    <meta property="og:image:alt" content="{{ $punto->title }}" />
    @endif
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $punto->title }} — Pindoor.cl" />
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($punto->description), 160) }}" />
    @if($imagenUrl)<meta name="twitter:image" content="{{ $imagenUrl }}" />@endif
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "{{ $punto->es_cliente ? 'LocalBusiness' : 'TouristAttraction' }}",
        "name": "{{ addslashes($punto->title) }}",
        "description": "{{ addslashes(Str::limit(strip_tags($punto->description), 300)) }}",
        "url": "{{ $canonicalUrl }}"
        @if($imagenUrl),"image": "{{ $imagenUrl }}"@endif
        @if($punto->direccion || $punto->sector)
        ,"address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ addslashes($punto->direccion ?? $punto->sector) }}",
            "addressLocality": "Valparaíso",
            "addressCountry": "CL"
        }
        @endif
        @if($punto->lat && $punto->lng)
        ,"geo": {
            "@type": "GeoCoordinates",
            "latitude": {{ $punto->lat }},
            "longitude": {{ $punto->lng }}
        }
        @endif
        @if($punto->horario),"openingHours": "{{ addslashes($punto->horario) }}"@endif
        @if($punto->enlace),"sameAs": "{{ $punto->enlace }}"@endif
        ,"breadcrumb": {
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type":"ListItem","position":1,"name":"Pindoor","item":"{{ route('atractivos.index') }}"}
                @if($punto->categoria)
                ,{"@type":"ListItem","position":2,"name":"{{ addslashes($punto->categoria->nombre) }}","item":"{{ route('atractivos.categoria', $punto->categoria->slug ?? $punto->categoria->id) }}"}
                ,{"@type":"ListItem","position":3,"name":"{{ addslashes($punto->title) }}","item":"{{ $canonicalUrl }}"}
                @else
                ,{"@type":"ListItem","position":2,"name":"{{ addslashes($punto->title) }}","item":"{{ $canonicalUrl }}"}
                @endif
            ]
        }
    }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&family=Lora:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .serif-text { font-family: 'Lora', serif; }
        [x-cloak] { display: none !important; }
        .richtext strong, .richtext b { font-weight: 700 !important; }
        .richtext em, .richtext i { font-style: italic !important; }
        .richtext u { text-decoration: underline !important; }
        .richtext h2 { font-size: 1.2rem; font-weight: 700; margin: 0.75rem 0 0.25rem; }
        .richtext h3 { font-size: 1rem; font-weight: 700; margin: 0.5rem 0 0.25rem; }
        .richtext ul { list-style-type: disc; padding-left: 1.5rem; margin: 0.4rem 0; }
        .richtext ol { list-style-type: decimal; padding-left: 1.5rem; margin: 0.4rem 0; }
        .richtext li { margin: 0.2rem 0; }
        .richtext p:not(:first-child) { margin-top: 0.5rem; }
    </style>
@endsection

@section('content')
    <div class="w-full mx-auto">
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

            {{-- Tabs móvil --}}
            @php
                $alojamiento    = $punto->dato('alojamiento');
                $tieneTabsExtra = $punto->tieneOfertaActiva()
                    || $punto->tieneCarta()
                    || $punto->tieneMenu()
                    || $punto->tieneAvisos()
                    || $punto->tienePromociones()
                    || ($punto->moduloActivo('habitaciones') && ($alojamiento['habitaciones'] ?? null))
                    || ($punto->moduloActivo('servicios')    && ($alojamiento['servicios']    ?? null))
                    || ($punto->moduloActivo('politicas')    && ($alojamiento['politicas']    ?? null))
                    || ($punto->moduloActivo('entradas')     && $punto->items('entradas')->count())
                    || ($punto->moduloActivo('exposiciones') && $punto->items('exposiciones')->count())
                    || ($punto->moduloActivo('agenda')       && $punto->eventosProximos()->count());
            @endphp
            @if($tieneTabsExtra)
            <div class="flex lg:hidden gap-2 mb-6 overflow-x-auto">
                <button
                    @click="vista = 'contenido'"
                    :class="vista === 'contenido' ? 'bg-gray-900 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    Descripción
                </button>
                @if($punto->tieneOfertaActiva())
                <button
                    @click="vista = 'oferta'"
                    :class="vista === 'oferta' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    Oferta del día
                </button>
                @endif
                @if($punto->tieneMenu())
                <button
                    @click="vista = 'menu'"
                    :class="vista === 'menu' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    Menú del día
                </button>
                @endif
                @if($punto->tieneCarta())
                <button
                    @click="vista = 'carta'"
                    :class="vista === 'carta' ? 'bg-pindoor-accent text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    Ver carta
                </button>
                @endif
                @if($punto->tieneAvisos())
                <button
                    @click="vista = 'avisos'"
                    :class="vista === 'avisos' ? 'bg-gray-700 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    📢 Avisos
                </button>
                @endif
                @if($punto->tienePromociones())
                <button
                    @click="vista = 'promociones'"
                    :class="vista === 'promociones' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    🎁 Promociones
                </button>
                @endif
                @if($punto->moduloActivo('habitaciones') && ($alojamiento['habitaciones'] ?? null))
                <button @click="vista = 'habitaciones'"
                    :class="vista === 'habitaciones' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    Habitaciones
                </button>
                @endif
                @if($punto->moduloActivo('servicios') && ($alojamiento['servicios'] ?? null))
                <button @click="vista = 'servicios'"
                    :class="vista === 'servicios' ? 'bg-teal-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    Servicios
                </button>
                @endif
                @if($punto->moduloActivo('politicas') && ($alojamiento['politicas'] ?? null))
                <button @click="vista = 'politicas'"
                    :class="vista === 'politicas' ? 'bg-gray-700 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    Políticas
                </button>
                @endif
                @if($punto->moduloActivo('entradas') && $punto->items('entradas')->count())
                <button @click="vista = 'entradas'"
                    :class="vista === 'entradas' ? 'bg-amber-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    🎟️ Entradas
                </button>
                @endif
                @if($punto->moduloActivo('exposiciones') && $punto->items('exposiciones')->count())
                <button @click="vista = 'exposiciones'"
                    :class="vista === 'exposiciones' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    🖼️ Exposiciones
                </button>
                @endif
                @if($punto->moduloActivo('agenda') && $punto->eventosProximos()->count())
                <button @click="vista = 'agenda'"
                    :class="vista === 'agenda' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    📅 Agenda
                </button>
                @endif
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

                    @if($punto->tieneOfertaActiva())
                    <button
                        @click="vista = 'oferta'"
                        :class="vista === 'oferta'
                            ? 'bg-green-600 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-green-500'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🏷️</span> Oferta del día
                    </button>
                    @endif

                    @if($punto->tieneMenu())
                    <button
                        @click="vista = 'menu'"
                        :class="vista === 'menu'
                            ? 'bg-orange-500 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-orange-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🥘</span> Menú del día
                    </button>
                    @endif

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

                    @if($punto->tieneAvisos())
                    <button
                        @click="vista = 'avisos'"
                        :class="vista === 'avisos'
                            ? 'bg-gray-700 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-500'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>📢</span> Avisos
                    </button>
                    @endif

                    @if($punto->tienePromociones())
                    <button
                        @click="vista = 'promociones'"
                        :class="vista === 'promociones'
                            ? 'bg-purple-600 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-purple-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🎁</span> Promociones
                    </button>
                    @endif

                    {{-- Alojamiento --}}
                    @if($punto->moduloActivo('habitaciones') && ($alojamiento['habitaciones'] ?? null))
                    <button @click="vista = 'habitaciones'"
                        :class="vista === 'habitaciones' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-indigo-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🛏️</span> Habitaciones
                    </button>
                    @endif

                    @if($punto->moduloActivo('servicios') && ($alojamiento['servicios'] ?? null))
                    <button @click="vista = 'servicios'"
                        :class="vista === 'servicios' ? 'bg-teal-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-teal-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>✨</span> Servicios
                    </button>
                    @endif

                    @if($punto->moduloActivo('politicas') && ($alojamiento['politicas'] ?? null))
                    <button @click="vista = 'politicas'"
                        :class="vista === 'politicas' ? 'bg-gray-700 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-500'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>📋</span> Políticas
                    </button>
                    @endif

                    @if($punto->moduloActivo('entradas') && $punto->items('entradas')->count())
                    <button @click="vista = 'entradas'"
                        :class="vista === 'entradas' ? 'bg-amber-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-amber-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🎟️</span> Entradas
                    </button>
                    @endif

                    @if($punto->moduloActivo('exposiciones') && $punto->items('exposiciones')->count())
                    <button @click="vista = 'exposiciones'"
                        :class="vista === 'exposiciones' ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-purple-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🖼️</span> Exposiciones
                    </button>
                    @endif

                    @if($punto->moduloActivo('agenda') && $punto->eventosProximos()->count())
                    <button @click="vista = 'agenda'"
                        :class="vista === 'agenda' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-blue-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>📅</span> Agenda
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

                        {{-- CABECERA DEL NEGOCIO --}}
                        @if($punto->es_cliente)
                        <div class="flex gap-4 items-start bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-8">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-16 h-16 rounded-2xl object-cover border border-gray-100 shrink-0">
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center shrink-0 text-2xl">🏪</div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="text-lg font-extrabold text-gray-900 leading-tight">{{ $punto->title }}</h2>
                                    @if($punto->categoria)
                                        <span class="text-[10px] font-black uppercase tracking-widest bg-pindoor-accent/10 text-pindoor-accent px-2 py-0.5 rounded-full">
                                            {{ $punto->categoria->nombre }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-sm text-gray-500 mt-1">📍 {{ $punto->sector }}{{ $punto->direccion ? ' · ' . $punto->direccion : '' }}</p>

                                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2">
                                    @if($punto->horario)
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            🕐 {{ $punto->horario }}
                                        </span>
                                    @endif
                                    @if($punto->enlace)
                                        <a href="{{ $punto->enlace }}" target="_blank" rel="noopener"
                                           class="text-xs text-pindoor-accent hover:underline font-medium flex items-center gap-1 truncate max-w-[200px]">
                                            🔗 {{ parse_url($punto->enlace, PHP_URL_HOST) ?? $punto->enlace }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Galería --}}
                        @php
                            $imagenes = $punto->imagenes->sortBy('orden');
                            $principal = $imagenes->firstWhere('es_principal', true) ?? $imagenes->first();
                            $principalIndex = $imagenes->values()->search(fn($img) => $img->id === ($principal?->id));
                        @endphp

                        <div class="mb-10">
                            @if($imagenes->count())
                                <div x-data='{
                                    images: @json($imagenes->values()->map(fn($img) => asset("storage/" . $img->ruta))),
                                    current: {{ $principalIndex !== false ? $principalIndex : 0 }},
                                    lightbox: false,
                                    init() { console.log("[galeria] init OK — images:", this.images, "| current:", this.current); },
                                    goTo(index) { console.log("[galeria] goTo", index); this.current = index; },
                                    open() { console.log("[galeria] open lightbox"); this.lightbox = true; },
                                    close() { console.log("[galeria] close lightbox"); this.lightbox = false; },
                                    prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
                                    next() { this.current = (this.current + 1) % this.images.length; },
                                }'>

                                    {{-- Imagen principal --}}
                                    <div class="aspect-[16/10] md:aspect-[16/9] rounded-3xl overflow-hidden shadow-2xl shadow-gray-200 mb-4 cursor-zoom-in"
                                         @click="open()">
                                        <img :src="images[current]" alt="{{ $punto->title }}"
                                             class="w-full h-full object-cover transition duration-500" />
                                    </div>

                                    {{-- Miniaturas --}}
                                    @if($imagenes->count() > 1)
                                        <div class="flex gap-3 overflow-x-auto pb-2 snap-x">
                                            @foreach($imagenes->values() as $i => $img)
                                                <div @click="goTo({{ $i }})"
                                                     class="flex-none w-20 h-20 md:w-24 md:h-24 snap-start rounded-2xl overflow-hidden border-2 transition cursor-pointer"
                                                     :class="current === {{ $i }} ? 'border-pindoor-accent' : 'border-transparent hover:border-gray-300'">
                                                    <img src="{{ asset('storage/' . $img->ruta) }}" alt="{{ $punto->title }}"
                                                         class="w-full h-full object-cover" />
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Lightbox --}}
                                    <div x-show="lightbox" x-transition.opacity
                                         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
                                         @click.self="close()"
                                         @keydown.escape.window="close()"
                                         @keydown.arrow-left.window="prev()"
                                         @keydown.arrow-right.window="next()"
                                         style="display:none;">

                                        {{-- Cerrar --}}
                                        <button @click="close()"
                                                class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>

                                        {{-- Anterior --}}
                                        @if($imagenes->count() > 1)
                                            <button @click="prev()"
                                                    class="absolute left-4 text-white bg-white/10 hover:bg-white/20 rounded-full p-3 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Imagen --}}
                                        <img :src="images[current]" alt="{{ $punto->title }}"
                                             class="max-h-[90vh] max-w-[90vw] object-contain rounded-xl shadow-2xl select-none" />

                                        {{-- Siguiente --}}
                                        @if($imagenes->count() > 1)
                                            <button @click="next()"
                                                    class="absolute right-4 text-white bg-white/10 hover:bg-white/20 rounded-full p-3 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        @endif

                                        {{-- Contador --}}
                                        @if($imagenes->count() > 1)
                                            <div class="absolute bottom-4 text-white/60 text-sm">
                                                <span x-text="current + 1"></span> / {{ $imagenes->count() }}
                                            </div>
                                        @endif
                                    </div>
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

                            <div class="richtext serif-text text-lg text-gray-700 leading-relaxed space-y-4">
                                {!! ($punto->description) !!}
                            </div>



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

                    {{-- PANEL: Oferta del día --}}
                    @if($punto->tieneOfertaActiva())
                    <div x-show="vista === 'oferta'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-green-600 mb-0.5">Válida hoy</p>
                                <span class="text-2xl font-extrabold text-gray-900">Oferta del día</span>
                                @if($punto->oferta_expira_at)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Válida hasta {{ $punto->oferta_expira_at->translatedFormat('d \d\e F') }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-sm border border-green-100 p-8">
                            <div class="richtext serif-text text-gray-700 leading-relaxed text-base">
                                {!! $punto->oferta_del_dia !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PANEL: Menú del día --}}
                    @if($punto->tieneMenu())
                    @php $datoMenu = $punto->dato('menu_del_dia'); @endphp
                    <div x-show="vista === 'menu'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-orange-500 mb-0.5">Hoy</p>
                                <span class="text-2xl font-extrabold text-gray-900">Menú del día</span>
                                @if($punto->registroDato('menu_del_dia')?->actualizado_en)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Actualizado {{ $punto->registroDato('menu_del_dia')->actualizado_en->diffForHumans() }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-sm border border-orange-100 p-8">
                            <div class="richtext serif-text text-gray-700 leading-relaxed text-base">
                                {!! $datoMenu['texto'] ?? '' !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PANEL: Avisos --}}
                    @if($punto->tieneAvisos())
                    <div x-show="vista === 'avisos'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-gray-600 mb-0.5">Aviso</p>
                                <span class="text-2xl font-extrabold text-gray-900">Avisos</span>
                            </div>
                        </div>
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-8">
                            <div class="richtext serif-text text-gray-700 leading-relaxed text-base">
                                {!! $punto->dato('avisos')['texto'] ?? '' !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PANEL: Promociones --}}
                    @if($punto->tienePromociones())
                    <div x-show="vista === 'promociones'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-purple-600 mb-0.5">Oferta especial</p>
                                <span class="text-2xl font-extrabold text-gray-900">Promociones</span>
                            </div>
                        </div>
                        <div class="bg-white rounded-3xl shadow-sm border border-purple-100 p-8">
                            <div class="richtext serif-text text-gray-700 leading-relaxed text-base">
                                {!! $punto->dato('promociones')['texto'] ?? '' !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PANEL: Carta / Menú --}}
                    @if($punto->tieneCarta())
                    @php
                        $datoCarta       = $punto->dato('carta');
                        $registroCarta   = $punto->registroDato('carta');
                    @endphp
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
                                @if($registroCarta?->actualizado_en)
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Actualizada {{ $registroCarta->actualizado_en->diffForHumans() }}
                                    </p>
                                @endif
                            </div>

                            @if($datoCarta['pdf_ruta'] ?? null)
                                <a href="{{ asset('storage/' . $datoCarta['pdf_ruta']) }}"
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
                            <div class="richtext serif-text text-gray-700 leading-relaxed text-base">
                                {!! $datoCarta['texto'] ?? '' !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PANEL: Habitaciones (alojamiento) --}}
                    @if($punto->moduloActivo('habitaciones') && ($alojamiento['habitaciones'] ?? null))
                    <div x-show="vista === 'habitaciones'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if($punto->imagen_perfil)
                                    <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                         alt="Logo" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                                @endif
                                <div>
                                    <p class="text-xs font-black uppercase tracking-widest text-indigo-600 mb-0.5">Alojamiento</p>
                                    <span class="text-2xl font-extrabold text-gray-900">Habitaciones</span>
                                </div>
                            </div>
                            @if($alojamiento['precio_desde'] ?? null)
                                <div class="text-right">
                                    <p class="text-xs text-gray-400 uppercase font-bold">Desde</p>
                                    <p class="text-lg font-extrabold text-indigo-600">{{ $alojamiento['precio_desde'] }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="bg-white rounded-3xl shadow-sm border border-indigo-100 p-8">
                            <div class="richtext serif-text text-gray-700 leading-relaxed text-base">
                                {!! $alojamiento['habitaciones'] !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PANEL: Servicios (alojamiento) --}}
                    @if($punto->moduloActivo('servicios') && ($alojamiento['servicios'] ?? null))
                    @php
                        $catalogoGrupos  = App\Models\PuntoInteres::catalogoServicios();
                        $serviciosActivos = $alojamiento['servicios'] ?? [];
                    @endphp
                    <div x-show="vista === 'servicios'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-teal-600 mb-0.5">Incluido</p>
                                <span class="text-2xl font-extrabold text-gray-900">Servicios</span>
                            </div>
                        </div>

                        <div class="space-y-6">
                            @foreach($catalogoGrupos as $grupo => $servicios)
                                @php $visibles = array_filter($servicios, fn($s, $k) => in_array($k, $serviciosActivos), ARRAY_FILTER_USE_BOTH); @endphp
                                @if(count($visibles))
                                <div class="bg-white rounded-2xl border border-teal-100 overflow-hidden">
                                    <div class="bg-teal-50 px-5 py-2.5">
                                        <p class="text-xs font-black uppercase tracking-widest text-teal-700">{{ $grupo }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4">
                                        @foreach($visibles as $servicio)
                                        <div class="flex items-center gap-2 bg-gray-50 rounded-xl px-3 py-2.5">
                                            <span class="text-xl">{{ $servicio['emoji'] }}</span>
                                            <span class="text-sm font-medium text-gray-700">{{ $servicio['label'] }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- PANEL: Políticas (alojamiento) --}}
                    @if($punto->moduloActivo('politicas') && ($alojamiento['politicas'] ?? null))
                    <div x-show="vista === 'politicas'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-gray-500 mb-0.5">Información</p>
                                <span class="text-2xl font-extrabold text-gray-900">Políticas</span>
                            </div>
                        </div>

                        {{-- Check-in / Check-out --}}
                        @if(($alojamiento['entrada'] ?? null) || ($alojamiento['salida'] ?? null))
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            @if($alojamiento['entrada'] ?? null)
                            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Check-in</p>
                                <p class="text-2xl font-extrabold text-gray-900">{{ $alojamiento['entrada'] }}</p>
                            </div>
                            @endif
                            @if($alojamiento['salida'] ?? null)
                            <div class="bg-white rounded-2xl border border-gray-100 p-5 text-center">
                                <p class="text-xs text-gray-400 uppercase font-bold mb-1">Check-out</p>
                                <p class="text-2xl font-extrabold text-gray-900">{{ $alojamiento['salida'] }}</p>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                            <div class="richtext serif-text text-gray-700 leading-relaxed text-base">
                                {!! $alojamiento['politicas'] !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- PANEL: Entradas (museo) --}}
                    @if($punto->moduloActivo('entradas') && $punto->items('entradas')->count())
                    <div x-show="vista === 'entradas'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-amber-600 mb-0.5">Acceso</p>
                                <span class="text-2xl font-extrabold text-gray-900">Entradas y tarifas</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-amber-50 border-b border-amber-100">
                                        <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-widest text-amber-700">Tipo</th>
                                        <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-widest text-amber-700">Precio</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-amber-50">
                                    @foreach($punto->items('entradas') as $entrada)
                                    <tr class="hover:bg-amber-50/50 transition">
                                        <td class="px-6 py-4">
                                            <p class="font-semibold text-gray-800">{{ $entrada->datos['etiqueta'] ?? '' }}</p>
                                            @if($entrada->datos['nota'] ?? null)
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $entrada->datos['nota'] }}</p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-extrabold text-lg {{ ($entrada->datos['precio'] ?? 1) == 0 ? 'text-green-600' : 'text-amber-700' }}">
                                                {{ $entrada->precioEntrada() }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($punto->horario)
                        <div class="mt-4 bg-amber-50 rounded-2xl px-5 py-3 flex items-center gap-2 text-sm text-amber-800">
                            <span>🕐</span> <span class="font-medium">{{ $punto->horario }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- PANEL: Exposiciones (museo) --}}
                    @if($punto->moduloActivo('exposiciones') && $punto->items('exposiciones')->count())
                    @php
                        $todasExposiciones = $punto->items('exposiciones');
                        $permanentes = $todasExposiciones->filter(fn($e) => ($e->datos['tipo'] ?? '') === 'permanente');
                        $temporales  = $todasExposiciones->filter(fn($e) => ($e->datos['tipo'] ?? '') === 'temporal');
                    @endphp
                    <div x-show="vista === 'exposiciones'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-purple-600 mb-0.5">Colecciones</p>
                                <span class="text-2xl font-extrabold text-gray-900">Exposiciones</span>
                            </div>
                        </div>

                        @if($permanentes->count())
                        <div class="mb-6">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Permanentes</p>
                            <div class="space-y-4">
                                @foreach($permanentes as $expo)
                                <div class="bg-white rounded-2xl border border-blue-100 overflow-hidden flex gap-0">
                                    @if($expo->imagen)
                                    <div class="w-24 shrink-0">
                                        <img src="{{ asset('storage/' . $expo->imagen) }}" alt="{{ $expo->datos['titulo'] ?? '' }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                    @endif
                                    <div class="p-5 flex-1">
                                        <span class="text-[10px] font-black uppercase bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Permanente</span>
                                        <h3 class="font-extrabold text-gray-900 mt-2 text-base">{{ $expo->datos['titulo'] ?? '' }}</h3>
                                        @if($expo->datos['descripcion'] ?? null)
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $expo->datos['descripcion'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($temporales->count())
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Temporales</p>
                            <div class="space-y-4">
                                @foreach($temporales as $expo)
                                <div class="bg-white rounded-2xl border border-orange-100 overflow-hidden flex gap-0">
                                    @if($expo->imagen)
                                    <div class="w-24 shrink-0">
                                        <img src="{{ asset('storage/' . $expo->imagen) }}" alt="{{ $expo->datos['titulo'] ?? '' }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                    @endif
                                    <div class="p-5 flex-1">
                                        <span class="text-[10px] font-black uppercase bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">Temporal</span>
                                        <h3 class="font-extrabold text-gray-900 mt-2 text-base">{{ $expo->datos['titulo'] ?? '' }}</h3>
                                        @if($expo->datos['descripcion'] ?? null)
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $expo->datos['descripcion'] }}</p>
                                        @endif
                                        @if(($expo->datos['fecha_inicio'] ?? null) || ($expo->datos['fecha_fin'] ?? null))
                                        <p class="text-xs text-gray-400 mt-2">
                                            📅
                                            {{ $expo->datos['fecha_inicio'] ? \Carbon\Carbon::parse($expo->datos['fecha_inicio'])->translatedFormat('d M Y') : '—' }}
                                            →
                                            {{ $expo->datos['fecha_fin'] ? \Carbon\Carbon::parse($expo->datos['fecha_fin'])->translatedFormat('d M Y') : 'Sin fecha fin' }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    {{-- PANEL: Agenda cultural --}}
                    @if($punto->moduloActivo('agenda') && $punto->eventosProximos()->count())
                    @php
                        $eventosProximos   = $punto->eventosProximos();
                        $eventosDestacados = $eventosProximos->where('destacado', true);
                        $eventosResto      = $eventosProximos->where('destacado', false);
                    @endphp
                    <div x-show="vista === 'agenda'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="mb-6 flex items-center gap-3">
                            @if($punto->imagen_perfil)
                                <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                     alt="Logo {{ $punto->title }}"
                                     class="w-12 h-12 rounded-xl object-cover border border-gray-100 shrink-0">
                            @endif
                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-blue-600 mb-0.5">Programación</p>
                                <span class="text-2xl font-extrabold text-gray-900">Agenda</span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($eventosProximos as $evento)
                            @php $tipoInfo = $evento->tipoEvento(); @endphp
                            <div class="bg-white rounded-2xl border {{ $evento->destacado ? 'border-blue-200 shadow-md' : 'border-gray-100' }} overflow-hidden">
                                <div class="flex gap-0">
                                    @if($evento->imagen)
                                    <div class="w-28 shrink-0">
                                        <img src="{{ asset('storage/' . $evento->imagen) }}" alt="{{ $evento->datos['titulo'] ?? '' }}"
                                             class="w-full h-full object-cover min-h-[7rem]">
                                    </div>
                                    @else
                                    <div class="w-20 shrink-0 flex items-center justify-center bg-gray-50 border-r border-gray-100 text-3xl min-h-[7rem]">
                                        {{ $tipoInfo['emoji'] }}
                                    </div>
                                    @endif
                                    <div class="p-4 flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                @if($evento->destacado)
                                                    <span class="text-[10px] font-black uppercase bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Destacado</span>
                                                @endif
                                                <span class="text-[10px] font-black uppercase bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full ml-1">
                                                    {{ $tipoInfo['emoji'] }} {{ $tipoInfo['label'] }}
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="font-extrabold text-gray-900 mt-2 leading-tight">{{ $evento->datos['titulo'] ?? '' }}</h3>
                                        @if($evento->datos['descripcion'] ?? null)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $evento->datos['descripcion'] }}</p>
                                        @endif
                                        <div class="flex items-center justify-between mt-2 flex-wrap gap-1">
                                            <p class="text-xs text-gray-500">
                                                📅 {{ $evento->fecha->translatedFormat('d M Y') }}
                                                @if($evento->datos['hora'] ?? null)· {{ \Carbon\Carbon::parse($evento->datos['hora'])->format('H:i') }}@endif
                                            </p>
                                            <span class="text-sm font-extrabold {{ ($evento->datos['precio'] ?? 1) == 0 ? 'text-green-600' : 'text-blue-700' }}">
                                                {{ $evento->precioEvento() }}
                                            </span>
                                        </div>
                                        @if($evento->datos['url_entradas'] ?? null)
                                        <a href="{{ $evento->datos['url_entradas'] }}" target="_blank" rel="noopener"
                                           class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg transition">
                                            Comprar entradas →
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
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

                    </div>
                </aside>

            </div>{{-- /grid --}}

            {{-- Puntos cercanos (200 m) --}}
            @if($cercanos->count())
            <section class="mt-16 pt-10 border-t border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-[#fc5648] mb-1">Cerca de aquí</p>
                        <h2 class="text-2xl font-extrabold text-gray-900">Los más cercanos</h2>
                    </div>
                    <span class="text-xs text-gray-400 font-medium hidden sm:block">
                        {{ $cercanos->count() }} lugar{{ $cercanos->count() !== 1 ? 'es' : '' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($cercanos as $cercano)
                    <a href="{{ route('atractivos.show', $cercano->slug ?? $cercano->id) }}"
                       class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col">

                        {{-- Imagen --}}
                        <div class="relative h-80 md:h-36 overflow-hidden bg-gray-100">
                            @if($cercano->imagenPrincipal)
                                <img src="{{ asset('storage/' . $cercano->imagenPrincipal->ruta) }}"
                                     alt="{{ $cercano->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-3xl text-gray-300">📍</div>
                            @endif

                            {{-- Distancia badge --}}
                            <span class="absolute bottom-2 right-2 bg-black/60 text-white text-[10px] font-bold px-2 py-1 rounded-full backdrop-blur-sm">
                                {{ round($cercano->distancia_m) }} m
                            </span>

                            {{-- Categoría badge --}}
                            @if($cercano->categoria)
                            <span class="absolute top-2 left-2 bg-[#fc5648] text-white text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full">
                                {{ $cercano->categoria->nombre }}
                            </span>
                            @endif
                        </div>

                        {{-- Contenido --}}
                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="font-bold text-gray-900 text-sm leading-tight group-hover:text-[#fc5648] transition line-clamp-2">
                                {{ $cercano->title }}
                            </h3>
                            @if($cercano->sector)
                            <p class="text-xs text-gray-400 mt-1">📍 {{ $cercano->sector }}</p>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
            </section>
            @endif

        </main>
    </div>

    {{-- Botones flotantes móvil --}}
    {{-- Botones flotantes móvil: se ocultan en lg porque allí están en la columna izquierda --}}
    <div class="lg:hidden fixed bottom-10 left-6 z-50 flex flex-col gap-2">
        @if($punto->tieneOfertaActiva())
        <button
            x-data
            @click="$dispatch('set-vista', 'oferta')"
            class="inline-flex items-center gap-2 px-5 py-3 bg-green-600 text-white rounded-full text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            🏷️ <span>Oferta del día</span>
        </button>
        @endif

        @if($punto->tieneMenu())
        <button
            x-data
            @click="$dispatch('set-vista', 'menu')"
            class="inline-flex items-center gap-2 px-5 py-3 bg-orange-500 text-white rounded-full text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            🥘 <span>Menú del día</span>
        </button>
        @endif

        @if($punto->tieneCarta())
        <button
            x-data
            @click="$dispatch('set-vista', 'carta')"
            class="inline-flex items-center gap-2 px-5 py-3 bg-pindoor-accent text-white rounded-full text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            🍽️ <span>Ver carta</span>
        </button>
        @endif

        @if($punto->tieneAvisos())
        <button
            x-data
            @click="$dispatch('set-vista', 'avisos')"
            class="inline-flex items-center gap-2 px-5 py-3 bg-gray-700 text-white rounded-full text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            📢 <span>Avisos</span>
        </button>
        @endif

        @if($punto->tienePromociones())
        <button
            x-data
            @click="$dispatch('set-vista', 'promociones')"
            class="inline-flex items-center gap-2 px-5 py-3 bg-purple-600 text-white rounded-full text-sm font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
            🎁 <span>Promociones</span>
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

    @if($punto->tieneOfertaActiva() || $punto->tieneCarta() || $punto->tieneMenu())
    <script>
        document.addEventListener('alpine:init', () => {
            window.addEventListener('set-vista', (e) => {
                const main = document.querySelector('main[x-data]');
                if (main && main._x_dataStack) {
                    main._x_dataStack[0].vista = e.detail;
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

    </div>{{-- /w-full mx-auto --}}
@endsection
