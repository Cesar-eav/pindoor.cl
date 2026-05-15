@extends('layouts.pindoor')

@section('title', 'Panoramas — La Brújula de Valparaíso')
@section('canonical', route('atractivos.panoramas'))
@section('bodyClass', 'bg-gray-100 text-gray-900 font-serif')

@section('content')
@php
    use Carbon\Carbon;

    $hoy    = Carbon::today();
    $manana = Carbon::tomorrow();

    $categorias  = \App\Models\Panorama::CATEGORIAS;
    $catActiva = request('categoria');
    $coleccion = match(true) {
        $catActiva === 'gratuito' => $panoramas->where('es_gratuito', true),
        (bool) $catActiva        => $panoramas->where('categoria', $catActiva),
        default                  => $panoramas,
    };

    // Agrupar por fecha (string YYYY-MM-DD como clave)
    $porDia = $coleccion->groupBy(fn($p) => $p->fecha->toDateString());

    // Todos los ítems indexados para el lightbox (filtrados)
    $items = $coleccion->values();

    // Mapa fecha → índice inicial en $items (para el lightbox desde el strip)
    $indicesPorDia = [];
    foreach ($porDia as $fecha => $grupo) {
        $indicesPorDia[$fecha] = $items->search(fn($p) => $p->fecha->toDateString() === $fecha);
    }

    function etiquetaDia($fecha, $hoy, $manana): string {
        if ($fecha->isSameDay($hoy))    return 'HOY';
        if ($fecha->isSameDay($manana)) return 'MAÑANA';
        return mb_strtoupper($fecha->translatedFormat('D'));
    }

    function tituloDia($fecha, $hoy, $manana): string {
        if ($fecha->isSameDay($hoy))    return 'Hoy · ' . $fecha->translatedFormat('l j \d\e F');
        if ($fecha->isSameDay($manana)) return 'Mañana · ' . $fecha->translatedFormat('l j \d\e F');
        return ucfirst($fecha->translatedFormat('l j \d\e F'));
    }
@endphp

<div
    class="max-w-5xl mx-auto px-4 py-8"
    x-data="{
        open: false,
        current: 0,
        images: {{ $items->map(fn($p) => [
            'src'      => $p->imagen ? asset('storage/'.$p->imagen) : null,
            'titulo'   => $p->titulo,
            'ubicacion'=> $p->ubicacion,
            'fecha'    => $p->fecha?->translatedFormat('l j \d\e F \d\e Y'),
            'hora'     => $p->hora,
        ])->toJson() }},
        openAt(i) { this.current = i; this.open = true; },
        prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
        next() { this.current = (this.current + 1) % this.images.length; },
        close() { this.open = false; }
    }"
    @keydown.escape.window="close()"
    @keydown.arrow-left.window="open && prev()"
    @keydown.arrow-right.window="open && next()"
