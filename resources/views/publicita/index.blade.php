@extends('layouts.pindoor')

@section('title', 'Regístrate — Pindoor.cl')
@section('description', 'Únete a Pindoor: registra tu espacio, súmate como artista o agrupación cultural, o registra tu operador turístico en Valparaíso.')
@section('canonical', route('publicita.index'))
@section('bodyClass', 'bg-gray-50 text-gray-900 font-sans antialiased')

@section('content')

<div class="min-h-screen">

    {{-- Header --}}
    <header class="bg-gray-900 text-white px-6 py-14 text-center"
            style="background: linear-gradient(135deg, #1a1c1e 0%, #000000 100%)">
        <div class="max-w-xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-white/70 mb-5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#fc5648] animate-pulse inline-block"></span>
                Únete a Pindoor
            </div>
            <h1 class="text-4xl md:text-5xl font-black leading-tight mb-4">
                Sé parte de la <span class="text-[#fc5648]">red</span> de Valparaíso
            </h1>
            <p class="text-white/60 text-base md:text-lg mb-6">
                Elige cómo quieres aparecer en la guía turística y cultural más completa de la ciudad. Es gratis.
            </p>
            <p class="text-white text-sm">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="font-bold text-white underline underline-offset-2 hover:text-white/80 transition">Inicia sesión</a>
            </p>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-12">

        {{-- Las 3 opciones de registro --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 -mt-20 mb-16">

            {{-- Espacios --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden flex flex-col">
                <div class="px-6 py-5 flex items-center gap-3" style="background: linear-gradient(135deg, #fc5648 0%, #d94035 100%)">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">📍</div>
                    <h2 class="text-white font-black text-lg leading-tight">Tengo un espacio</h2>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-sm text-gray-500 mb-5">Restaurante · Cafetería ·  Museo · Bar · Tienda · Centro cultural · Hostal </p>
                    <ul class="space-y-2 mb-6 text-xs text-gray-600">
                        <li class="flex items-center gap-2"><span class="text-[#fc5648]">✓</span> Presencia en el mapa</li>
                        <li class="flex items-center gap-2"><span class="text-[#fc5648]">✓</span> Galería, horarios y ofertas del día</li>
                        <li class="flex items-center gap-2"><span class="text-[#fc5648]">✓</span> Llega a turistas y locales</li>
                    </ul>
                    <a href="{{ route('register') }}"
                       class="mt-auto text-center bg-[#fc5648] hover:bg-[#ff6b5b] text-white font-bold px-6 py-3.5 rounded-xl shadow-lg transition-all hover:-translate-y-0.5 active:translate-y-0 text-sm">
                        Registrar mi espacio →
                    </a>
                </div>
            </div>

            {{-- Artistas --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden flex flex-col">
                <div class="px-6 py-5 flex items-center gap-3" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">🎨</div>
                    <h2 class="text-white font-black text-lg leading-tight">Soy artista o agrupación artística</h2>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-sm text-gray-500 mb-5">Música · Artes visuales · Teatro · Danza · Fotografía · Artesanía · Cine</p>
                    <ul class="space-y-2 mb-6 text-xs text-gray-600">
                        <li class="flex items-center gap-2"><span class="text-violet-600">✓</span> Vitrina pública con tu obra</li>
                        <li class="flex items-center gap-2"><span class="text-violet-600">✓</span> Apareces en La Escena</li>
                        <li class="flex items-center gap-2"><span class="text-violet-600">✓</span> Te encuentran para contratarte</li>
                    </ul>
                    <a href="{{ route('artista.register') }}"
                       class="mt-auto text-center text-white font-bold px-6 py-3.5 rounded-xl shadow-lg transition-all hover:-translate-y-0.5 active:translate-y-0 text-sm"
                       style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)">
                        Unirme como artista →
                    </a>
                </div>
            </div>

            {{-- Operadores turísticos --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-xl overflow-hidden flex flex-col">
                <div class="px-6 py-5 flex items-center gap-3" style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%)">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">🧭</div>
                    <h2 class="text-white font-black text-lg leading-tight">Soy operador turístico</h2>
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <p class="text-sm text-gray-500 mb-5">Tours a pie · City tours · Excursiones · Traslados · Experiencias guiadas</p>
                    <ul class="space-y-2 mb-6 text-xs text-gray-600">
                        <li class="flex items-center gap-2"><span class="text-teal-600">✓</span> Perfil con tus servicios</li>
                        <li class="flex items-center gap-2"><span class="text-teal-600">✓</span> Visible para turistas</li>
                        <li class="flex items-center gap-2"><span class="text-teal-600">✓</span> Muestra tus rutas  </li>
                    </ul>
                    <a href="{{ route('operador.register') }}"
                       class="mt-auto text-center text-white font-bold px-6 py-3.5 rounded-xl shadow-lg transition-all hover:-translate-y-0.5 active:translate-y-0 text-sm"
                       style="background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%)">
                        Registrar mi operador →
                    </a>
                </div>
            </div>

        </div>

        {{-- Vitrina de ejemplos --}}
        @if(isset($atractivos) && $atractivos->count())
        <section>
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-900">Así se ve en Pindoor</h2>
                    <p class="text-gray-500 text-sm">Locales ficticios a modo de ejemplo</p>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">En vivo</span>
                </div>
            </div>

            {{-- Mobile: scroll horizontal --}}
            <div class="sm:hidden flex gap-3 overflow-x-auto pb-2 -mx-4 px-4 no-scrollbar">
                @foreach($atractivos->take(6) as $atractivo)
                <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                   class="flex-none w-40 bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
                    @if($atractivo->imagenPrincipal)
                        <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                             alt="{{ $atractivo->title }}" class="w-full h-28 object-cover">
                    @else
                        <div class="w-full h-28 bg-gray-100 flex items-center justify-center text-2xl">📍</div>
                    @endif
                    <div class="p-2.5">
                        <h3 class="text-xs font-bold leading-tight line-clamp-2">{{ $atractivo->title }}</h3>
                        @if($atractivo->sector)
                        <p class="text-[10px] text-[#fc5648] mt-0.5">{{ $atractivo->sector }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Desktop: grid --}}
            <div class="hidden sm:grid sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($atractivos->take(8) as $atractivo)
                <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
                   class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300">
                    <div class="relative overflow-hidden">
                        @if($atractivo->imagenPrincipal)
                            <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                                 alt="{{ $atractivo->title }}"
                                 class="w-full h-36 object-cover group-hover:scale-110 transition-transform duration-700">
                        @else
                            <div class="w-full h-36 bg-gray-100 flex items-center justify-center text-3xl">📍</div>
                        @endif
                        @if($atractivo->categoria)
                        <span class="absolute top-3 left-3 bg-[#fc5648] text-white text-[8px] uppercase tracking-widest font-black px-2 py-1 rounded-md">
                            {{ $atractivo->categoria->nombre }}
                        </span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="text-sm font-bold text-gray-900 mb-1 group-hover:text-[#fc5648] transition">{{ $atractivo->title }}</h3>
                        <div class="flex items-center gap-1 text-[10px] text-gray-400 font-medium">
                            <span class="text-[#fc5648]">●</span> {{ $atractivo->sector ?? 'Valparaíso' }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif

    </main>
</div>

@section('scripts')
<style>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection

@endsection
