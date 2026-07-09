@php
    use Illuminate\Support\Str;
    $seoLugar   = trim(($punto->sector ?: $punto->ciudad ?: 'Valparaíso'));
    $seoCateg   = $punto->categoria?->nombre ?? '';
    $seoTitle   = $punto->title . ', ' . $seoLugar . ' — Pindoor';
    $seoDesc    = ($seoCateg ? $seoCateg . ' en ' . $seoLugar . ', Valparaíso. ' : '')
                . Str::limit(strip_tags(strip_tags($punto->description ?? '')), 110);
@endphp

@extends('layouts.pindoor')

@section('title', $seoTitle)

@section('canonical', route('puntos.show', $punto->slug ?? $punto->id))

@section('description', $seoDesc)

@section('bodyClass', 'bg-[#f9fafb] text-gray-900 leading-relaxed')

@php
    $imagenPrincipal = $punto->imagenes->firstWhere('es_principal', true) ?? $punto->imagenes->first();
    $imagenUrl       = $imagenPrincipal ? asset('storage/' . $imagenPrincipal->ruta) : null;
    $canonicalUrl    = route('puntos.show', $punto->slug ?? $punto->id);
@endphp

@section('og_type', 'place')
@section('og_url', $canonicalUrl)
@section('og_title', $seoTitle)
@section('og_description', $seoDesc)
@if($imagenUrl)@section('og_image', $imagenUrl)@endif

@section('head')
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
                {"@type":"ListItem","position":1,"name":"Pindoor","item":"{{ route('puntos.index') }}"}
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
    @vite('resources/js/leaflet.js')
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
    </style>
@endsection