>

    {{-- Header --}}
    <section class="mb-6 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-2">🧭 Panoramas</h1>
        <p class="text-gray-500 text-base">Valparaíso · próximos 15 días</p>
    </section>

    {{-- Filtro de categorías --}}
    @if($panoramas->isNotEmpty())
    <div class="flex flex-wrap gap-2 justify-center mb-6">
        <a href="{{ route('atractivos.panoramas') }}"
           class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                  {{ !$catActiva ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
            Todos
        </a>
        @foreach($categorias as $slug => $cat)
            @if($panoramas->where('categoria', $slug)->isNotEmpty())
            <a href="{{ route('atractivos.panoramas', ['categoria' => $slug]) }}"
               class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                      {{ $catActiva === $slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                {{ $cat['emoji'] }} {{ $cat['label'] }}
            </a>
            @endif
        @endforeach
        @if($panoramas->where('es_gratuito', true)->isNotEmpty())
        <a href="{{ route('atractivos.panoramas', ['categoria' => 'gratuito']) }}"
           class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                  {{ $catActiva === 'gratuito' ? 'bg-green-700 text-white border-green-700' : 'bg-green-50 text-green-700 border-green-200 hover:border-green-500' }}">
            🎟️ Gratis
        </a>
        @endif
    </div>
    @endif

    @if($panoramas->isEmpty())
        <div class="text-center py-20">
            <div class="text-5xl mb-3">📭</div>
            <p class="font-bold text-gray-700 mb-1">No hay panoramas publicados aún</p>
            <p class="text-sm text-gray-400">Vuelve pronto para ver las novedades.</p>
        </div>
    @else

    {{-- ── Strip de días navegable ─────────────────────────────────────────── --}}
    <div class="sticky top-14 md:top-0 z-20 bg-gray-100/90 backdrop-blur-sm py-3 mb-8 -mx-4 px-4">
        <div class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
            @foreach($porDia as $fechaStr => $grupo)
            @php
                $fecha  = Carbon::parse($fechaStr);
                $esHoy  = $fecha->isSameDay($hoy);
                $esMana = $fecha->isSameDay($manana);
                $label  = etiquetaDia($fecha, $hoy, $manana);
                $num    = $fecha->format('j');
            @endphp
            <a href="#dia-{{ $fechaStr }}"
               class="flex-shrink-0 flex flex-col items-center gap-0.5 px-4 py-2 rounded-2xl text-xs font-bold transition-all
                      {{ $esHoy  ? 'bg-[#fc5648] text-white shadow-md shadow-[#fc5648]/30' :
                         ($esMana ? 'bg-gray-900 text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-400') }}">
                <span class="text-[10px] tracking-widest uppercase leading-none">{{ $label }}</span>
                <span class="text-lg leading-tight font-black">{{ $num }}</span>
                <span class="text-[9px] leading-none opacity-70">
                    {{ mb_strtoupper($fecha->translatedFormat('M')) }}
                </span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── Secciones por día ───────────────────────────────────────────────── --}}
    @foreach($porDia as $fechaStr => $grupo)
    @php
        $fecha       = Carbon::parse($fechaStr);
        $esHoy       = $fecha->isSameDay($hoy);
        $esMana      = $fecha->isSameDay($manana);
        $tituloSeccion = tituloDia($fecha, $hoy, $manana);
        $indiceBase  = $indicesPorDia[$fechaStr];
    @endphp

    <section id="dia-{{ $fechaStr }}" class="mb-12 scroll-mt-36 md:scroll-mt-20">

        {{-- Encabezado del día --}}
        <div class="flex items-center gap-3 mb-5">
            @if($esHoy)
                <span class="bg-[#fc5648] text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow">HOY</span>
            @elseif($esMana)
                <span class="bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">MAÑANA</span>
            @endif
            <h2 class="text-lg font-bold text-gray-800 capitalize">{{ $tituloSeccion }}</h2>
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-xs text-gray-400 font-semibold">{{ $grupo->count() }} {{ $grupo->count() === 1 ? 'evento' : 'eventos' }}</span>
        </div>

        {{-- Cards del día --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($grupo as $panorama)
            @php $idx = $indiceBase + $loop->index; @endphp

            <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-zoom-in
                        {{ $esHoy ? 'ring-1 ring-[#fc5648]/20' : '' }}"
                 @click="openAt({{ $idx }})">

                {{-- Imagen --}}
                <div class="relative aspect-4/5 overflow-hidden bg-gray-100">
                    @if($panorama->imagen)
                        <img src="{{ asset('storage/' . $panorama->imagen) }}"
                             alt="{{ $panorama->titulo }}"
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-300">📷</div>
                    @endif

                    {{-- Badge categoría (arriba izquierda) --}}
                    @if($panorama->categoria && isset($categorias[$panorama->categoria]))
                    <div class="absolute top-2 left-2 bg-black/60 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-lg">
                        {{ $categorias[$panorama->categoria]['emoji'] }} {{ $categorias[$panorama->categoria]['label'] }}
                    </div>
                    @endif

                    {{-- Badge gratuito (arriba derecha) --}}
                    @if($panorama->es_gratuito)
                    <div class="absolute top-2 right-2 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-lg">
                        🎟️ Gratis
                    </div>
                    @endif

                    {{-- Badge hora (abajo derecha) --}}
                    @if($panorama->hora)
                    <div class="absolute bottom-2 right-2 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-lg">
                        🕐 {{ $panorama->hora }}
                    </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4">
                    <p class="font-bold text-gray-900 leading-snug mb-2">{{ $panorama->titulo }}</p>

                    {{-- Rango de fechas --}}
                    @if($panorama->fecha_fin && !$panorama->fecha->isSameDay($panorama->fecha_fin))
                    @php
                        $mismoMes = $panorama->fecha->month === $panorama->fecha_fin->month;
                    @endphp
                    <p class="text-xs text-[#fc5648] font-semibold mb-1">
                        📅 {{ $panorama->fecha->translatedFormat($mismoMes ? 'j' : 'j \d\e F') }}
                           al {{ $panorama->fecha_fin->translatedFormat('j \d\e F') }}
                    </p>
                    @endif

                    @if($panorama->ubicacion)
                    <p class="text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-3 h-3 shrink-0 text-[#fc5648]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $panorama->ubicacion }}
                    </p>
                    @endif
                </div>
            </div>

            @endforeach
        </div>
    </section>
    @endforeach

    {{-- ── Lightbox ─────────────────────────────────────────────────────────── --}}
    <div x-show="open" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
         @click.self="close()" style="display:none;">

        <button @click="close()"
                class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/25 rounded-full p-2.5 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <button @click="prev()"
                class="absolute left-3 md:left-6 text-white bg-white/10 hover:bg-white/25 rounded-full p-3 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="flex flex-col items-center gap-4 px-16 max-w-2xl w-full">
            <template x-if="images[current].src">
                <img :src="images[current].src" :alt="images[current].titulo"
                     class="max-h-[70vh] w-auto object-contain rounded-xl shadow-2xl select-none">
            </template>
            <div class="text-center text-white space-y-1">
                <p class="font-bold text-lg" x-text="images[current].titulo"></p>
                <p class="text-sm text-white/70" x-show="images[current].ubicacion"
                   x-text="'📍 ' + images[current].ubicacion"></p>
                <div class="flex items-center justify-center gap-3 text-sm text-white/70">
                    <span x-show="images[current].fecha" x-text="'📅 ' + images[current].fecha"></span>
                    <span x-show="images[current].hora"  x-text="'🕐 ' + images[current].hora"></span>
                </div>
            </div>
            <p class="text-white/40 text-sm">
                <span x-text="current + 1"></span> / {{ $items->count() }}
            </p>
        </div>

        <button @click="next()"
                class="absolute right-3 md:right-6 text-white bg-white/10 hover:bg-white/25 rounded-full p-3 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    @endif
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
