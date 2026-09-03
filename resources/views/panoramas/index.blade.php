@extends('layouts.pindoor')

@section('title', 'Panoramas en Valparaíso · Qué hacer hoy — Pindoor.cl')
@section('canonical', route('atractivos.panoramas'))
@section('description', 'Eventos, conciertos, exposiciones y actividades en Valparaíso. Qué hacer hoy y esta semana: agenda cultural actualizada con entradas, horarios y ubicaciones.')
@if(request()->anyFilled(['categoria']))
@section('robots', 'noindex, follow')
@endif
@section('bodyClass', 'bg-gray-100 text-gray-900')

@php
$jsonLdEventos = $panoramas->where('categoria', '!=', 'exposicion')->take(20)->values();
$jsonLdList = [];
foreach ($jsonLdEventos as $i => $p) {
    $jsonLdList[] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'item'     => [
            '@type'               => 'Event',
            'name'                => (string) $p->titulo,
            'startDate'           => $p->fecha instanceof \Carbon\Carbon ? $p->fecha->toDateString() : (string)$p->fecha,
            'location'            => ['@type' => 'Place', 'name' => $p->lugar ?: 'Valparaíso', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Valparaíso', 'addressCountry' => 'CL']],
            'url'                 => $p->slug
                                        ? route('panoramas.show', $p)
                                        : ($p->punto_slug
                                            ? route('puntos.evento', ['slug' => $p->punto_slug, 'item' => $p->modulo_item_id])
                                            : route('artista.evento', ['slug' => $p->artista_slug, 'item' => $p->modulo_item_id])),
            'organizer'           => ['@type' => 'Organization', 'name' => 'Pindoor'],
            'eventStatus'         => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
        ],
    ];
}
$jsonLdJson = json_encode(['@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => 'Panoramas en Valparaíso', 'url' => route('atractivos.panoramas'), 'itemListElement' => $jsonLdList], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@endphp

@section('head')
<script type="application/ld+json">{!! $jsonLdJson !!}</script>
@endsection

@section('content')

{{-- ── Hero banner publicitario ──────────────────────────────────────────── --}}
@php $bannerFotos = $panoramas->filter(fn($p) => $p->imagen)->take(6)->values(); @endphp
<div x-data="{
        slide: 0,
        slides: [
            { titulo: '¿Tienes un evento en Valparaíso?',       sub: 'Llega a miles de personas que buscan qué hacer en la ciudad.' },
            { titulo: 'Destaca tu panorama aquí',               sub: 'Tu show, feria o actividad frente a la audiencia correcta.' },
            { titulo: '¿Organizas conciertos o talleres?',      sub: 'Promociona tu espacio cultural entre turistas y locales.' },
            { titulo: 'Más visibilidad, más público',           sub: 'Pindoor es la guía de eventos de Valparaíso. Úsala a tu favor.' },
        ],
        init() { setInterval(() => this.slide = (this.slide + 1) % this.slides.length, 3800); }
     }"
     class="w-full bg-gray-950 overflow-hidden mb-0">

    <div class="max-w-5xl mx-auto px-4 py-6 flex items-center gap-4">

        {{-- Texto izquierda --}}
        <div class="flex-1 min-w-0">
            <span class="text-[10px] font-black uppercase tracking-widest text-[#fc5648]">Espacio publicitario · Pindoor</span>
            <div class="relative mt-1" style="min-height:3.6rem">
                <template x-for="(s, i) in slides" :key="i">
                    <div x-show="slide === i"
                         x-transition:enter="transition duration-500"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition duration-300 absolute"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="w-full">
                        <p class="text-xl sm:text-2xl font-extrabold text-white leading-tight" x-text="s.titulo"></p>
                        <p class="text-xs sm:text-sm text-gray-400 mt-0.5 leading-snug line-clamp-2" x-text="s.sub"></p>
                    </div>
                </template>
            </div>
            <div class="flex items-center gap-3 mt-3">
                <a href="{{ route('contacto.index') }}"
                   class="inline-block bg-[#fc5648] hover:bg-[#d94439] text-white text-xs font-black px-5 py-2 rounded-full transition whitespace-nowrap">
                    Contáctanos →
                </a>
                {{-- Dots --}}
                <div class="flex gap-1.5">
                    <template x-for="(s, i) in slides" :key="i">
                        <span class="block h-1.5 rounded-full transition-all duration-300 cursor-pointer"
                              :class="slide === i ? 'bg-[#fc5648] w-4' : 'bg-gray-600 w-1.5'"
                              @click="slide = i"></span>
                    </template>
                </div>
            </div>
        </div>

        {{-- Imágenes derecha --}}
        {{-- @if($bannerFotos->isNotEmpty())
        <div class="hidden sm:flex gap-2 shrink-0">
            @foreach($bannerFotos->take(4) as $foto)
            <a href="{{ route('panoramas.show', $foto) }}"
               class="relative block shrink-0 w-20 h-24 rounded-xl overflow-hidden ring-1 ring-white/10 hover:ring-[#fc5648]/60 transition">
                <img src="{{ asset('storage/' . $foto->imagen) }}"
                     alt="{{ $foto->titulo }}"
                     class="w-full h-full object-cover brightness-75 hover:brightness-100 transition duration-300">
            </a>
            @endforeach
        </div>
        @endif --}}

        {{-- Re-vival autorotante --}}
        @if($revivals->isNotEmpty())
        <a href="{{ route('revival.index') }}"
           x-data="{
               items: {{ Js::from($revivals->map(fn($r) => ['titulo' => $r->titulo, 'imagen' => $r->imagen_portada_url])->values()) }},
               i: 0,
               init() { if (this.items.length > 1) setInterval(() => this.i = (this.i + 1) % this.items.length, 4000) }
           }"
           class="block relative self-stretch my-1 w-20 sm:w-36 shrink-0 rounded-xl overflow-hidden bg-gray-800 ring-1 ring-white/10 hover:ring-[#fc5648]/60 transition">
            <template x-for="(item, idx) in items" :key="idx">
                <img :src="item.imagen" x-show="i === idx"
                     x-transition:enter="transition duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     class="absolute inset-0 w-full h-full object-cover">
            </template>
            <span class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full bg-black/50 backdrop-blur-sm flex items-center justify-center">
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
            </span>
            <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-black/85 via-black/40 to-transparent px-2 pt-5 pb-1.5">
                <span class="block text-[8px] font-black uppercase tracking-wide text-[#fc5648]">🎬 Re-vival</span>
                <span class="block text-[11px] font-bold text-white truncate" x-text="items[i]?.titulo"></span>
            </div>
        </a>
        @endif

    </div>
