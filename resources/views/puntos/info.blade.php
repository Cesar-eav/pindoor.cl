@extends('layouts.pindoor')

@section('title', 'Info práctica — Pindoor Valparaíso')
@section('canonical', route('puntos.info'))
@section('description', 'Transporte, terminal de buses, baños, cambio de moneda y estacionamientos en Valparaíso.')
@section('bodyClass', 'bg-gray-50 text-gray-900')
@section('robots', 'index, follow')

@section('content')
<div class="max-w-xl mx-auto px-3 py-5 pb-28">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('puntos.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm text-gray-500 hover:text-gray-800 transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 leading-tight">Info práctica</h1>
            <p class="text-xs text-gray-400 mt-0.5">Transporte · Servicios · Valparaíso</p>
        </div>
    </div>

    @php
    $cards = [
        [
            'id'      => 'trolebus',
            'emoji'   => '🚎',
            'titulo'  => 'Trolebús',
            'sub'     => 'Línea 802 · Av. Argentina ↔ Aduana',
            'color'   => '#f59e0b',
            'bg'      => '#fffbeb',
        ],
        [
            'id'      => 'metro',
            'emoji'   => '🚆',
            'titulo'  => 'Metro / Merval',
            'sub'     => 'Tren Limache–Puerto · 21 estaciones',
            'color'   => '#3b82f6',
            'bg'      => '#eff6ff',
        ],
        [
            'id'      => 'terminal',
            'emoji'   => '🚌',
            'titulo'  => 'Terminal de Buses',
            'sub'     => 'Av. Pedro Montt 2860 · frente al Congreso',
            'color'   => '#10b981',
            'bg'      => '#f0fdf4',
        ],
        [
            'id'      => 'banos',
            'emoji'   => '🚻',
            'titulo'  => 'Baños Públicos',
            'sub'     => '72 puntos en la ciudad · algunos gratuitos',
            'color'   => '#8b5cf6',
            'bg'      => '#f5f3ff',
        ],
        [
            'id'      => 'cambio',
            'emoji'   => '💱',
            'titulo'  => 'Cambio de Moneda',
            'sub'     => 'Casas de cambio en el centro',
            'color'   => '#fc5648',
            'bg'      => '#fff5f5',
        ],
        [
            'id'      => 'estacionamiento',
            'emoji'   => '🅿️',
            'titulo'  => 'Estacionamientos',
            'sub'     => 'Plan y acceso a los cerros',
            'color'   => '#64748b',
            'bg'      => '#f8fafc',
        ],
    ];
    @endphp

    <div class="space-y-3">

    @foreach($cards as $card)
    <div x-data="{ open: false }"
         class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- Header clicable --}}
        <button @click="open = !open"
                class="w-full flex items-center gap-3 px-4 py-4 text-left transition-colors"
                :style="open ? 'background:{{ $card['bg'] }}' : ''">
            <span class="text-2xl shrink-0 leading-none">{{ $card['emoji'] }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-extrabold text-gray-900">{{ $card['titulo'] }}</p>
                <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $card['sub'] }}</p>
            </div>
            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                 :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Línea de color que aparece al abrir --}}
        <div x-show="open" style="height:3px; background:{{ $card['color'] }}; margin-top:-1px"></div>

        {{-- Contenido --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1">

            {{-- ── TROLEBÚS ─────────────────────────────────────────────── --}}
            @if($card['id'] === 'trolebus')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div class="text-xs text-amber-700 bg-amber-50 rounded-xl px-3 py-2 leading-snug">
                    🏆 El sistema de trolebuses más antiguo de América Latina en operación continua <strong>desde 1952</strong>. Patrimonio vivo de la ciudad.
                </div>

                {{-- Horarios --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Horario de operación</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-700">Lunes a Sábado</span>
                            <span class="text-sm font-bold text-gray-900">07:00 – 22:00</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-500">Domingos y festivos</span>
                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">No opera</span>
                        </div>
                    </div>
                </div>

                {{-- Recorrido --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Recorrido (~13 km)</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 mb-2">Ida → Aduana</p>
                            @php $ida = ['Av. Argentina','Colón','Edwards','Brasil','Pedro Montt','Blanco Sur','Blanco','Aduana']; @endphp
                            @foreach($ida as $i => $p)
                            <div class="flex items-start gap-2 mb-0.5">
                                <div class="flex flex-col items-center shrink-0 w-3 mt-1">
                                    <div class="w-2 h-2 rounded-full border {{ $i===0?'bg-amber-500 border-amber-500':($i===count($ida)-1?'bg-gray-700 border-gray-700':'bg-white border-gray-300') }}"></div>
                                    @if($i<count($ida)-1)<div class="w-px h-3.5 bg-gray-200"></div>@endif
                                </div>
                                <p class="text-xs {{ $i===0||$i===count($ida)-1?'font-bold text-gray-800':'text-gray-600' }} leading-tight">{{ $p }}</p>
                            </div>
                            @endforeach
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 mb-2">Vuelta → Av. Argentina</p>
                            @php $vuelta = ['Aduana','Bustamante','Serrano / Prat','Esmeralda','Condell','Buenos Aires','Colón','Av. Argentina']; @endphp
                            @foreach($vuelta as $i => $p)
                            <div class="flex items-start gap-2 mb-0.5">
                                <div class="flex flex-col items-center shrink-0 w-3 mt-1">
                                    <div class="w-2 h-2 rounded-full border {{ $i===0?'bg-gray-700 border-gray-700':($i===count($vuelta)-1?'bg-amber-500 border-amber-500':'bg-white border-gray-300') }}"></div>
                                    @if($i<count($vuelta)-1)<div class="w-px h-3.5 bg-gray-200"></div>@endif
                                </div>
                                <p class="text-xs {{ $i===0||$i===count($vuelta)-1?'font-bold text-gray-800':'text-gray-600' }} leading-tight">{{ $p }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <p class="text-[11px] text-gray-400 text-center">Frecuencia cada 10–15 min · Se paga con tarjeta Bip!</p>
            </div>
            @endif

            {{-- ── METRO ────────────────────────────────────────────────── --}}
            @if($card['id'] === 'metro')
            <div class="px-4 pb-5 pt-4 space-y-4">

                {{-- Horarios --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Horario de operación</p>
                    <div class="space-y-1">
                        @php $horarios = [['Lunes a Viernes','06:00 – 23:30'],['Sábado','07:30 – 23:30'],['Domingo y festivos','07:30 – 23:30']]; @endphp
                        @foreach($horarios as [$dia, $hr])
                        <div class="flex justify-between items-center py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                            <span class="text-sm text-gray-700">{{ $dia }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $hr }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Frecuencias --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Frecuencias</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-start gap-2">
                            <div>
                                <p class="text-xs font-semibold text-gray-800">L–V hora punta · Puerto–Sargento Aldea</p>
                            </div>
                            <span class="text-xs font-bold text-blue-600 shrink-0">c/ 6 min</span>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <p class="text-xs font-semibold text-gray-800">L–V hora punta · Puerto–Limache</p>
                            <span class="text-xs font-bold text-blue-600 shrink-0">c/ 12 min</span>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <p class="text-xs font-semibold text-gray-800">L–V fuera de punta · Sáb · Dom</p>
                            <span class="text-xs font-bold text-gray-500 shrink-0">c/ 12 min</span>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <p class="text-xs font-semibold text-gray-800">Recorrido completo Puerto ↔ Limache</p>
                            <span class="text-xs font-bold text-gray-500 shrink-0">~50 min</span>
                        </div>
                    </div>
                </div>

                {{-- Estaciones --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">21 Estaciones — Puerto → Limache</p>
                    @php
                    $estaciones = [
                        ['Puerto','Valparaíso'],['Bellavista','Valparaíso'],['Francia','Valparaíso'],
                        ['Barón','Valparaíso'],['Portales','Valparaíso'],
                        ['Recreo','Viña del Mar'],['Miramar','Viña del Mar'],['Viña del Mar','Viña del Mar'],
                        ['Hospital','Viña del Mar'],['Chorrillos','Viña del Mar'],['El Salto','Viña del Mar'],
                        ['Quilpué','Quilpué'],['El Sol','Quilpué'],['Valencia','Quilpué',true],['Belloto','Quilpué'],
                        ['Las Américas','Villa Alemana'],['La Concepción','Villa Alemana'],
                        ['Villa Alemana','Villa Alemana'],['Sargento Aldea','Villa Alemana'],
                        ['Peñablanca','Peñablanca'],['Limache','Limache'],
                    ];
                    $colores = ['Valparaíso'=>'#fc5648','Viña del Mar'=>'#3b82f6','Quilpué'=>'#10b981','Villa Alemana'=>'#8b5cf6','Peñablanca'=>'#64748b','Limache'=>'#64748b'];
                    $ciudadActual = '';
                    @endphp
                    <div class="flex flex-col">
                    @foreach($estaciones as $i => $est)
                        @php [$nombre, $ciudad] = $est; $nuevo = $est[2] ?? false; @endphp
                        @if($ciudad !== $ciudadActual)
                            @php $ciudadActual = $ciudad; @endphp
                            @if($i > 0)<div class="h-1"></div>@endif
                            <p class="text-[9px] font-black uppercase tracking-widest pl-5 mb-1"
                               style="color:{{ $colores[$ciudad] ?? '#94a3b8' }}">{{ $ciudad }}</p>
                        @endif
                        <div class="flex items-start gap-2">
                            <div class="flex flex-col items-center shrink-0 w-3 mt-0.5">
                                <div class="w-2.5 h-2.5 rounded-full border-2 shrink-0"
                                     style="{{ $i===0||$i===count($estaciones)-1 ? 'background:'.($colores[$ciudad]??'#94a3b8').';border-color:'.($colores[$ciudad]??'#94a3b8') : 'background:white;border-color:'.($colores[$ciudad]??'#d1d5db') }}"></div>
                                @if($i<count($estaciones)-1)<div class="w-px flex-1 bg-gray-100" style="min-height:14px"></div>@endif
                            </div>
                            <div class="flex items-center gap-1.5 pb-0.5 flex-1">
                                <p class="text-xs {{ $i===0||$i===count($estaciones)-1?'font-bold text-gray-900':'text-gray-700' }}">{{ $nombre }}</p>
                                @if($nuevo)<span class="text-[9px] font-black px-1.5 py-px rounded-full bg-emerald-100 text-emerald-700">NUEVA</span>@endif
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>

            </div>
            @endif

            {{-- ── TERMINAL DE BUSES ────────────────────────────────────── --}}
            @if($card['id'] === 'terminal')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div class="flex items-start gap-3 bg-emerald-50 rounded-xl px-3 py-3">
                    <span class="text-xl mt-0.5">📍</span>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Av. Pedro Montt 2860</p>
                        <p class="text-xs text-gray-500 mt-0.5">Frente al Congreso Nacional · Sector Almendral</p>
                        <p class="text-xs text-gray-500">Cerca del Metro Estación Portales y el Trolebús</p>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Destinos frecuentes</p>
                    <div class="space-y-1">
                        @php $destinos = [['Santiago','Cada 10–15 min · todo el día'],['Viña del Mar','Frecuente'],['Costa Central (Algarrobo, El Quisco…)','L–D'],['Casablanca','Frecuente'],['Norte y Sur de Chile','Varios horarios'],]; @endphp
                        @foreach($destinos as [$dest, $frec])
                        <div class="flex justify-between items-center py-1.5 {{ !$loop->last?'border-b border-gray-50':'' }}">
                            <span class="text-sm text-gray-700">{{ $dest }}</span>
                            <span class="text-xs text-gray-400">{{ $frec }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Empresas principales</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Turbus','Pullman Bus','Cóndor Bus','Eme Bus','Ciktur','ETM','Sol del Pacífico'] as $emp)
                        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-medium">{{ $emp }}</span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Servicios en terminal</p>
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                            <div class="flex items-center gap-2"><span>🚿</span><span class="text-sm text-gray-700">Duchas</span></div>
                            <span class="text-sm font-semibold text-gray-900">$1.300</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                            <div class="flex items-center gap-2"><span>🧳</span><span class="text-sm text-gray-700">Custodia bolso pequeño</span></div>
                            <span class="text-sm font-semibold text-gray-900">$500</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <div class="flex items-center gap-2"><span>🧳</span><span class="text-sm text-gray-700">Custodia bolso grande</span></div>
                            <span class="text-sm font-semibold text-gray-900">$1.000</span>
                        </div>
                    </div>
                </div>

            </div>
            @endif

            {{-- ── BAÑOS ────────────────────────────────────────────────── --}}
            @if($card['id'] === 'banos')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div class="text-xs text-purple-700 bg-purple-50 rounded-xl px-3 py-2 leading-snug">
                    La municipalidad dispone de <strong>72 baños públicos</strong> distribuidos por la ciudad, algunos gratuitos y otros de pago.
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Zonas con baños disponibles</p>
                    <div class="space-y-1">
                        @php $banos = [
                            ['🚢','Sector Puerto / Aduana','Zona turística principal'],
                            ['🏛','Plan (calle Esmeralda / Prat)','Varios puntos'],
                            ['🎨','Cerro Alegre / Concepción','En miradores y plazas'],
                            ['🌊','Playa Caleta El Membrillo','Zona costera'],
                            ['🚌','Terminal de Buses','Pago · $500 aprox.'],
                            ['🚆','Estación Metro Puerto','Acceso con Bip!'],
                        ]; @endphp
                        @foreach($banos as [$ic, $lugar, $nota])
                        <div class="flex items-start gap-3 py-2 {{ !$loop->last?'border-b border-gray-50':'' }}">
                            <span class="text-lg shrink-0 leading-snug">{{ $ic }}</span>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $lugar }}</p>
                                <p class="text-xs text-gray-400">{{ $nota }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <p class="text-[11px] text-gray-400 text-center leading-snug">
                    Mapa interactivo: <span class="font-semibold">banosvalparaiso-geopucv.hub.arcgis.com</span>
                </p>

            </div>
            @endif

            {{-- ── CAMBIO DE MONEDA ─────────────────────────────────────── --}}
            @if($card['id'] === 'cambio')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Casas de cambio · Centro</p>
                    <div class="space-y-3">
                        <div class="border border-gray-100 rounded-xl p-3">
                            <p class="text-sm font-bold text-gray-900">Cambios FC</p>
                            <p class="text-xs text-gray-500 mt-0.5">Zona calle Prat / Barrio Financiero</p>
                            <p class="text-xs text-gray-500">Tel. +56 9 6207 4957</p>
                            <div class="flex gap-2 mt-2 flex-wrap">
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">USD</span>
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">EUR</span>
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">ARS</span>
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">BRL</span>
                            </div>
                        </div>
                        <div class="border border-gray-100 rounded-xl p-3">
                            <p class="text-sm font-bold text-gray-900">Calle Prat y alrededores</p>
                            <p class="text-xs text-gray-500 mt-0.5">Varias opciones en la zona financiera del centro</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Horario general</p>
                    <div class="space-y-1">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-sm text-gray-700">Lunes a Viernes</span>
                            <span class="text-sm font-bold text-gray-900">09:00 – 18:00</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-sm text-gray-700">Sábado</span>
                            <span class="text-sm font-bold text-gray-900">10:00 – 13:00</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-sm text-gray-500">Domingo</span>
                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">Cerrado</span>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-xl px-3 py-3 space-y-1.5">
                    <p class="text-xs font-bold text-amber-800">💡 Consejos</p>
                    <p class="text-xs text-amber-700">· Lleva siempre tu cédula o pasaporte — es obligatorio para montos grandes.</p>
                    <p class="text-xs text-amber-700">· Evita a los captadores en la calle que ofrecen mejor cambio.</p>
                    <p class="text-xs text-amber-700">· Los cajeros automáticos aceptan Visa/Mastercard internacionales.</p>
                </div>

            </div>
            @endif

            {{-- ── ESTACIONAMIENTOS ─────────────────────────────────────── --}}
            @if($card['id'] === 'estacionamiento')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div class="text-xs text-slate-600 bg-slate-50 rounded-xl px-3 py-2 leading-snug">
                    ⚠️ Los cerros (Alegre, Concepción, Artillería) tienen calles angostas y estacionamiento muy limitado. <strong>Se recomienda dejar el auto en el Plan y subir a pie, en ascensor o funicular.</strong>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Zonas de estacionamiento</p>
                    <div class="space-y-3">
                        @php $parkins = [
                            ['🟢','Av. Argentina / Av. Pedro Montt','El Plan — mayor disponibilidad','Pago por metro en la calle'],
                            ['🟢','Mall Plaza Valparaíso','Av. Brasil 2870 · varios niveles','Pago por hora'],
                            ['🟡','Sector Puerto / Muelle Prat','Disponibilidad variable','Pago en estacionamiento'],
                            ['🔴','Cerro Alegre / Concepción','Muy limitado · calles angostas','Sin estacionamientos formales'],
                            ['🟡','Av. Errázuriz (Barrio Puerto)','Zona costera Plan','Pago por metro en la calle'],
                        ]; @endphp
                        @foreach($parkins as [$sem, $nombre, $nota, $info])
                        <div class="flex items-start gap-3">
                            <span class="text-base shrink-0 mt-0.5">{{ $sem }}</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">{{ $nombre }}</p>
                                <p class="text-xs text-gray-500">{{ $nota }}</p>
                                <p class="text-xs text-gray-400">{{ $info }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl px-3 py-3">
                    <p class="text-xs font-bold text-blue-800 mb-1">🚡 Alternativa recomendada</p>
                    <p class="text-xs text-blue-700">Estaciona en el Plan (Av. Argentina) y usa los <strong>ascensores</strong> o escaleras para subir a los cerros turísticos.</p>
                </div>

            </div>
            @endif

        </div>
    </div>
    @endforeach

    </div>

    <p class="text-[11px] text-gray-400 text-center mt-6 px-4">
        Información actualizada al primer semestre 2026 · Los precios y horarios pueden variar
    </p>

</div>
@endsection
