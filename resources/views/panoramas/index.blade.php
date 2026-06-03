@extends('layouts.pindoor')

@section('title', 'Panoramas — Pindoor.cl')
@section('canonical', route('atractivos.panoramas'))
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('content')

<div
    class="max-w-5xl mx-auto px-4 py-8"
    x-data="{
        open: false,
        current: 0,
        images: {{ $allImages->toJson() }},
        openAt(i) { this.current = i; this.open = true; history.pushState({ lightbox: true }, ''); },
        prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
        next() { this.current = (this.current + 1) % this.images.length; },
        close() { if (this.open) { this.open = false; history.back(); } }
    }"
    @keydown.escape.window="close()"
    @keydown.arrow-left.window="open && prev()"
    @keydown.arrow-right.window="open && next()"
    @popstate.window="open && (open = false)"
>

    {{-- Header --}}
{{-- Header --}}
<section class="mb-8 text-center max-w-2xl mx-auto px-4">
    <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-950 mb-3 ">
        Pa<span class="text-[#fc5648]">no</span>ra<span class="text-[#fc5648]">ma</span>s
    </h1>
            {{-- <span class="text-[#FF5A43]  font-bold">Valparaíso</span> --}}

    <p class="text-slate-500 text-base md:text-lg font-medium inline-flex flex-wrap items-center justify-center gap-2 bg-slate-50 border border-slate-100 rounded-full px-6 py-2 shadow-sm">
        {{-- <span class="text-slate-300">•</span> --}}
        <span>
            <strong class="text-slate-950 font-extrabold text-2xl align-bottom">{{ $panoramas->count() }}</strong> 
            panoramas en los próximos
        </span>
        <span>
            <strong class="text-[#FF5A43] font-extrabold text-2xl align-bottom ">{{ $limite }}</strong> 
            días
        </span>
            

    </p>
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
        <div id="dias-strip" class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
            @foreach($porDia as $fechaStr => $grupo)
            @php $meta = $diasMeta[$fechaStr]; @endphp
            <a href="#dia-{{ $fechaStr }}"
               data-dia="{{ $fechaStr }}"
               class="dia-pill shrink-0 flex flex-col items-center gap-0.5 px-4 py-2 rounded-2xl text-xs font-bold transition-all
                      bg-white text-gray-500 border border-gray-200 hover:border-gray-400">
                <span class="text-[10px] tracking-widest uppercase leading-none">{{ $meta['label'] }}</span>
                <span class="text-lg leading-tight font-black">{{ $meta['num'] }}</span>
                <span class="text-[9px] leading-none opacity-70">{{ $meta['mes'] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── Secciones por día ───────────────────────────────────────────────── --}}
    @foreach($porDia as $fechaStr => $grupo)
    @php $meta = $diasMeta[$fechaStr]; @endphp

    <section id="dia-{{ $fechaStr }}" data-fecha="{{ $fechaStr }}" class="mb-12">

        {{-- Encabezado del día --}}
        <div class="flex items-center gap-3 mb-5">
            @if($meta['esHoy'])
                <span class="bg-[#fc5648] text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full shadow">HOY</span>
            @elseif($meta['esMana'])
                <span class="bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full">MAÑANA</span>
            @endif
            <h2 class="text-lg font-bold text-gray-800 capitalize">{{ $meta['titulo'] }}</h2>
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-xs text-gray-400 font-semibold">{{ $grupo->count() }} {{ $grupo->count() === 1 ? 'evento' : 'eventos' }}</span>
        </div>

        {{-- Cards del día --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($grupo as $panorama)
            @php $idx = $startIndexMap[$panorama->id] ?? 0; @endphp

            <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-zoom-in
                        {{ $meta['esHoy'] ? 'ring-1 ring-[#fc5648]/20' : '' }}"
                 @click="openAt({{ $idx }})">

                {{-- Imagen --}}
                <div class="relative overflow-hidden bg-gray-100 sm:aspect-3/4 sm:bg-gray-900">
                    @if($panorama->imagen)
                        {{-- Fondo desenfocado (solo escritorio) --}}
                        <img src="{{ asset('storage/' . $panorama->imagen) }}"
                             aria-hidden="true"
                             class="hidden sm:block absolute inset-0 w-full h-full object-cover blur-2xl scale-110 brightness-50 pointer-events-none">
                        {{-- Imagen principal --}}
                        <img src="{{ asset('storage/' . $panorama->imagen) }}"
                             alt="{{ $panorama->titulo }}"
                             class="relative z-10 w-full h-auto block sm:h-full sm:object-contain transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-gray-300">📷</div>
                    @endif

                    {{-- Badge categoría (arriba izquierda) --}}
                    @if($panorama->categoria && isset($categorias[$panorama->categoria]))
                    <div class="absolute top-2 left-2 z-20 bg-black/60 backdrop-blur-sm text-white md:text-sm text-lg font-bold px-2 py-0.5 rounded-lg">
                        {{ $categorias[$panorama->categoria]['emoji'] }} {{ $categorias[$panorama->categoria]['label'] }}
                    </div>
                    @endif

                    {{-- Badge gratuito (arriba derecha) --}}
                    @if($panorama->es_gratuito)
                    <div class="absolute top-2 right-2 z-20 bg-green-500 text-white md:text-[12px] text-[15px] font-bold px-2 py-0.5 rounded-lg">
                        🎟️ Gratis
                    </div>
                    @endif

                    {{-- Badge cantidad de imágenes (abajo izquierda) --}}
                    @php $totalFotos = ($panorama->imagen ? 1 : 0) + $panorama->imagenes->count(); @endphp
                    @if($totalFotos > 1)
                    <div class="absolute bottom-2 left-2 z-20 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $totalFotos }}
                    </div>
                    @endif

                    {{-- Badge hora (abajo derecha) --}}
                    @if($panorama->hora)
                    <div class="absolute bottom-2 right-2 z-20 bg-black/60 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-lg">
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

                    @if($panorama->enlace)
                    <a href="{{ $panorama->enlace }}" target="_blank" rel="noopener noreferrer"
                       @click.stop
                       class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-[#fc5648] hover:text-[#d94439] transition">
                        Más información
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
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
                <a x-show="images[current].enlace"
                   :href="images[current].enlace"
                   target="_blank" rel="noopener noreferrer"
                   class="mt-1 inline-flex items-center gap-1.5 text-xs font-bold bg-white/10 hover:bg-white/20 text-white px-4 py-1.5 rounded-full transition">
                    Más información
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
            <p class="text-white/40 text-sm">
                <span x-text="current + 1"></span> / {{ $allImages->count() }}
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

<script>
(function () {
    const pills    = document.querySelectorAll('.dia-pill');
    const strip    = document.getElementById('dias-strip');
    const secciones = document.querySelectorAll('section[data-fecha]');

    // Guarda el style original de cada pill (colores Blade)
    pills.forEach(p => { p.dataset.origStyle = p.getAttribute('style') || ''; });

    let fechaActiva  = null;
    let ignorarScroll = false; // bloquea el observer mientras scrolleamos por clic

    // ── Pintar pill activa ───────────────────────────────────────────────────
    function marcarPill(fecha) {
        if (fecha === fechaActiva) return;
        fechaActiva = fecha;

        pills.forEach(p => {
            p.style.cssText = p.dataset.dia === fecha
                ? 'background:#fc5648;color:white;border-color:#fc5648;box-shadow:0 4px 12px rgba(252,86,72,.3);'
                : p.dataset.origStyle;
        });

        // Desplaza la pill al centro del strip horizontal (sin afectar el scroll vertical)
        const activa = strip?.querySelector(`.dia-pill[data-dia="${fecha}"]`);
        if (activa && strip) {
            const pillCenter  = activa.offsetLeft + activa.offsetWidth / 2;
            const stripCenter = strip.offsetWidth / 2;
            strip.scrollTo({ left: pillCenter - stripCenter, behavior: 'smooth' });
        }
    }

    // ── Altura de los headers sticky/fixed sobre el contenido ───────────────
    function stickyOffset() {
        const appbar    = document.querySelector('header[class*="sticky"]');
        const stripWrap = strip?.closest('[class*="sticky"]');
        return (appbar?.offsetHeight ?? 0) + (stripWrap?.offsetHeight ?? 0) + 8;
    }

    // ── Clic en pill ────────────────────────────────────────────────────────
    pills.forEach(pill => {
        pill.addEventListener('click', e => {
            e.preventDefault();
            marcarPill(pill.dataset.dia);

            const target = document.getElementById('dia-' + pill.dataset.dia);
            if (!target) return;

            ignorarScroll = true;
            const top = target.getBoundingClientRect().top + window.scrollY - stickyOffset();
            window.scrollTo({ top, behavior: 'smooth' });

            // Reactiva el observer una vez que el scroll suave termina (~700 ms)
            clearTimeout(pill._timer);
            pill._timer = setTimeout(() => { ignorarScroll = false; }, 700);
        });
    });

    // ── IntersectionObserver (scroll manual) ────────────────────────────────
    if (secciones.length) {
        const observer = new IntersectionObserver(entries => {
            if (ignorarScroll) return;

            // De todas las secciones visibles en la zona, tomar la más alta en pantalla
            let mejor = null;
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                if (!mejor || e.boundingClientRect.top < mejor.boundingClientRect.top) mejor = e;
            });
            if (mejor) marcarPill(mejor.target.dataset.fecha);

        }, { rootMargin: '-15% 0px -55% 0px', threshold: 0 });

        secciones.forEach(s => observer.observe(s));
        marcarPill(secciones[0].dataset.fecha);
    }
})();
</script>
@endsection
