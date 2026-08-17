@php use Illuminate\Support\Str; @endphp

@extends('layouts.pindoor')

@section('title', $producto->nombre . ' — ' . $punto->title . ' · Pindoor.cl')

@section('canonical', route('puntos.producto', [$punto->slug ?? $punto->id, $producto->id]))

@section('description', $producto->descripcion
    ? Str::limit($producto->descripcion, 160)
    : 'Producto de ' . $punto->title . ' en Pindoor.cl')

@section('bodyClass', 'bg-[#f9fafb] text-gray-900 leading-relaxed')

@section('head')
    @php
        $canonicalUrl = route('puntos.producto', [$punto->slug ?? $punto->id, $producto->id]);
        $catalogoUrl  = route('puntos.show', $punto->slug ?? $punto->id) . '#catalogo';
    @endphp
    <meta property="og:type"        content="product" />
    <meta property="og:url"         content="{{ $canonicalUrl }}" />
    <meta property="og:title"       content="{{ $producto->nombre }} — {{ $punto->title }}" />
    <meta property="og:description" content="{{ $producto->descripcion ?? 'Producto de ' . $punto->title }}" />
    @if($producto->imagen_url)
    <meta property="og:image"       content="{{ $producto->imagen_url }}" />
    <meta property="og:image:alt"   content="{{ $producto->nombre }}" />
    @endif
    <meta name="twitter:card"        content="summary_large_image" />
    <meta name="twitter:title"       content="{{ $producto->nombre }} — {{ $punto->title }}" />
    <meta name="twitter:description" content="{{ $producto->descripcion ?? 'Producto de ' . $punto->title }}" />
    @if($producto->imagen_url)<meta name="twitter:image" content="{{ $producto->imagen_url }}" />@endif

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ addslashes($producto->nombre) }}",
        @if($producto->descripcion)"description": "{{ addslashes($producto->descripcion) }}",@endif
        @if($producto->imagen_url)"image": "{{ $producto->imagen_url }}",@endif
        @if($producto->precio)"offers": {
            "@type": "Offer",
            "priceCurrency": "CLP",
            "price": "{{ addslashes($producto->precio) }}",
            "availability": "https://schema.org/InStock",
            "seller": { "@type": "LocalBusiness", "name": "{{ addslashes($punto->title) }}" }
        },@endif
        "brand": { "@type": "Brand", "name": "{{ addslashes($punto->title) }}" },
        "breadcrumb": {
            "@type": "BreadcrumbList",
            "itemListElement": [
                {"@type":"ListItem","position":1,"name":"Pindoor","item":"{{ route('puntos.index') }}"},
                {"@type":"ListItem","position":2,"name":"{{ addslashes($punto->title) }}","item":"{{ route('puntos.show', $punto->slug ?? $punto->id) }}"},
                {"@type":"ListItem","position":3,"name":"Catálogo","item":"{{ route('puntos.show', $punto->slug ?? $punto->id) }}"},
                {"@type":"ListItem","position":4,"name":"{{ addslashes($producto->nombre) }}","item":"{{ $canonicalUrl }}"}
            ]
        }
    }
    </script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .serif-text { font-family: 'Lora', serif; }
    </style>
@endsection

