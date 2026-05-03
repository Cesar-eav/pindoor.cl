@extends('layouts.pindoor')

@section('title', 'Registra tu negocio — Pindoor.cl')
@section('canonical', route('publicita.index'))
@section('bodyClass', 'bg-gray-50 text-gray-900 font-sans antialiased')

@section('content')

{{-- ══════════════════════════════════════════
     DESKTOP: ZERO SCROLL (Layout de pantalla fija)
     ══════════════════════════════════════════ --}}
<div class="hidden lg:flex flex-col h-screen overflow-hidden bg-white">

    {{-- Header Compacto --}}
    <header class="shrink-0 bg-slate-900 text-white px-8 py-4 flex items-center justify-between border-b border-white/10" 
            style="background: linear-gradient(135deg, #1a1c1e 0%, #000000 100%)">
        <div class="flex items-center gap-4">
            <div class="bg-[#fc5648] p-2 rounded-lg shadow-lg text-xl">📍</div>
            <div>
                <h1 class="text-xl font-black tracking-tight">
                    Ponle el <span class="text-[#fc5648]">pin</span> a tu negocio — <span class="text-white/70 font-normal">Gratis</span>
                </h1>
                <p class="text-xs text-gray-400">La vitrina más grande de Valparaíso para turistas y locales.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('login') }}" class="text-sm font-bold text-gray-300 hover:text-white transition">Iniciar Sesión</a>
            <a href="{{ route('register') }}"
               class="bg-[#fc5648] hover:bg-[#ff6b5b] text-white font-bold px-6 py-2.5 rounded-xl shadow-[0_8px_15px_rgba(252,86,72,0.3)] transition-all hover:-translate-y-0.5 active:translate-y-0">
                Registrar mi Local →
            </a>
        </div>
    </header>

    <div class="flex flex-1 min-h-0">
        
        {{-- Columna Izquierda: Beneficios y Registro (Scroll Independiente) --}}
        <aside class="w-[35%] shrink-0 flex flex-col border-r border-gray-100 bg-gray-50/30 overflow-y-auto">
            <div class="p-8 space-y-8">
                
                {{-- Beneficios --}}
                <section>
                    <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-[#fc5648] mb-4">¿Por qué Pindoor?</h2>
                    <div class="grid grid-cols-1 gap-3">
                        @foreach([
                            ['📍', 'Presencia en el mapa', 'Geolocalización exacta para turistas.'],
                            ['📸', 'Galería y Horarios', 'Muestra tus mejores fotos y ofertas.'],
                            ['🎯', 'Audiencia Real', 'Llega a quien busca qué hacer ahora.']
                        ] as [$ico, $title, $desc])
                        <div class="flex items-start gap-3 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                            <span class="text-xl">{{ $ico }}</span>
                            <div>
                                <h3 class="text-xs font-bold text-gray-800">{{ $title }}</h3>
                                <p class="text-[11px] text-gray-500 leading-tight">{{ $desc }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </section>

            </div>

            <footer class="mt-auto p-6 text-center">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest italic">© {{ date('Y') }} Pindoor.cl</p>
            </footer>
        </aside>

        {{-- Columna Derecha: El Preview Real (Scroll Independiente) --}}
        <main class="flex-1 overflow-y-auto bg-white p-8">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-900">Así se ve tu local</h2>
                    <p class="text-gray-500 text-sm">Ejemplos ficticios.</p>
                </div>
                <div class="flex gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">En vivo</span>
                </div>
            </div>

            @if(isset($atractivos) && $atractivos->count())
            <div class="grid grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($atractivos as $atractivo)
                <article class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col">
                    <div class="relative overflow-hidden">
                        {{-- ENLACE IMAGEN --}}
                        <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}" class="block">
                            @if($atractivo->imagenPrincipal)
                                <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}" class="w-full h-40 object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-40 bg-gray-100 flex items-center justify-center text-3xl">📍</div>
                            @endif
                        </a>

                        {{-- BADGES --}}
                        @if($atractivo->categoria)
                        <span class="absolute top-3 left-3 bg-[#fc5648] text-white text-[8px] uppercase tracking-widest font-black px-2 py-1 rounded-md">
                            {{ $atractivo->categoria->nombre }}
                        </span>
                        @endif

                        @if($atractivo->es_cliente && $atractivo->oferta_del_dia && $atractivo->oferta_activa)
                        <span class="absolute top-3 right-3 bg-amber-400 text-amber-900 text-[8px] uppercase font-black px-2 py-1 rounded-md shadow-sm">
                            Oferta hoy
                        </span>
                        @endif

                        @if($atractivo->es_cliente && $atractivo->imagen_perfil)
                        <img src="{{ asset('storage/' . $atractivo->imagen_perfil) }}" class="absolute bottom-2 right-2 w-8 h-8 rounded-lg object-cover border-2 border-white shadow-md">
                        @endif
                    </div>

                    <div class="p-4">
                        {{-- ENLACE TÍTULO --}}
                        <h3 class="text-sm font-bold text-gray-900 mb-1">
                            <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}" class="hover:text-[#fc5648] transition">
                                {{ $atractivo->title }}
                            </a>
                        </h3>

                        {{-- SECTOR / DIRECCIÓN --}}
                        <div class="flex items-center gap-1 text-[10px] text-gray-400 font-medium mb-2">
                            <span class="text-[#fc5648]">●</span> {{ $atractivo->sector ?? 'Valparaíso' }}
                        </div>

                        <p class="text-[11px] text-gray-500 leading-relaxed line-clamp-2">
                            {!! \Illuminate\Support\Str::limit(strip_tags($atractivo->description), 80) !!}
                        </p>
                    </div>
                </article>
                @endforeach
            </div>
            @endif
        </main>

    </div>
</div>

{{-- ══════════════════════════════════════════
     MOBILE: SCROLL TRADICIONAL
     ══════════════════════════════════════════ --}}
<div class="lg:hidden flex flex-col bg-white">
    <section class="bg-[#fc5648] text-white py-16 px-6 text-center">
        <h1 class="text-4xl font-black mb-4">Ponle el pin a tu negocio</h1>
        <p class="text-white/90 text-lg mb-8">Únete gratis a la red turística de Valparaíso.</p>
        <a href="{{ route('register') }}" class="inline-block bg-white text-[#fc5648] font-bold px-8 py-4 rounded-2xl shadow-xl">Registro Gratis →</a>
    </section>

    
    {{-- Lista de ejemplos en móvil (seccion simplificada para carga rápida) --}}
    @if(isset($atractivos))
    <section class="p-6">
        <h2 class="text-xl font-bold mb-6">Así se verá tu ficha</h2>
        <div class="space-y-4">
            @foreach($atractivos->take(3) as $atractivo)
            <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}" class="flex bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                <img src="{{ asset('storage/' . ($atractivo->imagenPrincipal->ruta ?? '')) }}" class="w-24 h-24 object-cover shrink-0">
                <div class="p-3">
                    <h3 class="text-sm font-bold leading-tight">{{ $atractivo->title }}</h3>
                    <p class="text-xs text-[#fc5648]">{{ $atractivo->sector }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

</div>

@endsection