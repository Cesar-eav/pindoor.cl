@extends('layouts.pindoor')

@section('title', 'Panoramas — Pindoor.cl')
@section('canonical', route('atractivos.panoramas'))
@section('bodyClass', 'bg-gray-100 text-gray-900')

@section('content')

<div
    class="max-w-5xl mx-auto px-4 pt-3 pb-8"
    x-data="{
        open: false,
        current: 0,
        images: {{ $allImages->toJson() }},
        get img() { return this.images[this.current] || {}; },
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

    {{-- Header compacto --}}
<div class="flex items-center justify-between mb-3">
    <h1 class="text-2xl font-extrabold tracking-tight text-slate-950">
        Pa<span class="text-[#fc5648]">no</span>ra<span class="text-[#fc5648]">ma</span>s
    </h1>
    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
        <span><strong class="text-slate-900 font-extrabold text-sm">{{ $panoramas->where('categoria','!=','exposicion')->count() }}</strong> eventos</span>
        @if($exposiciones->isNotEmpty())
        <span class="text-slate-300">·</span>
        <span><strong class="text-slate-900 font-extrabold text-sm">{{ $exposiciones->count() }}</strong> en cartelera</span>
        @endif
    </div>
</div>
    {{-- Filtro de categorías --}}
    @if($panoramas->isNotEmpty())
    <div class="relative mb-6">
        <button onclick="document.getElementById('cats-scroll').scrollBy({left:-200,behavior:'smooth'})"
                class="hidden md:flex absolute left-0 top-0 bottom-0 z-10 items-center pr-4
                       bg-linear-to-r from-gray-100 via-gray-100/80 to-transparent">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button onclick="document.getElementById('cats-scroll').scrollBy({left:200,behavior:'smooth'})"
                class="hidden md:flex absolute right-0 top-0 bottom-0 z-10 items-center pl-4
                       bg-linear-to-l from-gray-100 via-gray-100/80 to-transparent">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
    <div id="cats-scroll" class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
        <a href="{{ route('atractivos.panoramas') }}"
           class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                  {{ !$catActiva ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
            Todos
        </a>
        @foreach($categorias as $slug => $cat)
            @if($panoramas->where('categoria', $slug)->isNotEmpty())
            <a href="{{ route('atractivos.panoramas', ['categoria' => $slug]) }}"
               class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                      {{ $catActiva === $slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                {{ $cat['emoji'] }} {{ $cat['label'] }}
            </a>
            @endif
        @endforeach
        @if($panoramas->where('es_gratuito', true)->isNotEmpty())
        <a href="{{ route('atractivos.panoramas', ['categoria' => 'gratuito']) }}"
           class="shrink-0 px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                  {{ $catActiva === 'gratuito' ? 'bg-green-700 text-white border-green-700' : 'bg-green-50 text-green-700 border-green-200 hover:border-green-500' }}">
            🎟️ Gratis
        </a>
        @endif
    </div>
    </div>
    @endif

    @if($panoramas->isEmpty())
        <div class="text-center py-20">
            <div class="text-5xl mb-3">📭</div>
            <p class="font-bold text-gray-700 mb-1">No hay panoramas publicados aún</p>
            <p class="text-sm text-gray-400">Vuelve pronto para ver las novedades.</p>
        </div>
    @else

    {{-- ── En cartelera (exposiciones) ────────────────────────────────────── --}}
    @if($exposiciones->isNotEmpty() && (!$catActiva || $catActiva === 'exposicion'))
    <section class="mb-10">
        <div class="flex items-center gap-3 mb-4">
            <span class="text-lg">🎭</span>
            <h2 class="text-base font-extrabold text-gray-900 tracking-tight">En cartelera</h2>
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-xs text-gray-400 font-semibold">{{ $exposiciones->count() }} {{ $exposiciones->count() === 1 ? 'exposición' : 'exposiciones' }}</span>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-2" style="scrollbar-width:none">
            @foreach($exposiciones as $exp)
            @php
                $hoyExp   = \Carbon\Carbon::today();
                $iniciada = $exp->fecha->lte($hoyExp);
                $hasta    = $exp->fecha_fin ?? $exp->fecha;
                $diasRest = $hoyExp->diffInDays($hasta, false);
            @endphp
            <a href="{{ $exp->enlace ?: route('atractivos.panoramas') }}"
               @if($exp->enlace) target="_blank" rel="noopener noreferrer" @endif
               class="group relative block shrink-0 rounded-2xl overflow-hidden shadow-sm bg-gray-900" style="width:15rem;height:15rem;">

                @if($exp->imagen)
                    <img src="{{ asset('storage/' . $exp->imagen) }}"
                         alt="{{ $exp->titulo }}"
                         class="absolute inset-0 w-full h-full object-cover opacity-75 group-hover:opacity-90 group-hover:scale-105 transition duration-500">
                @endif
                <div class="absolute inset-0 bg-linear-to-t from-black/90 via-black/30 to-transparent"></div>

                {{-- Badge vigencia --}}
                <div class="absolute top-3 left-3 z-10">
                    @if($diasRest <= 7 && $diasRest >= 0)
                        <span class="bg-[#fc5648] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                            Últimos {{ $diasRest === 0 ? 'hoy' : ($diasRest === 1 ? '1 día' : $diasRest.' días') }}
                        </span>
                    @elseif(!$iniciada)
                        <span class="bg-gray-800/80 text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                            Próximamente
                        </span>
                    @else
                        <span class="bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                            En curso
                        </span>
                    @endif
                    @if($exp->es_gratuito)
                        <span class="ml-1 bg-green-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full">Gratis</span>
                    @endif
                </div>

                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <p class="text-[11px] font-bold text-white/60 uppercase tracking-widest mb-1">
                        @if($iniciada)
                            Hasta el {{ $hasta->translatedFormat('j \d\e F') }}
                        @else
                            Del {{ $exp->fecha->translatedFormat('j \d\e F') }} al {{ $hasta->translatedFormat('j \d\e F') }}
                        @endif
                    </p>
                    <h3 class="font-extrabold text-white leading-snug line-clamp-2">{{ $exp->titulo }}</h3>
                    @if($exp->ubicacion)
                    <p class="text-xs text-white/60 mt-1.5 flex items-center gap-1">
                        <svg class="w-3 h-3 shrink-0 text-[#fc5648]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $exp->ubicacion }}
                    </p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ── Strips de navegación (meses + días) ───────────────────────────── --}}
    <div class="sticky top-12 md:top-0 z-20 bg-gray-100 -mx-4 px-4 pb-2 pt-2 mb-6 space-y-1.5">

        {{-- Strip de meses --}}
        @if(count($mesesMeta) > 1)
        <div id="meses-strip" class="flex gap-2 overflow-x-auto no-scrollbar">
            @foreach($mesesMeta as $mesKey => $mes)
            <a href="#mes-{{ $mesKey }}"
               data-mes="{{ $mesKey }}"
               class="mes-pill shrink-0 px-4 py-1.5 rounded-full text-xs font-bold transition-all border
                      bg-white text-gray-500 border-gray-200 hover:border-gray-400 whitespace-nowrap">
                {{ $mes['label'] }}
            </a>
            @endforeach
        </div>
        @endif

        {{-- Strip de días --}}
        <div id="dias-strip" class="flex gap-2 overflow-x-auto no-scrollbar pb-1">
            @foreach($porDia as $fechaStr => $grupo)
            @php $meta = $diasMeta[$fechaStr]; @endphp
            <a href="#dia-{{ $fechaStr }}"
               data-dia="{{ $fechaStr }}"
               data-mes="{{ $mesPorDia[$fechaStr] }}"
               class="dia-pill shrink-0 flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-2xl text-xs font-bold transition-all
                      bg-white text-gray-500 border border-gray-200 hover:border-gray-400">
                <span class="text-[9px] tracking-widest uppercase leading-none">{{ $meta['label'] }}</span>
                <span class="text-base leading-tight font-black">{{ $meta['num'] }}</span>
                <span class="text-[9px] leading-none opacity-70">{{ $meta['mes'] }}</span>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ── Secciones por día (agrupadas por mes) ─────────────────────────── --}}
    @php $mesPrevio = null; @endphp
    @foreach($porDia as $fechaStr => $grupo)
    @php $meta = $diasMeta[$fechaStr]; $mesActual = $mesPorDia[$fechaStr]; @endphp

    @if($mesActual !== $mesPrevio)
    <div id="mes-{{ $mesActual }}" data-mes="{{ $mesActual }}"
         class="mes-seccion flex items-center gap-3 mb-5 {{ $mesPrevio ? 'mt-10' : '' }}">
        <span class="text-sm font-extrabold text-gray-400 uppercase tracking-widest">{{ $mesesMeta[$mesActual]['titulo'] }}</span>
        <div class="flex-1 h-px bg-gray-300"></div>
    </div>
    @php $mesPrevio = $mesActual; @endphp
    @endif

    <section id="dia-{{ $fechaStr }}" data-fecha="{{ $fechaStr }}" data-mes="{{ $mesActual }}" class="mb-12 z-50">

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
                    <div class="absolute top-2 left-2 z-10 bg-black/60 backdrop-blur-sm text-white md:text-sm text-lg font-bold px-2 py-0.5 rounded-lg">
                        {{ $categorias[$panorama->categoria]['emoji'] }} {{ $categorias[$panorama->categoria]['label'] }}
                    </div>
                    @endif

                    {{-- Badge gratuito (arriba derecha) --}}
                    @if($panorama->es_gratuito)
                    <div class="absolute top-2 right-2 z-10 bg-green-500 text-white md:text-[12px] text-[15px] font-bold px-2 py-0.5 rounded-lg">
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
                    <div class="absolute bottom-2 right-2 z-10 bg-black/60 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-lg">
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
            <template x-if="img.src">
                <img :src="img.src" :alt="img.titulo"
                     class="max-h-[70vh] w-auto object-contain rounded-xl shadow-2xl select-none">
            </template>
            <div class="text-center text-white space-y-1">
                <p class="font-bold text-lg" x-text="img.titulo"></p>
                <p class="text-sm text-white/70" x-show="img.ubicacion"
                   x-text="'📍 ' + img.ubicacion"></p>
                <div class="flex items-center justify-center gap-3 text-sm text-white/70">
                    <span x-show="img.fecha" x-text="'📅 ' + img.fecha"></span>
                    <span x-show="img.hora"  x-text="'🕐 ' + img.hora"></span>
                </div>
                <a x-show="img.enlace"
                   :href="img.enlace"
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
    const diaPills   = document.querySelectorAll('.dia-pill');
    const mesPills   = document.querySelectorAll('.mes-pill');
    const diaStrip   = document.getElementById('dias-strip');
    const mesStrip   = document.getElementById('meses-strip');
    const secciones  = document.querySelectorAll('section[data-fecha]');

    diaPills.forEach(p => { p.dataset.origStyle = p.getAttribute('style') || ''; });
    mesPills.forEach(p => { p.dataset.origStyle = p.getAttribute('style') || ''; });

    let fechaActiva = null;
    let mesActivo   = null;
    let ignorar     = false;

    function stickyOffset() {
        const appbar   = document.querySelector('header[class*="sticky"]');
        const navStrip = diaStrip?.closest('[class*="sticky"]');
        return (appbar?.offsetHeight ?? 0) + (navStrip?.offsetHeight ?? 0) + 8;
    }

    function marcarMes(mes) {
        if (mes === mesActivo) return;
        mesActivo = mes;
        mesPills.forEach(p => {
            p.style.cssText = p.dataset.mes === mes
                ? 'background:#1f2937;color:white;border-color:#1f2937;'
                : p.dataset.origStyle;
        });
        const activaMes = mesStrip?.querySelector(`.mes-pill[data-mes="${mes}"]`);
        if (activaMes && mesStrip) {
            const c = activaMes.offsetLeft + activaMes.offsetWidth / 2;
            mesStrip.scrollTo({ left: c - mesStrip.offsetWidth / 2, behavior: 'smooth' });
        }
    }

    function marcarDia(fecha) {
        if (fecha === fechaActiva) return;
        fechaActiva = fecha;
        diaPills.forEach(p => {
            p.style.cssText = p.dataset.dia === fecha
                ? 'background:#fc5648;color:white;border-color:#fc5648;box-shadow:0 4px 12px rgba(252,86,72,.3);'
                : p.dataset.origStyle;
        });
        const activaDia = diaStrip?.querySelector(`.dia-pill[data-dia="${fecha}"]`);
        if (activaDia && diaStrip) {
            const c = activaDia.offsetLeft + activaDia.offsetWidth / 2;
            diaStrip.scrollTo({ left: c - diaStrip.offsetWidth / 2, behavior: 'smooth' });
        }
        // Sincronizar mes activo con el día
        const mes = activaDia?.dataset.mes;
        if (mes) marcarMes(mes);
    }

    // Clic en pill de día
    diaPills.forEach(pill => {
        pill.addEventListener('click', e => {
            e.preventDefault();
            marcarDia(pill.dataset.dia);
            const target = document.getElementById('dia-' + pill.dataset.dia);
            if (!target) return;
            ignorar = true;
            window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - stickyOffset(), behavior: 'smooth' });
            clearTimeout(pill._t);
            pill._t = setTimeout(() => { ignorar = false; }, 700);
        });
    });

    // Clic en pill de mes → salta al primer día de ese mes
    mesPills.forEach(pill => {
        pill.addEventListener('click', e => {
            e.preventDefault();
            marcarMes(pill.dataset.mes);
            // Encontrar el primer día de ese mes en el strip
            const primerDia = diaStrip?.querySelector(`.dia-pill[data-mes="${pill.dataset.mes}"]`);
            if (primerDia) {
                marcarDia(primerDia.dataset.dia);
                const target = document.getElementById('dia-' + primerDia.dataset.dia);
                if (target) {
                    ignorar = true;
                    window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - stickyOffset(), behavior: 'smooth' });
                    setTimeout(() => { ignorar = false; }, 700);
                }
            }
        });
    });

    // IntersectionObserver
    if (secciones.length) {
        const observer = new IntersectionObserver(entries => {
            if (ignorar) return;
            let mejor = null;
            entries.forEach(e => {
                if (!e.isIntersecting) return;
                if (!mejor || e.boundingClientRect.top < mejor.boundingClientRect.top) mejor = e;
            });
            if (mejor) marcarDia(mejor.target.dataset.fecha);
        }, { rootMargin: '-15% 0px -55% 0px', threshold: 0 });

        secciones.forEach(s => observer.observe(s));
        if (secciones[0]) marcarDia(secciones[0].dataset.fecha);
    }
})();
</script>
@endsection