@section('content')
<div class="w-full mx-auto">
    <main class="max-w-7xl mx-auto px-4 py-8 md:py-12">

        {{-- Breadcrumbs --}}
        <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-8 overflow-x-auto whitespace-nowrap">
            <a href="{{ route('puntos.index') }}" wire:navigate class="hover:text-pindoor-accent transition">Pindoor</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('puntos.show', $punto->slug ?? $punto->id) }}" class="hover:text-pindoor-accent transition">{{ $punto->title }}</a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('puntos.show', $punto->slug ?? $punto->id) }}?vista=catalogo" class="hover:text-pindoor-accent transition">Catálogo</a>
            <span class="text-gray-300">/</span>
            <span class="text-pindoor-accent truncate max-w-[160px]">{{ $producto->nombre }}</span>
        </nav>

        {{-- Grid principal --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- COLUMNA CENTRAL --}}
            <div class="lg:col-span-8">

                {{-- Imagen del producto --}}
                <div class="mb-8">
                    @if($producto->imagen_url)
                        <div class="aspect-[4/3] md:aspect-[16/10] rounded-3xl overflow-hidden shadow-2xl shadow-gray-200 bg-gray-100">
                            <img src="{{ $producto->imagen_url }}"
                                 alt="{{ $producto->nombre }}"
                                 class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="aspect-[4/3] md:aspect-[16/10] rounded-3xl bg-gray-100 flex items-center justify-center text-7xl shadow-inner">
                            📦
                        </div>
                    @endif
                </div>

                {{-- Ficha del producto --}}
                <section class="space-y-5">
                    {{-- Categoría del negocio --}}
                    @if($punto->categoria)
                    <span class="inline-flex items-center gap-2 bg-pindoor-accent/10 text-pindoor-accent text-[11px] uppercase font-black px-4 py-1.5 rounded-full">
                        {{ $punto->categoria->nombre }}
                    </span>
                    @endif

                    {{-- Nombre --}}
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        {{ $producto->nombre }}
                    </h1>

                    {{-- Precio --}}
                    @if($producto->precio)
                    <div class="inline-flex items-baseline gap-2">
                        <span class="text-3xl font-black text-[#fc5648]">{{ $producto->precio }}</span>
                    </div>
                    @endif

                    {{-- Descripción --}}
                    @if($producto->descripcion)
                    <div class="prose prose-gray max-w-none serif-text text-lg text-gray-700 leading-relaxed border-t border-gray-100 pt-5">
                        {{ $producto->descripcion }}
                    </div>
                    @endif

                    {{-- CTA: volver al catálogo --}}
                    <div class="pt-4 border-t border-gray-100">
                        <a href="{{ route('puntos.show', $punto->slug ?? $punto->id) }}"
                           class="inline-flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-pindoor-accent transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Ver todos los productos de {{ $punto->title }}
                        </a>
                    </div>
                </section>

                {{-- Otros productos del mismo negocio --}}
                @php
                    $otrosProductos = $punto->productos->where('id', '!=', $producto->id)->take(6);
                @endphp
                @if($otrosProductos->count())
                <section class="mt-14 pt-10 border-t border-gray-200">
                    <div class="mb-6">
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-[#fc5648] mb-1">Del mismo local</p>
                        <h2 class="text-xl font-extrabold text-gray-900">Más de {{ $punto->title }}</h2>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach($otrosProductos as $otro)
                        <a href="{{ route('puntos.producto', [$punto->slug ?? $punto->id, $otro->id]) }}"
                           class="group bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                            @if($otro->imagen_url)
                                <img src="{{ $otro->imagen_url }}" alt="{{ $otro->nombre }}"
                                     class="w-full aspect-square object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full aspect-square bg-gray-100 flex items-center justify-center text-4xl">📦</div>
                            @endif
                            <div class="p-3">
                                <p class="font-bold text-gray-900 text-sm leading-tight line-clamp-2 group-hover:text-pindoor-accent transition">{{ $otro->nombre }}</p>
                                @if($otro->precio)
                                <p class="text-sm font-black text-[#fc5648] mt-1">{{ $otro->precio }}</p>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </section>
                @endif

            </div>

            {{-- SIDEBAR DERECHO --}}
            <aside class="lg:col-span-4">
                <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/50 border border-white sticky top-8 space-y-6">

                    {{-- Logo e info del negocio --}}
                    <div class="flex items-center gap-3 pb-6 border-b border-gray-100">
                        @if($punto->imagen_perfil)
                            <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                 alt="Logo {{ $punto->title }}"
                                 class="w-14 h-14 rounded-2xl object-cover border border-gray-100 shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-2xl shrink-0">🏪</div>
                        @endif
                        <div>
                            <p class="font-bold text-gray-900 leading-tight">{{ $punto->title }}</p>
                            @if($punto->categoria)
                            <p class="text-xs text-gray-400">{{ $punto->categoria->nombre }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Ubicación --}}
                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[.2em] text-gray-400 mb-3">Ubicación</h3>
                        <div class="flex items-start gap-3">
                            <div class="bg-pindoor-accent/10 p-2.5 rounded-xl text-pindoor-accent shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $punto->sector }}</p>
                                @if($punto->direccion)
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $punto->direccion }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Horario --}}
                    @if($punto->horario)
                    <div class="pt-5 border-t border-gray-100">
                        <h3 class="text-[10px] font-black uppercase tracking-[.2em] text-gray-400 mb-2">Horario</h3>
                        <p class="text-sm font-medium text-gray-700">{{ $punto->horario }}</p>
                    </div>
                    @endif

                    {{-- Enlace web --}}
                    @if($punto->enlace)
                    <div class="pt-5 border-t border-gray-100">
                        <h3 class="text-[10px] font-black uppercase tracking-[.2em] text-gray-400 mb-2">Más información</h3>
                        <a href="{{ $punto->enlace }}" target="_blank" rel="noopener"
                           class="flex items-center gap-2 text-sm font-bold text-pindoor-accent hover:underline break-all">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            {{ parse_url($punto->enlace, PHP_URL_HOST) ?? $punto->enlace }}
                        </a>
                    </div>
                    @endif

                    {{-- Ir al perfil --}}
                    <div class="pt-5 border-t border-gray-100">
                        <a href="{{ route('puntos.show', $punto->slug ?? $punto->id) }}"
                           class="flex items-center justify-center gap-2 w-full py-3 rounded-2xl bg-gray-900 text-white text-sm font-bold hover:bg-pindoor-accent transition-colors duration-300">
                            Ver perfil completo
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                </div>
            </aside>

        </div>{{-- /grid --}}

    </main>
</div>
@endsection