</div>

<div class="max-w-5xl mx-auto px-4 pt-3 pb-8">

    {{-- Header compacto --}}
<div class="flex items-center justify-between mb-3 flex-wrap gap-2">
    <div class="flex items-center">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-950">
            {{ __('ui.panoramas.titulo') }}
        </h1>
        <div class="flex items-center ml-5 gap-2 text-xs text-slate-500 font-medium">
            <span><strong class="text-slate-900 font-extrabold text-sm">{{ $panoramas->where('categoria','!=','exposicion')->count() }}</strong> {{ __('ui.panoramas.eventos') }}</span>
            @if($exposiciones->isNotEmpty())
            <span class="text-slate-300">·</span>
            <span><strong class="text-slate-900 font-extrabold text-sm">{{ $exposiciones->count() }}</strong> {{ __('ui.panoramas.en_cartelera') }}</span>
            @endif
        </div>
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
            {{ __('ui.home.todos_label') }}
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
            🎟️ {{ __('ui.panoramas.gratis') }}
        </a>
        @endif
    </div>
    </div>
    @endif

    @if($panoramas->isEmpty())
        <div class="text-center py-20">
            <div class="text-5xl mb-3">📭</div>
            <p class="font-bold text-gray-700 mb-1">{{ __('ui.panoramas.vacio') }}</p>
            <p class="text-sm text-gray-400">{{ __('ui.panoramas.vacio_sub') }}</p>
        </div>
    @else

    {{-- ── En cartelera (exposiciones) ────────────────────────────────────── --}}
    @if($exposiciones->isNotEmpty() && (!$catActiva || $catActiva === 'exposicion'))
    <section class="mb-10">
        <div class="flex items-center gap-3 mb-4">
            <span class="text-lg">🎭</span>
            <h2 class="text-base font-extrabold text-gray-900 tracking-tight">{{ ucfirst(__('ui.panoramas.en_cartelera')) }}</h2>
            <div class="flex-1 h-px bg-gray-200"></div>
            <span class="text-xs text-gray-400 font-semibold">{{ $exposiciones->count() }} {{ $exposiciones->count() === 1 ? __('ui.panoramas.exposicion') : __('ui.panoramas.exposiciones') }}</span>
        </div>
        <div class="flex gap-2 overflow-x-auto pb-2" style="scrollbar-width:none">
            @foreach($exposiciones as $exp)
            @php
                $hoyExp       = \Carbon\Carbon::today();
                $esRecurrente = !empty($exp->dias_semana);
                if ($esRecurrente) {
                    $proximaFecha     = $exp->proximaOcurrencia($hoyExp);
                    $diasHastaProxima = $proximaFecha ? $hoyExp->diffInDays($proximaFecha, false) : null;
                } else {
                    $iniciada = $exp->fecha->lte($hoyExp);
                    $hasta    = $exp->fecha_fin ?? $exp->fecha;
                    $diasRest = $hoyExp->diffInDays($hasta, false);
                }
                $expHref  = $exp->slug
                    ? route('panoramas.show', $exp)
                    : ($exp->punto_slug
                        ? route('puntos.evento', ['slug' => $exp->punto_slug, 'item' => $exp->modulo_item_id])
                        : route('artista.evento', ['slug' => $exp->artista_slug, 'item' => $exp->modulo_item_id]));
            @endphp
            <a href="{{ $expHref }}"
               class="group relative block shrink-0" style="width:12rem;">

                {{-- Imagen --}}
                <div class="relative overflow-hidden shadow-sm bg-gray-900" style="height:14rem;">
                    @if($exp->imagen)
                        <img src="{{ asset('storage/' . $exp->imagen) }}"
                             alt="{{ $exp->titulo }}"
                             class="absolute inset-0 w-full h-full object-cover opacity-100 group-hover:opacity-90 group-hover:scale-105 transition duration-500">
                    @endif

                    {{-- Badge vigencia --}}
                    <div class="absolute top-3 left-3 z-10">
                        @if($esRecurrente)
                            <span class="bg-[#fc5648] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                                @if($diasHastaProxima === 0) {{ __('ui.panoramas.hoy') }}
                                @elseif($diasHastaProxima === 1) {{ __('ui.panoramas.manana') }}
                                @else {{ __('ui.panoramas.en_x_dias', ['n' => $diasHastaProxima]) }}
                                @endif
                            </span>
                        @elseif($iniciada && $diasRest <= 7 && $diasRest >= 0)
                            <span class="bg-[#fc5648] text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                                {{ $diasRest === 0 ? __('ui.panoramas.ultimo_dia') : __('ui.panoramas.ultimos_dias', ['n' => $diasRest]) }}
                            </span>
                        @elseif(!$iniciada)
                            <span class="bg-gray-800/80 text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
                                {{ __('ui.panoramas.proximamente') }}
                            </span>
                        @else
                            <span class="bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold px-2.5 py-1 rounded-full">
                                {{ __('ui.panoramas.en_curso') }}
                            </span>
                        @endif
                        @if($exp->es_gratuito)
                            <span class="ml-1 bg-green-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full">{{ __('ui.panoramas.gratis') }}</span>
                        @endif
                    </div>
                </div>

                {{-- Info debajo de la imagen --}}
                <div class="pt-2 px-0.5">
                    <p class="text-[10px] font-bold text-gray-400  tracking-widest mb-1">
                        @if($esRecurrente)
                            @if($proximaFecha)
                                {{ __('ui.panoramas.proxima_fecha') }} {{ $proximaFecha->locale('es')->translatedFormat('j \d\e F') }}
                            @endif
                        @elseif($iniciada)
                            Hasta el {{ $hasta->locale('es')->translatedFormat('j \d\e F') }}
                        @else
                            Del {{ $exp->fecha->locale('es')->translatedFormat('j \d\e F') }} al {{ $hasta->locale('es')->translatedFormat('j \d\e F') }}
                        @endif
                    </p>
                    <h3 class="text-gray-500 leading-snug text-xs">{{ $exp->titulo }}</h3>
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
            <span class="text-xs text-gray-400 font-semibold">{{ $grupo->count() }} {{ $grupo->count() === 1 ? __('ui.panoramas.evento') : __('ui.panoramas.eventos') }}</span>
        </div>

        {{-- Cards del día --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($grupo as $panorama)

            @php $cardHref = $panorama->slug
                ? route('panoramas.show', $panorama)
                : ($panorama->punto_slug
                    ? route('puntos.evento', ['slug' => $panorama->punto_slug, 'item' => $panorama->modulo_item_id])
                    : route('artista.evento', ['slug' => $panorama->artista_slug, 'item' => $panorama->modulo_item_id])); @endphp
            <div class="group bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer
                        {{ $meta['esHoy'] ? 'ring-1 ring-[#fc5648]/20' : '' }}"
                 onclick="window.location.href='{{ $cardHref }}'"
                 style="border-radius:1rem">

                {{-- Imagen --}}
                <div class="relative overflow-hidden rounded-t-2xl bg-gray-100 sm:aspect-3/4 sm:bg-gray-900">
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
                        🎟️ {{ __('ui.panoramas.gratis') }}
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
                    <p class="font-bold text-gray-900 leading-snug mb-1">
                        <span class="md:hidden">{{ mb_strlen($panorama->titulo) > 35 ? mb_substr($panorama->titulo, 0, 35) . '…' : $panorama->titulo }}</span>
                        <span class="hidden md:inline">{{ mb_strlen($panorama->titulo) > 30 ? mb_substr($panorama->titulo, 0, 30) . '…' : $panorama->titulo }}</span>
                    </p>
                    @if($panorama->getTranslation('descripcion', app()->getLocale(), false))
                    <p class="text-xs text-gray-500 leading-snug line-clamp-2 mb-1">{{ $panorama->descripcion }}</p>
                    @endif

                    {{-- Rango de fechas --}}
                    @if($panorama->fecha_fin && !$panorama->fecha->isSameDay($panorama->fecha_fin))
                    @php
                        $mismoMes = $panorama->fecha->month === $panorama->fecha_fin->month;
                    @endphp
                    <p class="text-xs text-[#fc5648] font-semibold mb-1">
                        📅 {{ $panorama->fecha->locale('es')->translatedFormat($mismoMes ? 'j' : 'j \d\e F') }}
                           al {{ $panorama->fecha_fin->locale('es')->translatedFormat('j \d\e F') }}
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

                    @php
                        $gcHora    = $panorama->hora ? str_replace(':', '', substr($panorama->hora, 0, 5)) . '00' : null;
                        $gcStart   = $gcHora ? $panorama->fecha->format('Ymd') . 'T' . $gcHora : $panorama->fecha->format('Ymd');
                        $gcFechaFin = $panorama->fecha_fin ?? $panorama->fecha;
                        $gcEnd     = $gcHora ? $gcFechaFin->format('Ymd') . 'T' . $gcHora : $gcFechaFin->copy()->addDay()->format('Ymd');
                        $gcUrl     = 'https://calendar.google.com/calendar/render?' . http_build_query([
                            'action'   => 'TEMPLATE',
                            'text'     => $panorama->titulo,
                            'dates'    => $gcStart . '/' . $gcEnd,
                            'location' => $panorama->ubicacion ?? '',
                        ]);
                        $waTitulo   = $panorama->titulo;
                        $waFecha    = $panorama->fecha->locale('es')->translatedFormat('j \d\e F');
                        $waHora     = $panorama->hora ? substr($panorama->hora, 0, 5) . ' hrs' : null;
                        $waFechaHora = $waFecha . ($waHora ? ', ' . $waHora : '');
                    @endphp

                    <div class="mt-3 flex items-center gap-3 flex-wrap">

                        <a href="{{ $gcUrl }}" target="_blank" rel="noopener noreferrer"
                           onclick="event.stopPropagation(); registrarCompartido('{{ $cardHref }}', 'calendario')"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-blue-600 transition">
                                Agregar Calendario
                        </a>
                        @include('partials._share_panel', [
                            'shareText' => $waTitulo . ' — ' . $waFechaHora . ' — ' . $cardHref,
                            'imageUrl' => $panorama->imagen ? asset('storage/' . $panorama->imagen) : null,
                            'url' => $cardHref,
                        ])
                    </div>
                </div>
            </div>

            @endforeach
        </div>
    </section>
    @endforeach

    @endif
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<script>
(function () {
    // Preservar scroll de pills de categoría
    const catsScroll = document.getElementById('cats-scroll');
    if (catsScroll) {
        const saved = sessionStorage.getItem('panoramas_cats_scroll');
        if (saved !== null) {
            catsScroll.scrollLeft = parseInt(saved);
            sessionStorage.removeItem('panoramas_cats_scroll');
        }
        catsScroll.querySelectorAll('a').forEach(pill => {
            pill.addEventListener('click', () => {
                sessionStorage.setItem('panoramas_cats_scroll', catsScroll.scrollLeft);
            });
        });
    }

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

{{-- Popup boletín de panoramas — sencillo, cerrable, no invasivo --}}
<div x-data="{
        visible: false, email: '', enviando: false, enviado: false, error: '',
        diasReaparicion: {{ $newsletterPopupDias }},
        cadaRecarga: {{ $newsletterPopupCadaRecarga ? 'true' : 'false' }},
        init() {
            const cerradoHasta = localStorage.getItem('newsletter_panoramas_cerrado_hasta');
            if (cerradoHasta === 'siempre') return;
            if (!this.cadaRecarga && cerradoHasta && Date.now() < parseInt(cerradoHasta, 10)) return;
            setTimeout(() => { this.visible = true; }, 4000);
        },
        cerrar() {
            this.visible = false;
            const expiracion = Date.now() + this.diasReaparicion * 24 * 60 * 60 * 1000;
            localStorage.setItem('newsletter_panoramas_cerrado_hasta', String(expiracion));
        },
        enviar() {
            if (this.enviando) return;
            this.enviando = true; this.error = '';
            fetch('{{ route('newsletter.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content,
                },
                body: JSON.stringify({ email: this.email, origen: 'panoramas' }),
            })
            .then(r => { if (!r.ok) throw new Error(); return r.json(); })
            .then(() => { this.enviado = true; localStorage.setItem('newsletter_panoramas_cerrado_hasta', 'siempre'); })
            .catch(() => { this.error = 'No pudimos suscribirte. Revisa el correo e intenta de nuevo.'; })
            .finally(() => { this.enviando = false; });
        }
     }"
     x-show="visible"
     @keydown.escape.window="cerrar()"
     class="fixed inset-0 z-999 flex items-center justify-center p-4"
     style="display:none">

    <div x-show="visible"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="cerrar()"
         class="absolute inset-0 bg-black/60"></div>

    <div x-show="visible"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
         class="relative bg-black border border-white/10 rounded-3xl shadow-2xl w-full max-w-sm p-6">

        <button @click="cerrar()" aria-label="Cerrar"
                class="absolute top-3 right-3 w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:bg-white/10 hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <template x-if="!enviado">
            <div class="text-center">
                <p class="font-extrabold text-white text-2xl leading-snug" style="font-family:'Lora',serif;">Pindoor Newsletter</p>
                <p class="text-sm text-gray-400 mt-2 leading-relaxed" style="font-family:'Lora',serif; font-style:italic;">
                    Déjanos tu correo y te avisamos cuando publiquemos novedades.
                </p>
                <form @submit.prevent="enviar()" class="mt-5 space-y-2">
                    <input type="email" x-model="email" required placeholder="tu@correo.com"
                           class="w-full px-4 py-2.5 bg-white/5 border border-white/15 rounded-xl text-sm text-white placeholder-gray-500 focus:ring-2 focus:ring-[#fc5648] outline-none">
                    <button type="submit" :disabled="enviando"
                            class="w-full bg-[#fc5648] hover:bg-[#d94439] text-white text-sm font-bold px-5 py-2.5 rounded-xl transition disabled:opacity-50">
                        <span x-show="!enviando">Avísame</span>
                        <span x-show="enviando">Enviando…</span>
                    </button>
                </form>
                <p x-show="error" x-text="error" class="text-xs text-red-400 mt-2"></p>
                <button @click="cerrar()" class="w-full text-center text-xs text-gray-500 hover:text-gray-300 mt-3 transition">
                    Ahora no
                </button>
            </div>
        </template>
        <template x-if="enviado">
            <div class="text-center py-2">
                <p class="text-3xl mb-2">✓</p>
                <p class="font-extrabold text-white text-xl" style="font-family:'Lora',serif;">¡Listo!</p>
                <p class="text-sm text-gray-400 mt-1">Te avisaremos por correo.</p>
            </div>
        </template>
    </div>
</div>

@endsection
