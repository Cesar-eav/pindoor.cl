@extends('layouts.pindoor')

@section('title', __('ui.info.titulo') . ' — Pindoor Valparaíso')
@section('canonical', route('puntos.info'))
@section('description', __('ui.info.subtitulo'))
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
            <h1 class="text-xl font-extrabold text-gray-900 leading-tight">{{ __('ui.info.titulo') }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">{{ __('ui.info.subtitulo') }}</p>
        </div>
    </div>

    @php
    $cards = [
        ['id'=>'trolebus',        'emoji'=>'🚎', 'titulo'=>__('ui.info.trolebus.titulo'),        'sub'=>__('ui.info.trolebus.sub'),        'color'=>'#f59e0b', 'bg'=>'#fffbeb'],
        ['id'=>'metro',           'emoji'=>'🚆', 'titulo'=>__('ui.info.metro.titulo'),           'sub'=>__('ui.info.metro.sub'),           'color'=>'#3b82f6', 'bg'=>'#eff6ff'],
        ['id'=>'ascensores',      'emoji'=>'🚡', 'titulo'=>__('ui.info.ascensores.titulo'),      'sub'=>__('ui.info.ascensores.sub'),      'color'=>'#0d9488', 'bg'=>'#f0fdfa'],
        ['id'=>'terminal',        'emoji'=>'🚌', 'titulo'=>__('ui.info.terminal.titulo'),        'sub'=>__('ui.info.terminal.sub'),        'color'=>'#10b981', 'bg'=>'#f0fdf4'],
        ['id'=>'banos',           'emoji'=>'🚻', 'titulo'=>__('ui.info.banos.titulo'),           'sub'=>__('ui.info.banos.sub'),           'color'=>'#8b5cf6', 'bg'=>'#f5f3ff'],
        ['id'=>'cambio',          'emoji'=>'💱', 'titulo'=>__('ui.info.cambio.titulo'),          'sub'=>__('ui.info.cambio.sub'),          'color'=>'#fc5648', 'bg'=>'#fff5f5'],
        ['id'=>'estacionamiento', 'emoji'=>'🅿️', 'titulo'=>__('ui.info.estacionamiento.titulo'),'sub'=>__('ui.info.estacionamiento.sub'), 'color'=>'#64748b', 'bg'=>'#f8fafc'],
    ];
    @endphp

    <div class="space-y-3">

    @foreach($cards as $card)
    <div x-data="{ open: false }"
         class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

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

        <div x-show="open" style="height:3px; background:{{ $card['color'] }}; margin-top:-1px"></div>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1">

            {{-- ── TROLEBÚS ────────────────────────────────────────────── --}}
            @if($card['id'] === 'trolebus')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div class="text-xs text-amber-700 bg-amber-50 rounded-xl px-3 py-2 leading-snug">
                    🏆 {!! __('ui.info.trolebus.patrimonio') !!}
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.horario_op') }}</p>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-700">{{ __('ui.info.lun_sab') }}</span>
                            <span class="text-sm font-bold text-gray-900">07:00 – 22:00</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-500">{{ __('ui.info.dom_festivos') }}</span>
                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">{{ __('ui.info.no_opera') }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">{{ __('ui.info.trolebus.recorrido') }}</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 mb-2">{{ __('ui.info.trolebus.ida') }}</p>
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
                            <p class="text-[10px] font-bold text-gray-500 mb-2">{{ __('ui.info.trolebus.vuelta') }}</p>
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

                <p class="text-[11px] text-gray-400 text-center">{{ __('ui.info.trolebus.frecuencia') }}</p>
            </div>
            @endif

            {{-- ── METRO ───────────────────────────────────────────────── --}}
            @if($card['id'] === 'metro')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.horario_op') }}</p>
                    <div class="space-y-1">
                        @php $horarios = [
                            [__('ui.info.lun_vie'),    '06:00 – 23:30'],
                            [__('ui.info.sabado'),     '07:30 – 23:30'],
                            [__('ui.info.dom_festivos'),'07:30 – 23:30'],
                        ]; @endphp
                        @foreach($horarios as [$dia, $hr])
                        <div class="flex justify-between items-center py-2 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                            <span class="text-sm text-gray-700">{{ $dia }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ $hr }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.frecuencias') }}</p>
                    <div class="space-y-2">
                        @php $frecuencias = [
                            [__('ui.info.metro.frec_punta_c'), 'c/ 6 min'],
                            [__('ui.info.metro.frec_punta_l'), 'c/ 12 min'],
                            [__('ui.info.metro.frec_fuera'),   'c/ 12 min'],
                            [__('ui.info.metro.frec_completo'),'~50 min'],
                        ]; @endphp
                        @foreach($frecuencias as [$label, $valor])
                        <div class="flex justify-between items-start gap-2">
                            <p class="text-xs font-semibold text-gray-800">{{ $label }}</p>
                            <span class="text-xs font-bold text-blue-600 shrink-0">{{ $valor }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">{{ __('ui.info.metro.estaciones') }}</p>
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

            {{-- ── TERMINAL DE BUSES ───────────────────────────────────── --}}
            @if($card['id'] === 'terminal')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div class="flex items-start gap-3 bg-emerald-50 rounded-xl px-3 py-3">
                    <span class="text-xl mt-0.5">📍</span>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Av. Pedro Montt 2860</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ __('ui.info.terminal.nota') }}</p>
                        <p class="text-xs text-gray-500">{{ __('ui.info.terminal.nota2') }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.terminal.destinos') }}</p>
                    <div class="space-y-1">
                        @foreach(__('ui.info.terminal.dests') as $row)
                        <div class="flex justify-between items-center py-1.5 {{ !$loop->last?'border-b border-gray-50':'' }}">
                            <span class="text-sm text-gray-700">{{ $row[0] }}</span>
                            <span class="text-xs text-gray-400">{{ $row[1] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.terminal.empresas') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['Turbus','Pullman Bus','Cóndor Bus','Eme Bus','Ciktur','ETM','Sol del Pacífico'] as $emp)
                        <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-medium">{{ $emp }}</span>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.terminal.servicios') }}</p>
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                            <div class="flex items-center gap-2"><span>🚿</span><span class="text-sm text-gray-700">{{ __('ui.info.terminal.duchas') }}</span></div>
                            <span class="text-sm font-semibold text-gray-900">$1.300</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-gray-50">
                            <div class="flex items-center gap-2"><span>🧳</span><span class="text-sm text-gray-700">{{ __('ui.info.terminal.custodia_p') }}</span></div>
                            <span class="text-sm font-semibold text-gray-900">$500</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5">
                            <div class="flex items-center gap-2"><span>🧳</span><span class="text-sm text-gray-700">{{ __('ui.info.terminal.custodia_g') }}</span></div>
                            <span class="text-sm font-semibold text-gray-900">$1.000</span>
                        </div>
                    </div>
                </div>

            </div>
            @endif

            {{-- ── BAÑOS ───────────────────────────────────────────────── --}}
            @if($card['id'] === 'banos')
            <div class="px-4 pb-5 pt-4 space-y-4">

                {{-- Pagados --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.banos.pago') }}</p>
                    <div class="space-y-0">
                        @foreach(__('ui.info.banos.pago_list') as $row)
                        <div class="flex items-center justify-between py-2 {{ !$loop->last?'border-b border-gray-50':'' }}">
                            <div class="flex-1 min-w-0 pr-3">
                                <p class="text-xs font-semibold text-gray-800 leading-tight">{{ $row[0] }}</p>
                                @if($row[1])<p class="text-[10px] text-gray-400 leading-tight mt-0.5">{{ $row[1] }}</p>@endif
                            </div>
                            <span class="text-xs font-bold text-purple-600 shrink-0 bg-purple-50 px-2 py-0.5 rounded-full">{{ $row[2] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Gratuitos --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.banos.gratis') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(__('ui.info.banos.gratis_list') as $row)
                        <div class="bg-green-50 rounded-xl px-3 py-1.5">
                            <p class="text-xs font-semibold text-green-800">{{ $row[0] }}</p>
                            @if($row[1])<p class="text-[10px] text-green-600">{{ $row[1] }}</p>@endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <p class="text-[11px] text-gray-400 text-center leading-snug">
                    {{ __('ui.info.banos.mapa') }}<br>
                    <span class="font-semibold">banosvalparaiso-geopucv.hub.arcgis.com</span>
                </p>

            </div>
            @endif

            {{-- ── ASCENSORES ──────────────────────────────────────────── --}}
            @if($card['id'] === 'ascensores')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div class="text-xs text-amber-700 bg-amber-50 rounded-xl px-3 py-2 leading-snug">
                    {!! __('ui.info.ascensores.aviso') !!}
                </div>

                {{-- Tarifas y horarios --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.horario_op') }}</p>
                        <div class="space-y-1.5">
                            <div>
                                <p class="text-[10px] text-gray-500">{{ __('ui.info.lun_vie') }}</p>
                                <p class="text-xs font-bold text-gray-900">07:00 – 21:30</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500">{{ __('ui.info.ascensores.horario_fds') }}</p>
                                <p class="text-xs font-bold text-gray-900">08:00 – 21:30</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Tarifas 2026</p>
                        <div class="space-y-1.5">
                            <div>
                                <p class="text-[10px] text-gray-500">{{ __('ui.info.ascensores.tarifa_gral') }}</p>
                                <p class="text-xs font-bold text-teal-700">$200</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500">{{ __('ui.info.ascensores.tarifa_ext') }}</p>
                                <p class="text-xs font-bold text-teal-700">$1.000</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500">{{ __('ui.info.ascensores.tarifa_bici') }}</p>
                                <p class="text-xs font-bold text-teal-700">$600</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lista operativos --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.ascensores.operativos') }}</p>
                    <div class="space-y-0">
                        @foreach(__('ui.info.ascensores.list') as $row)
                        <div class="flex items-center justify-between py-2 {{ !$loop->last?'border-b border-gray-50':'' }}">
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $row[0] }}</p>
                                <p class="text-[10px] text-gray-400">{{ $row[1] }}</p>
                            </div>
                            <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full shrink-0">{{ $row[2] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Exentos --}}
                <div class="bg-teal-50 rounded-xl px-3 py-2">
                    <p class="text-[10px] font-bold text-teal-800 mb-0.5">{{ __('ui.info.ascensores.exentos') }}</p>
                    <p class="text-xs text-teal-700">{{ __('ui.info.ascensores.exentos_txt') }}</p>
                </div>

                {{-- Fuera de servicio --}}
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.ascensores.fuera') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach(__('ui.info.ascensores.fuera_list') as $row)
                        <div class="bg-red-50 rounded-xl px-3 py-1.5">
                            <p class="text-xs font-semibold text-red-700">{{ $row[0] }}</p>
                            <p class="text-[10px] text-red-400">{{ $row[1] }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
            @endif

            {{-- ── CAMBIO DE MONEDA ────────────────────────────────────── --}}
            @if($card['id'] === 'cambio')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.cambio.casas') }}</p>
                    <div class="space-y-3">
                        <div class="border border-gray-100 rounded-xl p-3">
                            <p class="text-sm font-bold text-gray-900">Cambios FC</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('ui.info.cambio.zona_fc') }}</p>
                            <p class="text-xs text-gray-500">Tel. +56 9 6207 4957</p>
                            <div class="flex gap-2 mt-2 flex-wrap">
                                @foreach(['USD','EUR','ARS','BRL'] as $mon)
                                <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ $mon }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="border border-gray-100 rounded-xl p-3">
                            <p class="text-sm font-bold text-gray-900">{{ app()->getLocale()==='en' ? 'Prat street and surroundings' : 'Calle Prat y alrededores' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('ui.info.cambio.zona_gen') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.cambio.horario') }}</p>
                    <div class="space-y-1">
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-sm text-gray-700">{{ __('ui.info.lun_vie') }}</span>
                            <span class="text-sm font-bold text-gray-900">09:00 – 18:00</span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-gray-50">
                            <span class="text-sm text-gray-700">{{ __('ui.info.sabado') }}</span>
                            <span class="text-sm font-bold text-gray-900">10:00 – 13:00</span>
                        </div>
                        <div class="flex justify-between py-1.5">
                            <span class="text-sm text-gray-500">{{ __('ui.info.domingo') }}</span>
                            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">{{ __('ui.info.cerrado') }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-xl px-3 py-3 space-y-1.5">
                    <p class="text-xs font-bold text-amber-800">{{ __('ui.info.cambio.consejos_t') }}</p>
                    <p class="text-xs text-amber-700">{{ __('ui.info.cambio.consejo1') }}</p>
                    <p class="text-xs text-amber-700">{{ __('ui.info.cambio.consejo2') }}</p>
                    <p class="text-xs text-amber-700">{{ __('ui.info.cambio.consejo3') }}</p>
                </div>

            </div>
            @endif

            {{-- ── ESTACIONAMIENTOS ────────────────────────────────────── --}}
            @if($card['id'] === 'estacionamiento')
            <div class="px-4 pb-5 pt-4 space-y-4">

                <div class="text-xs text-slate-600 bg-slate-50 rounded-xl px-3 py-2 leading-snug">
                    {!! __('ui.info.estacionamiento.aviso') !!}
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">{{ __('ui.info.estacionamiento.subterraneos') }}</p>
                    <div class="space-y-3">
                    @foreach(__('ui.info.estacionamiento.parkins') as $i => $row)
                        @if($i === 2)
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3 mt-1">{{ __('ui.info.estacionamiento.superficie') }}</p>
                        <div class="space-y-3">
                        @endif
                        <div class="flex items-start gap-3">
                            <span class="text-base shrink-0 mt-0.5">{{ $row[0] }}</span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">{{ $row[1] }}</p>
                                <p class="text-xs text-gray-500">{{ $row[2] }}</p>
                                <p class="text-xs text-gray-400">{{ $row[3] }}</p>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl px-3 py-3">
                    <p class="text-xs font-bold text-blue-800 mb-1">{{ __('ui.info.estacionamiento.alt_titulo') }}</p>
                    <p class="text-xs text-blue-700">{!! __('ui.info.estacionamiento.alt_texto') !!}</p>
                </div>

            </div>
            @endif

        </div>
    </div>
    @endforeach

    </div>

    <p class="text-[11px] text-gray-400 text-center mt-6 px-4">
        {{ __('ui.info.actualizacion') }}
    </p>

</div>
@endsection
