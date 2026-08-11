@php
    use Illuminate\Support\Str;
    $seoTitle = $ruta->titulo . ' · Rutas Pindoor | Pindoor';
    $seoDesc  = $ruta->descripcion
        ? Str::limit($ruta->descripcion, 155, '')
        : 'Ruta a pie por Valparaíso con ' . $ruta->puntos->count() . ' paradas, curada por Pindoor.';
@endphp

@extends('layouts.pindoor')

@section('title', $seoTitle)
@section('canonical', route('rutas.show', $ruta->slug))
@section('description', $seoDesc)
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('head')
    @php $canonicalUrl = route('rutas.show', $ruta->slug); @endphp
    <meta property="og:type"        content="article" />
    <meta property="og:url"         content="{{ $canonicalUrl }}" />
    <meta property="og:title"       content="{{ $seoTitle }}" />
    <meta property="og:description" content="{{ $seoDesc }}" />
    @if($ruta->imagen_portada_url)
    <meta property="og:image"       content="{{ $ruta->imagen_portada_url }}" />
    @endif
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:title"       content="{{ $seoTitle }}" />
    <meta name="twitter:description" content="{{ $seoDesc }}" />
    @if($ruta->imagen_portada_url)
    <meta name="twitter:image"       content="{{ $ruta->imagen_portada_url }}" />
    @endif

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "TouristTrip",
        "name": "{{ addslashes($ruta->titulo) }}",
        @if($ruta->descripcion)"description": "{{ addslashes(Str::limit($ruta->descripcion, 300, '')) }}",@endif
        @if($ruta->imagen_portada_url)"image": "{{ $ruta->imagen_portada_url }}",@endif
        "itinerary": [
            @foreach($ruta->puntos as $punto)
            {
                "@type": "TouristAttraction",
                "name": "{{ addslashes($punto->title) }}",
                "url": "{{ route('puntos.show', $punto->slug) }}"
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
    </script>
    @vite('resources/js/leaflet.js')
@endsection

@section('content')
<article class="w-full">

    {{-- Portada --}}
    @if($ruta->imagen_portada_url)
    <div class="w-full max-w-4xl mx-auto px-4 pt-10">
        <div class="aspect-video rounded-3xl overflow-hidden shadow-2xl shadow-gray-200">
            <img src="{{ $ruta->imagen_portada_url }}" alt="{{ $ruta->titulo }}"
                 class="w-full h-full object-cover">
        </div>
    </div>
    @endif

    <div class="max-w-2xl mx-auto px-4 py-10">

        {{-- Breadcrumbs --}}
        <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-8">
            <a href="{{ route('puntos.index') }}" class="hover:text-[#fc5648] transition">Pindoor</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('rutas.index') }}" class="hover:text-[#fc5648] transition">Rutas</a>
            <span class="text-gray-300">/</span>
            <span class="text-[#fc5648] truncate max-w-45">{{ $ruta->titulo }}</span>
        </nav>

        {{-- Meta --}}
        <p class="text-xs font-black uppercase tracking-widest text-[#fc5648] mb-3">
            🗺️ Ruta Pindoor · {{ $ruta->puntos->count() }} {{ $ruta->puntos->count() === 1 ? 'parada' : 'paradas' }}
        </p>

        {{-- Título --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-gray-900
                   tracking-tight leading-[1.1] mb-8">
            {{ $ruta->titulo }}
        </h1>

        {{-- Descripción --}}
        @if($ruta->descripcion)
        <p class="text-xl text-gray-500 leading-relaxed border-l-4 border-[#fc5648] pl-5 mb-10 whitespace-pre-line"
           style="font-family:'Lora',serif; font-style:italic;">
            {{ $ruta->descripcion }}
        </p>
        @endif

        <hr class="border-gray-100 mb-10">

        {{-- Mapa del recorrido --}}
        @php $puntosMapa = $ruta->puntos->filter(fn($p) => $p->lat && $p->lng)->values(); @endphp
        @if($puntosMapa->isNotEmpty())
        <div id="mapa-ruta" class="w-full h-80 rounded-2xl border border-gray-100 shadow-sm mb-10 bg-gray-100"></div>
        @endif

        {{-- Puntos de la ruta, en orden --}}
        <div class="space-y-4">
            @foreach($ruta->puntos as $punto)
            <a href="{{ route('puntos.show', $punto->slug) }}"
               class="group flex items-center gap-4 bg-white rounded-2xl border border-gray-100 shadow-sm
                      hover:shadow-md hover:-translate-y-0.5 transition-all p-4">
                <div class="shrink-0 w-9 h-9 rounded-full bg-[#fc5648] text-white flex items-center justify-center
                            font-black text-sm">
                    {{ $loop->iteration }}
                </div>
                <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                    @if($punto->imagenPrincipal)
                        <img src="{{ asset('storage/' . $punto->imagenPrincipal->ruta) }}" alt="{{ $punto->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-2xl text-gray-300">📍</div>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="font-bold text-gray-800 group-hover:text-[#fc5648] transition truncate">{{ $punto->title }}</p>
                    <p class="text-xs text-gray-400 truncate">
                        {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre }}
                        @if($punto->sector) · {{ $punto->sector }} @endif
                    </p>
                </div>
                <svg class="w-5 h-5 text-gray-300 group-hover:text-[#fc5648] shrink-0 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>

        {{-- Operadores que ofrecen esta ruta --}}
        <div class="mt-14 pt-8 border-t border-gray-100">
            <h3 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4">
                🧭 Disponible con
            </h3>
            @if($ruta->operadores->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($ruta->operadores as $operador)
                <a href="{{ route('operador.show', $operador->slug) }}"
                   class="flex items-center gap-3 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all p-3">
                    <div class="w-14 h-14 rounded-full overflow-hidden bg-gray-100 shrink-0">
                        @if($operador->imagen_perfil)
                            <img src="{{ asset('storage/' . $operador->imagen_perfil) }}" alt="{{ $operador->nombre }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl text-gray-300">🧭</div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-gray-800 text-sm truncate">{{ $operador->nombre }}</p>
                        @if($operador->ciudad)
                        <p class="text-xs text-gray-400 truncate">{{ $operador->ciudad }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="flex items-center gap-3 bg-[#fff0ef] border border-[#fc5648]/20 rounded-2xl p-4">
                <span class="text-2xl shrink-0">🧭</span>
                <p class="text-sm text-gray-600">Pronto habrá una oferta de operadores turísticos para realizar esta ruta.</p>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="mt-16 pt-8 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
            <a href="{{ route('rutas.index') }}"
               class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-[#fc5648] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a rutas
            </a>
            <a href="{{ route('puntos.index') }}"
               class="text-sm font-bold text-[#fc5648] hover:underline">
                Explorar Valparaíso →
            </a>
        </div>
    </div>

</article>
@endsection

@if($puntosMapa->isNotEmpty())
@php
    $puntosMapaJs = $puntosMapa->values()->map(fn($p, $i) => [
        'lat'    => (float) $p->lat,
        'lng'    => (float) $p->lng,
        'title'  => $p->title,
        'orden'  => $i + 1,
        'slug'   => $p->slug,
        'imagen' => $p->imagenPrincipal ? asset('storage/' . $p->imagenPrincipal->ruta) : null,
    ]);
@endphp
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('mapa-ruta');
    if (!el || typeof L === 'undefined') return;

    const puntos = @json($puntosMapaJs);

    delete L.Icon.Default.prototype._getIconUrl;

    const mapa = L.map('mapa-ruta', { attributionControl: false, scrollWheelZoom: false, dragging: false });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, subdomains: 'abcd',
    }).addTo(mapa);

    function iconoNumerado(n) {
        return L.divIcon({
            className: '',
            html: `<div style="background:#fc5648;color:white;width:28px;height:28px;border-radius:50%;
                        display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;
                        font-family:sans-serif;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,.3);">${n}</div>`,
            iconSize:    [28, 28],
            iconAnchor:  [14, 14],
            popupAnchor: [0, -16],
        });
    }

    const latlngs = puntos.map(p => [p.lat, p.lng]);

    L.polyline(latlngs, { color: '#fc5648', weight: 3, opacity: 0.6, dashArray: '6,6' }).addTo(mapa);

    function popupHtml(p) {
        const imgHtml = p.imagen
            ? `<img src="${p.imagen}" style="width:100%;height:110px;object-fit:cover;border-radius:.5rem .5rem 0 0;">`
            : `<div style="width:100%;height:60px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border-radius:.5rem .5rem 0 0;">📍</div>`;
        return `<div style="font-family:sans-serif;">
            ${imgHtml}
            <div style="padding:10px 12px 12px;">
                <div style="font-weight:700;font-size:13px;line-height:1.3;margin-bottom:8px;">${p.orden}. ${p.title}</div>
                <a href="/lugar/${p.slug}" style="background:#fc5648;color:white;font-size:11px;font-weight:700;padding:5px 12px;border-radius:8px;text-decoration:none;display:inline-block;">Ver detalle →</a>
            </div>
        </div>`;
    }

    puntos.forEach(p => {
        L.marker([p.lat, p.lng], { icon: iconoNumerado(p.orden) })
            .bindPopup(popupHtml(p), { maxWidth: 230 })
            .addTo(mapa);
    });

    if (latlngs.length === 1) {
        mapa.setView(latlngs[0], 16);
    } else {
        mapa.fitBounds(latlngs, { padding: [30, 30] });
    }
});
</script>
@endsection
@endif