@section('content')
    <div class="w-full mx-auto">
        {{-- Componente principal Alpine --}}
        <main
            x-data="{ vista: ['agenda','catalogo','carta','menu','oferta','exposiciones'].includes(location.hash.slice(1)) ? location.hash.slice(1) : 'contenido' }"
            class="max-w-7xl mx-auto px-4 py-8 md:py-12"
        >
            {{-- Breadcrumbs --}}
            <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-8 overflow-x-auto whitespace-nowrap">
                <a href="{{ route('puntos.index') }}" class="hover:text-pindoor-accent transition">Pindoor</a>
                <span class="text-gray-300">/</span>
                @if($punto->categoria)
                    <span class="text-gray-500">{{ $punto->categoria->nombre }}</span>
                    <span class="text-gray-300">/</span>
                @endif
                <span class="text-pindoor-accent">{{ $punto->title }}</span>
            </nav>

            {{-- Aviso especial para ascensores --}}
            @if($punto->categoria?->slug === 'ascensores')
            @php $esOperativo = str_contains($punto->getTranslation('description', 'es') ?? '', '*OPERATIVO*'); @endphp
            @if($esOperativo)
            <div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-5 space-y-3">
                <div class="flex items-start gap-3">
                    <span class="text-2xl leading-none mt-0.5">⚠️</span>
                    <div class="space-y-2 text-sm text-amber-900">
                        <p class="font-bold text-base">{{ __('ui.lugar.ascensor_aviso_titulo') }}</p>
                        <p>{!! __('ui.lugar.ascensor_operativo_aviso') !!}</p>
                        <div class="bg-amber-100 rounded-xl px-4 py-3 space-y-1">
                            <p class="font-bold text-amber-800">{{ __('ui.lugar.ascensor_aviso_horario') }}</p>
                            <p>{{ __('ui.lugar.ascensor_aviso_horario_semana') }}</p>
                            <p>{{ __('ui.lugar.ascensor_aviso_horario_finde') }}</p>
                        </div>
                        <p class="text-amber-700 text-xs">{!! __('ui.lugar.ascensor_operativo_mantenc') !!}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <span class="text-2xl leading-none mt-0.5">🚫</span>
                    <div class="space-y-1 text-sm text-red-900">
                        <p class="font-bold text-base">{{ __('ui.lugar.ascensor_fuera_titulo') }}</p>
                        <p>{!! __('ui.lugar.ascensor_fuera_descripcion') !!}</p>
                    </div>
                </div>
            </div>
            @endif
            @endif

            {{-- Tabs móvil --}}
            @php
                $alojamiento    = $punto->dato('alojamiento');
                $tieneProductos  = $punto->productos()->exists();
                $tieneTabsExtra = $tieneProductos
                    || $punto->tieneOfertaActiva()
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
                    {{ __('ui.lugar.tab_descripcion') }}
                </button>
                @if($tieneProductos)
                <button
                    @click="vista = 'catalogo'"
                    :class="vista === 'catalogo' ? 'bg-[#fc5648] text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    {{ __('ui.lugar.tab_catalogo') }}
                </button>
                @endif
                @if($punto->tieneOfertaActiva())
                <button
                    @click="vista = 'oferta'"
                    :class="vista === 'oferta' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    {{ __('ui.lugar.tab_oferta') }}
                </button>
                @endif
                @if($punto->tieneMenu())
                <button
                    @click="vista = 'menu'"
                    :class="vista === 'menu' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    {{ __('ui.lugar.tab_menu') }}
                </button>
                @endif
                @if($punto->tieneCarta())
                <button
                    @click="vista = 'carta'"
                    :class="vista === 'carta' ? 'bg-pindoor-accent text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    {{ __('ui.lugar.tab_carta') }}
                </button>
                @endif
                @if($punto->tieneAvisos())
                <button
                    @click="vista = 'avisos'"
                    :class="vista === 'avisos' ? 'bg-gray-700 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    {{ __('ui.lugar.tab_avisos') }}
                </button>
                @endif
                @if($punto->tienePromociones())
                <button
                    @click="vista = 'promociones'"
                    :class="vista === 'promociones' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    {{ __('ui.lugar.tab_promociones') }}
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
                    {{ __('ui.lugar.tab_entradas') }}
                </button>
                @endif
                @if($punto->moduloActivo('exposiciones') && $punto->items('exposiciones')->count())
                <button @click="vista = 'exposiciones'"
                    :class="vista === 'exposiciones' ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    {{ __('ui.lugar.tab_exposiciones') }}
                </button>
                @endif
                @if($punto->moduloActivo('agenda') && $punto->eventosProximos()->count())
                <button @click="vista = 'agenda'"
                    :class="vista === 'agenda' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200'"
                    class="flex-none py-2.5 px-4 rounded-xl text-sm font-bold transition">
                    {{ __('ui.lugar.tab_agenda') }}
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
                        <span>📖</span> {{ __('ui.lugar.tab_descripcion') }}
                    </button>

                    @if($tieneProductos)
                    <button
                        @click="vista = 'catalogo'"
                        :class="vista === 'catalogo'
                            ? 'bg-[#fc5648] text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-[#fc5648]'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🛍️</span> {{ __('ui.lugar.tab_catalogo') }}
                    </button>
                    @endif

                    @if($punto->tieneOfertaActiva())
                    <button
                        @click="vista = 'oferta'"
                        :class="vista === 'oferta'
                            ? 'bg-green-600 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-green-500'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🏷️</span> {{ __('ui.lugar.tab_oferta') }}
                    </button>
                    @endif

                    @if($punto->tieneMenu())
                    <button
                        @click="vista = 'menu'"
                        :class="vista === 'menu'
                            ? 'bg-orange-500 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-orange-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🥘</span> {{ __('ui.lugar.tab_menu') }}
                    </button>
                    @endif

                    @if($punto->tieneCarta())
                    <button
                        @click="vista = 'carta'"
                        :class="vista === 'carta'
                            ? 'bg-pindoor-accent text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-pindoor-accent'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🍽️</span> {{ __('ui.lugar.tab_carta') }}
                    </button>
                    @endif

                    @if($punto->tieneAvisos())
                    <button
                        @click="vista = 'avisos'"
                        :class="vista === 'avisos'
                            ? 'bg-gray-700 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-500'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>📢</span> {{ __('ui.lugar.tab_avisos') }}
                    </button>
                    @endif

                    @if($punto->tienePromociones())
                    <button
                        @click="vista = 'promociones'"
                        :class="vista === 'promociones'
                            ? 'bg-purple-600 text-white shadow-lg'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-purple-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🎁</span> {{ __('ui.lugar.tab_promociones') }}
                    </button>
                    @endif


                    {{-- Alojamiento --}}
                    @if($punto->moduloActivo('habitaciones') && ($alojamiento['habitaciones'] ?? null))
                    <button @click="vista = 'habitaciones'"
                        :class="vista === 'habitaciones' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-indigo-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🛏️</span> {{ __('ui.lugar.tab_habitaciones') }}
                    </button>
                    @endif

                    @if($punto->moduloActivo('servicios') && ($alojamiento['servicios'] ?? null))
                    <button @click="vista = 'servicios'"
                        :class="vista === 'servicios' ? 'bg-teal-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-teal-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>✨</span> {{ __('ui.lugar.tab_servicios') }}
                    </button>
                    @endif

                    @if($punto->moduloActivo('politicas') && ($alojamiento['politicas'] ?? null))
                    <button @click="vista = 'politicas'"
                        :class="vista === 'politicas' ? 'bg-gray-700 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-500'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>📋</span> {{ __('ui.lugar.tab_politicas') }}
                    </button>
                    @endif

                    @if($punto->moduloActivo('entradas') && $punto->items('entradas')->count())
                    <button @click="vista = 'entradas'"
                        :class="vista === 'entradas' ? 'bg-amber-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-amber-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🎟️</span> {{ __('ui.lugar.tab_entradas') }}
                    </button>
                    @endif

                    @if($punto->moduloActivo('exposiciones') && $punto->items('exposiciones')->count())
                    <button @click="vista = 'exposiciones'"
                        :class="vista === 'exposiciones' ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-purple-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>🖼️</span> {{ __('ui.lugar.tab_exposiciones') }}
                    </button>
                    @endif

                    @if($punto->moduloActivo('agenda') && $punto->eventosProximos()->count())
                    <button @click="vista = 'agenda'"
                        :class="vista === 'agenda' ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-600 border border-gray-200 hover:border-blue-400'"
                        class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2">
                        <span>📅</span> {{ __('ui.lugar.tab_agenda') }}
                    </button>
                    @endif

                    @if($punto->lat && $punto->lng)
                    <button @click="$dispatch('geo-modal')"
                       class="w-full py-3 px-4 rounded-2xl text-sm font-bold text-left transition-all duration-200 flex items-center gap-2 bg-white text-gray-600 border border-gray-200 hover:border-gray-400">
                        <span>📍</span> {{ __('ui.lugar.como_llegar') }}
                    </button>
                    @endif
                </aside>

                {{-- COLUMNA CENTRAL: contenido dinámico --}}
                <div class="lg:col-span-6">

                    {{-- PANEL: Contenido principal --}}
                    <div x-show="vista === 'contenido'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">

                        {{-- CABECERA DEL NEGOCIO --}}
                        @if($punto->es_cliente || $punto->categoria_id==7)
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
                                    {{ __('ui.lugar.sin_fotos') }}
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

                            <div class="richtext serif-text text-lg text-gray-700 leading-relaxed">
                                {!! str_replace('*OPERATIVO*', '', $punto->description) !!}
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
                    {{-- Panel catálogo de productos --}}
                    @if($tieneProductos)
                    <div x-show="vista === 'catalogo'" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <h2 class="text-xl font-black text-gray-900 mb-5">🛍️ Catálogo</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($punto->productos as $producto)
                            <a href="{{ route('puntos.producto', [$punto->slug ?? $punto->id, $producto->id]) }}"
                               class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                                @if($producto->imagen)
                                    <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}"
                                         class="w-full aspect-square object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full aspect-square bg-gray-100 flex items-center justify-center text-4xl">📦</div>
                                @endif
                                <div class="p-3">
                                    <p class="font-bold text-gray-900 text-sm leading-tight group-hover:text-pindoor-accent transition">{{ $producto->nombre }}</p>
                                    @if($producto->precio)
                                    <p class="text-sm font-black text-[#fc5648] mt-1">{{ $producto->precio }}</p>
                                    @endif
                                    @if($producto->descripcion)
                                    <p class="text-xs text-gray-400 mt-1 leading-snug line-clamp-2">{{ $producto->descripcion }}</p>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif

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
                                <span class="text-2xl font-extrabold text-gray-900">{{ __('ui.lugar.oferta_titulo') }}</span>
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
                                <span class="text-2xl font-extrabold text-gray-900">{{ __('ui.lugar.menu_titulo') }}</span>
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
                                <p class="text-xs font-black uppercase tracking-widest text-purple-600 mb-0.5">{{ __('ui.lugar.oferta_especial') }}</p>
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
                                <span class="text-2xl font-extrabold text-gray-900">{{ __('ui.lugar.carta_titulo') }}</span>
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

                        @php $textoCartaLimpio = trim(strip_tags($datoCarta['texto'] ?? '')); @endphp
                        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                            @if($textoCartaLimpio)
                                <div class="richtext serif-text text-gray-700 leading-relaxed text-base">
                                    {!! $datoCarta['texto'] !!}
                                </div>
                            @elseif($datoCarta['pdf_ruta'] ?? null)
                                <div class="flex flex-col items-center justify-center gap-4 py-6 text-center">
                                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm text-gray-500">La carta está disponible en formato PDF.</p>
                                    <a href="{{ asset('storage/' . $datoCarta['pdf_ruta']) }}"
                                       target="_blank" rel="noopener"
                                       class="inline-flex items-center gap-2 px-6 py-3 bg-[#fc5648] text-white text-sm font-bold rounded-xl hover:bg-[#d94439] transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ __('ui.lugar.carta_ver_pdf') }}
                                    </a>
                                </div>
                            @endif
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
                                    <span class="text-2xl font-extrabold text-gray-900">{{ __('ui.lugar.habitaciones_titulo') }}</span>
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
                                <span class="text-2xl font-extrabold text-gray-900">{{ __('ui.lugar.servicios_titulo') }}</span>
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
                                <p class="text-xs font-black uppercase tracking-widest text-gray-500 mb-0.5">{{ __('ui.lugar.informacion') }}</p>
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
                                <span class="text-2xl font-extrabold text-gray-900">{{ __('ui.lugar.entradas_titulo') }}</span>
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
                                <span class="text-2xl font-extrabold text-gray-900">{{ __('ui.lugar.exposiciones_titulo') }}</span>
                            </div>
                        </div>

                        @if($permanentes->count())
                        <div class="mb-8">
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Permanentes</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($permanentes as $expo)
                                <a href="{{ route('puntos.exposicion', [$punto->slug, $expo->id]) }}"
                                   class="bg-white rounded-2xl border border-blue-100 overflow-hidden shadow-sm flex flex-col hover:shadow-md transition">
                                    @if($expo->imagen)
                                    <div class="aspect-[3/4] overflow-hidden bg-gray-100">
                                        <img src="{{ asset('storage/' . $expo->imagen) }}"
                                             alt="{{ $expo->datos['titulo'] ?? '' }}"
                                             class="w-full h-full object-cover object-top">
                                    </div>
                                    @else
                                    <div class="aspect-[3/4] bg-blue-50 flex items-center justify-center text-4xl">🖼️</div>
                                    @endif
                                    <div class="p-3 flex-1 flex flex-col">
                                        <span class="text-[10px] font-black uppercase bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full self-start mb-1.5">Permanente</span>
                                        <h3 class="font-extrabold text-gray-900 text-sm leading-snug">{{ $expo->datos['titulo'] ?? '' }}</h3>
                                        @if($expo->datos['descripcion'] ?? null)
                                            <p class="text-xs text-gray-500 mt-1 leading-relaxed line-clamp-3">{{ $expo->datos['descripcion'] }}</p>
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($temporales->count())
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Temporales</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach($temporales as $expo)
                                <a href="{{ route('puntos.exposicion', [$punto->slug, $expo->id]) }}"
                                   class="bg-white rounded-2xl border border-orange-100 overflow-hidden shadow-sm flex flex-col hover:shadow-md transition">
                                    @if($expo->imagen)
                                    <div class="aspect-[3/4] overflow-hidden bg-gray-100">
                                        <img src="{{ asset('storage/' . $expo->imagen) }}"
                                             alt="{{ $expo->datos['titulo'] ?? '' }}"
                                             class="w-full h-full object-cover object-top">
                                    </div>
                                    @else
                                    <div class="aspect-[3/4] bg-orange-50 flex items-center justify-center text-4xl">🗓️</div>
                                    @endif
                                    <div class="p-3 flex-1 flex flex-col">
                                        <span class="text-[10px] font-black uppercase bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full self-start mb-1.5">Temporal</span>
                                        <h3 class="font-extrabold text-gray-900 text-sm leading-snug">{{ $expo->datos['titulo'] ?? '' }}</h3>
                                        @if($expo->datos['descripcion'] ?? null)
                                            <p class="text-xs text-gray-500 mt-1 leading-relaxed line-clamp-3">{{ $expo->datos['descripcion'] }}</p>
                                        @endif
                                        @if(($expo->datos['fecha_inicio'] ?? null) || ($expo->datos['fecha_fin'] ?? null))
                                        <p class="text-[11px] text-gray-400 mt-auto pt-2">
                                            📅
                                            {{ $expo->datos['fecha_inicio'] ? \Carbon\Carbon::parse($expo->datos['fecha_inicio'])->translatedFormat('d M Y') : '—' }}
                                            → {{ $expo->datos['fecha_fin'] ? \Carbon\Carbon::parse($expo->datos['fecha_fin'])->translatedFormat('d M Y') : 'Sin fecha fin' }}
                                        </p>
                                        @endif
                                    </div>
                                </a>
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
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-data="{ modalOpen: false, evento: {} }"
                         @keydown.escape.window="modalOpen = false">

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
                            @php
                                $tipoInfo   = $evento->tipoEvento();
                                $descripcion = $evento->datos['descripcion'] ?? '';
                                $eventoJson  = json_encode([
                                    'titulo'      => $evento->datos['titulo']      ?? '',
                                    'descripcion' => $descripcion,
                                    'fecha'       => $evento->fecha->translatedFormat('d \d\e F \d\e Y'),
                                    'hora'        => isset($evento->datos['hora']) ? \Carbon\Carbon::parse($evento->datos['hora'])->format('H:i') : null,
                                    'precio'      => $evento->precioEvento(),
                                    'precio_gratis' => ($evento->datos['precio'] ?? 1) == 0,
                                    'url_entradas'=> $evento->datos['url_entradas'] ?? null,
                                    'imagen'      => $evento->imagen ? asset('storage/' . $evento->imagen) : null,
                                    'tipo_emoji'  => $tipoInfo['emoji'],
                                    'tipo_label'  => $tipoInfo['label'],
                                    'destacado'   => $evento->destacado,
                                ]);
                            @endphp
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
                                        @if($descripcion)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $descripcion }}</p>
                                            @if(mb_strlen($descripcion) > 120)
                                            <button type="button"
                                                    @click="evento = {{ $eventoJson }}; modalOpen = true"
                                                    class="mt-1 text-xs font-bold text-blue-600 hover:underline">
                                                Leer más →
                                            </button>
                                            @endif
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

                                        {{-- CAMBIAR CUANDO DECIDAMOS COMO COMPRAR ENTRADAS
                                        inline-flex  --}}
                                        <a href="{{ $evento->datos['url_entradas'] }}" target="_blank" rel="noopener"
                                           class="hidden mt-2 items-center gap-1 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg transition">
                                            {{ __('ui.lugar.comprar_entradas') }}
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Modal evento --}}
                        <div x-show="modalOpen"
                             x-transition.opacity
                             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/60 px-4 pb-4 sm:pb-0"
                             @click.self="modalOpen = false"
                             style="display:none;">

                            <div x-show="modalOpen"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 class="bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">

                                {{-- Imagen --}}
                                <template x-if="evento.imagen">
                                    <div class="w-full bg-black rounded-t-3xl flex items-center justify-center overflow-hidden"
                                         style="max-height: 65vh;">
                                        <img :src="evento.imagen" :alt="evento.titulo"
                                             class="w-full object-contain"
                                             style="max-height: 65vh;">
                                    </div>
                                </template>
                                <template x-if="!evento.imagen">
                                    <div class="w-full h-24 rounded-t-3xl bg-blue-50 flex items-center justify-center text-4xl"
                                         x-text="evento.tipo_emoji"></div>
                                </template>

                                <div class="p-6">
                                    {{-- Badges --}}
                                    <div class="flex flex-wrap gap-1.5 mb-3">
                                        <template x-if="evento.destacado">
                                            <span class="text-[10px] font-black uppercase bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Destacado</span>
                                        </template>
                                        <span class="text-[10px] font-black uppercase bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full"
                                              x-text="evento.tipo_emoji + ' ' + evento.tipo_label"></span>
                                    </div>

                                    {{-- Título --}}
                                    <h3 class="text-xl font-extrabold text-gray-900 leading-tight mb-3"
                                        x-text="evento.titulo"></h3>

                                    {{-- Descripción completa --}}
                                    <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line mb-4"
                                       x-text="evento.descripcion"></p>

                                    {{-- Meta --}}
                                    <div class="flex items-center justify-between flex-wrap gap-2 pt-4 border-t border-gray-100">
                                        <div class="text-sm text-gray-500 space-y-0.5">
                                            <p>📅 <span x-text="evento.fecha"></span></p>
                                            <template x-if="evento.hora">
                                                <p>🕐 <span x-text="evento.hora"></span></p>
                                            </template>
                                        </div>
                                        <span class="text-lg font-extrabold"
                                              :class="evento.precio_gratis ? 'text-green-600' : 'text-blue-700'"
                                              x-text="evento.precio"></span>
                                    </div>

                                    {{-- Botones --}}
                                    <div class="flex gap-3 mt-5">
                                        <template x-if="evento.url_entradas" class="hidden">
                                            {{-- <a :href="evento.url_entradas" target="_blank" rel="noopener"
                                               class="flex-1 text-center text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 px-4 py-2.5 rounded-xl transition">
                                                {{ __('ui.lugar.comprar_entradas') }}
                                            </a> --}}
                                        </template>
                                        <button type="button"
                                                @click="modalOpen = false"
                                                class="flex-1 text-sm font-bold text-gray-600 border border-gray-200 hover:bg-gray-50 px-4 py-2.5 rounded-xl transition">
                                            {{ __('ui.lugar.cerrar') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif

                </div>

                {{-- COLUMNA DERECHA: sidebar --}}
                <aside class="lg:col-span-4">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/50 border border-white lg:sticky lg:top-8">
      
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
                            <div id="mini-mapa" class="h-64 rounded-3xl overflow-hidden border border-gray-100 shadow-inner"></div>
                            <button onclick="window.dispatchEvent(new CustomEvent('geo-modal'))"
                               class="mt-3 flex items-center justify-center gap-2 w-full py-2.5 rounded-2xl text-sm font-bold text-[#fc5648] border border-[#fc5648]/30 hover:bg-[#fff0ef] transition-colors">
                                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                {{ __('ui.lugar.como_llegar') }}
                            </button>
                        @endif

                        {{-- Horario --}}
                        @if($punto->horario)
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h3 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.lugar.horario') }}</h3>
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

                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                    @foreach($cercanos as $cercano)
                    <a href="{{ route('atractivos.show', $cercano->slug ?? $cercano->id) }}"
                       class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col">

                        {{-- Imagen --}}
                        <div class="relative h-36 md:h-36 overflow-hidden bg-gray-100">
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

    {{-- Modal: Cómo llegar con Google Maps --}}
    @if($punto->lat && $punto->lng)
    @php
        $destLat   = $punto->lat;
        $destLng   = $punto->lng;
        $gmapBase  = "https://maps.google.com/maps?output=embed&z=15";
        $gmapDest  = "https://maps.google.com/maps?q={$destLat},{$destLng}&output=embed&z=16";
        $gmapNav   = "https://www.google.com/maps/dir/?api=1&destination={$destLat},{$destLng}&travelmode=walking";
    @endphp
    <div x-data="{
            abierto: false,
            iframeSrc: '',
            estado: 'idle',
            destLat: {{ $destLat }},
            destLng: {{ $destLng }},
            abrir() {
                this.abierto = true;
                document.body.style.overflow = 'hidden';
                this.iframeSrc = '{{ $gmapDest }}';
                this.pedirUbicacion();
            },
            cerrar() {
                this.abierto = false;
                document.body.style.overflow = '';
                this.iframeSrc = '';
                this.estado = 'idle';
            },
            pedirUbicacion() {
                if (!navigator.geolocation) return;
                this.estado = 'cargando';
                const self = this;
                navigator.geolocation.getCurrentPosition(pos => {
                    const uLat = pos.coords.latitude;
                    const uLng = pos.coords.longitude;
                    self.iframeSrc = `https://maps.google.com/maps?saddr=${uLat},${uLng}&daddr=${self.destLat},${self.destLng}&output=embed`;
                    self.estado = 'ok';
                }, () => {
                    self.estado = 'idle';
                }, { enableHighAccuracy: true, timeout: 8000 });
            },
        }"
        @geo-modal.window="abrir()">

        {{-- Backdrop --}}
        <div x-show="abierto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="cerrar()"
             class="fixed inset-0 bg-black/50 z-[200]">
        </div>

        {{-- Bottom sheet --}}
        <div x-show="abierto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 z-[201] bg-white rounded-t-3xl overflow-hidden flex flex-col"
             style="height:85dvh;max-height:700px">

            {{-- Handle --}}
            <div class="flex justify-center pt-3 pb-1 shrink-0">
                <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-start justify-between px-5 py-3 border-b border-gray-100 shrink-0">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">{{ __('ui.lugar.como_llegar') }}</p>
                    <p class="font-extrabold text-gray-900 leading-tight">{{ $punto->title }}</p>
                </div>
                <button @click="cerrar()"
                    class="shrink-0 w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-sm font-bold hover:bg-gray-200 transition ml-4">
                    ✕
                </button>
            </div>

            {{-- Status geolocalización --}}
            <div x-show="estado === 'cargando'" class="px-5 py-2 shrink-0 flex items-center gap-2 text-xs font-semibold text-gray-500">
                <svg class="w-3.5 h-3.5 animate-spin shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                {{ __('ui.general.localizando') }}
            </div>

            {{-- Google Maps iframe: primero destino, luego ruta origen→destino --}}
            <div class="flex-1 min-h-0">
                <iframe
                    x-bind:src="iframeSrc"
                    class="w-full h-full border-0"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            {{-- Footer: botón navegación nativa --}}
            <div class="px-5 py-4 border-t border-gray-100 shrink-0">
                <a href="{{ $gmapNav }}"
                   target="_blank" rel="noopener"
                   class="flex items-center justify-center gap-2 w-full py-3 bg-[#fc5648] text-white text-sm font-bold rounded-2xl shadow hover:bg-[#e04437] transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    Navegar con Google Maps
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- FAB colapsable móvil --}}
    @php
        $hayAcciones = $punto->tieneOfertaActiva() || $punto->tieneMenu()
                    || $punto->tieneCarta()         
                    || $punto->tieneAvisos()
                    || $punto->tienePromociones()
                    || $punto->moduloActivo('exposiciones')
                    || ($punto->moduloActivo('agenda') && $punto->eventosProximos()->count());
    @endphp
    @if($punto->lat && $punto->lng)
    <button onclick="window.dispatchEvent(new CustomEvent('geo-modal'))"
       class="lg:hidden fixed left-4 z-50 flex items-center gap-2 bg-white text-gray-800 text-sm font-bold px-4 py-3 rounded-full shadow-xl border border-gray-200 hover:bg-gray-50 transition"
       style="bottom: calc(80px + var(--inset-bottom, 0px))">
        <svg class="w-4 h-4 text-[#fc5648] shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
        </svg>
        {{ __('ui.lugar.como_llegar') }}
    </button>
    @endif

    @if($hayAcciones)
    <div class="lg:hidden fixed right-4 z-50"
         style="bottom: calc(80px + var(--inset-bottom, 0px))"
         x-data="{ abierto: false }"
         @click.outside="abierto = false">

        {{-- Panel de acciones --}}
        <div x-show="abierto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             x-cloak
             class="absolute bottom-16 right-0 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden min-w-48">

            @if($punto->tieneOfertaActiva())
            <button @click="abierto = false; $dispatch('set-vista', 'oferta')"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left hover:bg-green-50 transition border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                🏷️ {{ __('ui.lugar.tab_oferta') }}
            </button>
            @endif

            @if($punto->tieneMenu())
            <button @click="abierto = false; $dispatch('set-vista', 'menu')"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left hover:bg-orange-50 transition border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0"></span>
                🥘 {{ __('ui.lugar.tab_menu') }}
            </button>
            @endif

            @if($punto->tieneCarta())
            <button @click="abierto = false; $dispatch('set-vista', 'carta')"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left hover:bg-red-50 transition border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-[#fc5648] shrink-0"></span>
                🍽️ {{ __('ui.lugar.tab_carta') }}
            </button>
            @endif

            @if($punto->tieneAvisos())
            <button @click="abierto = false; $dispatch('set-vista', 'avisos')"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left hover:bg-gray-50 transition border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-gray-700 shrink-0"></span>
                {{ __('ui.lugar.tab_avisos') }}
            </button>
            @endif

            @if($punto->tienePromociones())
            <button @click="abierto = false; $dispatch('set-vista', 'promociones')"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left hover:bg-purple-50 transition border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-purple-600 shrink-0"></span>
                {{ __('ui.lugar.tab_promociones') }}
            </button>
            @endif

            @if($punto->moduloActivo('agenda') && $punto->eventosProximos()->count())
            <button @click="abierto = false; $dispatch('set-vista', 'agenda')"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left hover:bg-blue-50 transition border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                {{ __('ui.lugar.tab_agenda') }}
            </button>
            @endif

            {{-- //&& $punto->eventosProximos()->count() --}}
            @if($punto->moduloActivo('exposiciones') )
            <button @click="abierto = false; $dispatch('set-vista', 'exposiciones')"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-left hover:bg-blue-50 transition border-b border-gray-100">
                <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                {{ __('ui.lugar.tab_exposiciones') }}
            </button>
            @endif

        </div>

        {{-- Botón principal --}}
        <button @click="abierto = !abierto"
                class="w-14 h-14 bg-[#fc5648] text-white rounded-full shadow-xl flex items-center justify-center transition-transform duration-300"
                :class="abierto ? 'rotate-45' : ''">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>
    @endif

    <script>
        document.addEventListener('alpine:init', () => {
            window.addEventListener('set-vista', (e) => {
                const main = document.querySelector('main[x-data]');
                if (main && main._x_dataStack) {
                    main._x_dataStack[0].vista = e.detail;
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>

    @if($punto->lat && $punto->lng)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('mini-mapa', {
                zoomControl: true, dragging: true,
                scrollWheelZoom: false, attributionControl: false
            }).setView([{{ $punto->lat }}, {{ $punto->lng }}], 16);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

            const customIcon = L.divIcon({
                className: 'custom-div-icon',
                html: "<div style='background-color:#fc5648;width:16px;height:16px;border-radius:50%;border:3px solid white;box-shadow:0 2px 12px rgba(252,86,72,0.5);'></div>",
                iconSize: [16, 16], iconAnchor: [8, 8]
            });

            L.marker([{{ $punto->lat }}, {{ $punto->lng }}], { icon: customIcon })
                .addTo(map)
                .bindPopup('<strong style="font-size:13px;font-family:Plus Jakarta Sans,sans-serif">{{ addslashes($punto->title) }}</strong>@if($punto->direccion)<br><span style="font-size:11px;color:#6b7280">{{ addslashes($punto->direccion) }}</span>@endif');
        });
    </script>
    @endif

    </div>{{-- /w-full mx-auto --}}
@endsection
