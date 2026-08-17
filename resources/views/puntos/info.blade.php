@extends('layouts.pindoor')

@section('title', __('ui.info.titulo') . ' — Pindoor Valparaíso')
@section('canonical', route('puntos.info'))
@section('description', __('ui.info.subtitulo'))
@section('bodyClass', 'bg-gray-50 text-gray-900')
@section('robots', 'index, follow')

@section('head')
    @vite('resources/js/leaflet.js')
    <style>
        .bano-marker-pago   { background:#8b5cf6; }
        .bano-marker-gratis { background:#10b981; }
        .bano-dot { width:14px;height:14px;border-radius:50%;border:2.5px solid white;box-shadow:0 2px 6px rgba(0,0,0,.25); }
    </style>
@endsection

@section('content')
<div class="max-w-xl mx-auto px-3 py-5 pb-28">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('puntos.index') }}" wire:navigate
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
    // Estado real de los ascensores — se edita desde /admin/configuracion
    $ascensores = \App\Models\PuntoInteres::publico()
        ->whereHas('categoria', fn($q) => $q->where('slug', 'ascensores'))
        ->orderBy('title')
        ->get(['title', 'sector', 'fuera_de_servicio', 'fuera_de_servicio_motivo']);
    $ascensoresOperativos = $ascensores->where('fuera_de_servicio', false)->values();
    $ascensoresFuera      = $ascensores->where('fuera_de_servicio', true)->values();

    $cards = [
        ['id'=>'trolebus',        'emoji'=>'🚎', 'titulo'=>__('ui.info.trolebus.titulo'),        'sub'=>__('ui.info.trolebus.sub'),        'color'=>'#f59e0b', 'bg'=>'#fffbeb'],
        ['id'=>'metro',           'emoji'=>'🚆', 'titulo'=>__('ui.info.metro.titulo'),           'sub'=>__('ui.info.metro.sub'),           'color'=>'#3b82f6', 'bg'=>'#eff6ff'],
        ['id'=>'ascensores',      'emoji'=>'🚡', 'titulo'=>__('ui.info.ascensores.titulo'),      'sub'=>__('ui.info.ascensores.sub', ['n' => $ascensoresOperativos->count()]), 'color'=>'#0d9488', 'bg'=>'#f0fdfa'],
        ['id'=>'terminal',        'emoji'=>'🚌', 'titulo'=>__('ui.info.terminal.titulo'),        'sub'=>__('ui.info.terminal.sub'),        'color'=>'#10b981', 'bg'=>'#f0fdf4'],
        ['id'=>'banos',           'emoji'=>'🚻', 'titulo'=>__('ui.info.banos.titulo'),           'sub'=>__('ui.info.banos.sub'),           'color'=>'#8b5cf6', 'bg'=>'#f5f3ff'],
        ['id'=>'cambio',          'emoji'=>'💱', 'titulo'=>__('ui.info.cambio.titulo'),          'sub'=>__('ui.info.cambio.sub'),          'color'=>'#fc5648', 'bg'=>'#fff5f5'],
        ['id'=>'estacionamiento', 'emoji'=>'🅿️', 'titulo'=>__('ui.info.estacionamiento.titulo'),'sub'=>__('ui.info.estacionamiento.sub'), 'color'=>'#64748b', 'bg'=>'#f8fafc'],
    ];
    @endphp

    <div class="space-y-3">

    @php
    $nl = "\n";
    // En la app nativa el host es 127.x.x.x — forzar URL pública para compartir
    $base = str_starts_with(request()->getHost(), '127.') ? 'https://pindoor.cl/info' : url('/info');

    $banosPago   = collect(__('ui.info.banos.pago_list'))
        ->map(fn($r) => "• {$r[0]}" . ($r[1] ? " · {$r[1]}" : '') . " — {$r[2]}")
        ->implode($nl);
    $banosGratis = collect(__('ui.info.banos.gratis_list'))
        ->map(fn($r) => "• {$r[0]}" . ($r[1] ? " · {$r[1]}" : ''))
        ->implode($nl);

    $ascList  = $ascensoresOperativos
        ->map(fn($a) => "• {$a->title}" . ($a->sector ? " ({$a->sector})" : ''))
        ->implode($nl);

    $termDests = collect(__('ui.info.terminal.dests'))
        ->map(fn($r) => "• {$r[0]}: {$r[1]}")
        ->implode($nl);

    $parkins = collect(__('ui.info.estacionamiento.parkins'))
        ->map(fn($r) => "• {$r[1]} — {$r[3]}")
        ->implode($nl);

    $waTexts = [

        'trolebus' => implode($nl, [
            "🚎 *Trolebús Valparaíso*",
            "Línea 802 · Av. Argentina ↔ Aduana",
            "🏆 Sistema de trolebuses más antiguo de América Latina (desde 1952)",
            "",
            "⏰ *Horario:*",
            "• Lunes a Sábado: 07:00 – 22:00",
            "• Domingo y festivos: No opera",
            "",
            "📍 *Recorrido ida → Aduana:*",
            "Av. Argentina › Colón › Edwards › Brasil › Pedro Montt › Blanco Sur › Blanco › Aduana",
            "",
            "📍 *Recorrido vuelta → Av. Argentina:*",
            "Aduana › Bustamante › Serrano/Prat › Esmeralda › Condell › Buenos Aires › Colón › Av. Argentina",
            "",
            "⏱ Frecuencia cada 10–15 min · Se paga con tarjeta Bip!",
            "",
            __('ui.info.wa_intro') . " {$base}#trolebus",
        ]),

        'metro' => implode($nl, [
            "🚆 *Metro / Merval Valparaíso*",
            "Tren Limache – Puerto · 21 estaciones",
            "",
            "⏰ *Horario:*",
            "• Lunes a Viernes: 06:00 – 23:30",
            "• Sábado: 07:30 – 23:30",
            "• Domingo y festivos: 07:30 – 23:30",
            "",
            "⏱ *Frecuencias:*",
            "• L–V punta (Puerto–Sarg. Aldea): c/ 6 min",
            "• L–V punta (Puerto–Limache): c/ 12 min",
            "• L–V fuera punta · Sáb · Dom: c/ 12 min",
            "• Recorrido completo Puerto ↔ Limache: ~50 min",
            "",
            "🚉 *Estaciones:*",
            "Puerto · Bellavista · Francia · Barón · Portales (Valparaíso)",
            "Recreo · Miramar · Viña del Mar · Hospital · Chorrillos · El Salto (Viña)",
            "Quilpué · El Sol · Valencia ✨ · Belloto (Quilpué)",
            "Las Américas · La Concepción · Villa Alemana · Sargento Aldea (Villa Alemana)",
            "Peñablanca · Limache",
            "",
            __('ui.info.wa_intro') . " {$base}#metro",
        ]),

        'terminal' => implode($nl, [
            "🚌 *Terminal de Buses Valparaíso*",
            "📍 Av. Pedro Montt 2860 · Frente al Congreso Nacional",
            "",
            "🗺 *Destinos frecuentes:*",
            $termDests,
            "",
            "🏢 *Empresas:* Turbus · Pullman Bus · Cóndor Bus · Eme Bus · Ciktur · ETM · Sol del Pacífico",
            "",
            "🛎 *Servicios:*",
            "• Duchas: \$1.300",
            "• Custodia bolso pequeño: \$500",
            "• Custodia bolso grande: \$1.000",
            "",
            __('ui.info.wa_intro') . " {$base}#terminal",
        ]),

        'banos' => implode($nl, [
            "🚻 *Baños Públicos en Valparaíso*",
            __('ui.info.wa_intro') . " {$base}#banos",
            "",
            "💰 *Con tarifa:*",
            $banosPago,
            "",
            "🆓 *Gratuitos:*",
            $banosGratis,
            "",

        ]),

        'ascensores' => implode($nl, [
            "🚡 *Ascensores de Valparaíso · 2026*",
            "Patrimonio UNESCO · {$ascensoresOperativos->count()} operativos",
            "",
            "⚠️ No hay garantía al 100% de que funcionen el día de tu visita.",
            "",
            "⏰ *Horario:*",
            "• Lunes a Viernes: 07:00 – 21:30",
            "• Sáb · Dom · Festivos: 08:00 – 21:30",
            "",
            "💰 *Tarifas 2026:*",
            "• Tarifa general: \$200",
            "• Turista extranjero: \$1.000",
            "• Bicicleta (Barón y Cordillera): \$600",
            "• Exentos: menores de 14 · estudiantes · mayores de 60",
            "",
            "✅ *Operativos:*",
            $ascList,
            "",
            __('ui.info.wa_intro') . " {$base}#ascensores",
        ]),

        'cambio' => implode($nl, [
            "💱 *Cambio de Moneda en Valparaíso*",
            "",
            "📍 *Dónde:*",
            "• Cambios FC — Zona calle Prat / Barrio Financiero · Tel. +56 9 6207 4957 · USD · EUR · ARS · BRL",
            "• Calle Prat y alrededores — Varias opciones en la zona financiera",
            "",
            "⏰ *Horario general:*",
            "• Lunes a Viernes: 09:00 – 18:00",
            "• Sábado: 10:00 – 13:00",
            "• Domingo: Cerrado",
            "",
            "💡 *Consejos:*",
            "· Lleva siempre tu cédula o pasaporte — es obligatorio para montos grandes.",
            "· Evita a los captadores en la calle que ofrecen mejor cambio.",
            "· Los cajeros automáticos aceptan Visa/Mastercard internacionales.",
            "",
            __('ui.info.wa_intro') . " {$base}#cambio",
        ]),

        'estacionamiento' => implode($nl, [
            "🅿️ *Estacionamientos en Valparaíso*",
            "",
            "⚠️ Los cerros (Alegre, Concepción, Artillería) tienen calles angostas y estacionamiento muy limitado.",
            "Se recomienda dejar el auto en el Plan y subir a pie, en ascensor o funicular.",
            "",
            "📋 *Opciones:*",
            $parkins,
            "",
            "🚡 *Alternativa recomendada:* Estaciona en el Plan (Av. Argentina) y usa los ascensores o escaleras para subir a los cerros.",
            "",
            __('ui.info.wa_intro') . " {$base}#estacionamiento",
        ]),
    ];
    @endphp

    @foreach($cards as $card)
    @php
        $waRaw    = $waTexts[$card['id']] ?? $card['emoji'] . ' ' . $card['titulo'] . "\n\n" . __('ui.info.wa_intro') . "\n" . $base . '#' . $card['id'];
        $waUrl    = "https://wa.me/?text=" . rawurlencode($waRaw);
        $cardZ    = count($cards) - $loop->index;
    @endphp
    <div id="{{ $card['id'] }}"
         x-data="{ open: window.location.hash === '#{{ $card['id'] }}' }"
         class="relative bg-white rounded-2xl shadow-sm border border-gray-100"
         style="z-index: {{ $cardZ }}">

        <button @click="open = !open"
                class="w-full flex items-center gap-3 px-4 py-4 text-left transition-colors"
                :style="open ? 'background:{{ $card['bg'] }}' : ''">
            <span class="text-2xl shrink-0 leading-none">{{ $card['emoji'] }}</span>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-extrabold text-gray-900">{{ $card['titulo'] }}</p>
                <p class="text-[11px] text-gray-400 truncate mt-0.5">{{ $card['sub'] }}</p>
            </div>
            <span class="shrink-0 w-8 h-8"></span>
            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200"
                 :class="open ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-data="sharePanel()"
             x-init="text = $el.dataset.text"
             data-text="{{ e($waRaw) }}"
             @click.outside="open = false"
             class="absolute right-10 top-3.5 z-20">
            <button @click.stop="click()"
                    class="w-8 h-8 flex items-center justify-center rounded-full text-gray-400 hover:text-[#fc5648] hover:bg-[#fff0ef] transition-colors"
                    title="Compartir">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 style="display:none"
                 class="absolute right-0 top-10 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-999">
                <button @click="wa()"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 shrink-0 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </button>
                <div class="border-t border-gray-100 my-1"></div>
                <button @click="copiar()"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm transition-colors"
                        :class="copiado ? 'text-green-600 bg-green-50' : 'text-gray-700 hover:bg-gray-50'">
                    <svg x-show="!copiado" class="w-4 h-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <svg x-show="copiado" class="w-4 h-4 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="copiado ? '¡Copiado!' : 'Copiar enlace'"></span>
                </button>
            </div>
        </div>

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

                <button @click="$dispatch('mapa-banos')"
                        class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-purple-50 text-purple-700 text-sm font-bold hover:bg-purple-100 transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Ver en mapa
                </button>

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

                <button @click="$dispatch('mapa-banos')"
                    class="w-full mt-1 flex items-center justify-center gap-2 bg-violet-50 hover:bg-violet-100 text-violet-700 font-semibold text-sm rounded-2xl px-4 py-3 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    {{ __('ui.info.banos.mapa') }}
                </button>

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
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                        {{ __('ui.info.ascensores.operativos', ['n' => $ascensoresOperativos->count()]) }}
                    </p>
                    <div class="space-y-0">
                        @foreach($ascensoresOperativos as $ascensor)
                        <div class="flex items-center justify-between py-2 {{ !$loop->last?'border-b border-gray-50':'' }}">
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $ascensor->title }}</p>
                                @if($ascensor->sector)
                                <p class="text-[10px] text-gray-400">{{ $ascensor->sector }}</p>
                                @endif
                            </div>
                            <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full shrink-0">✅</span>
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
                @if($ascensoresFuera->isNotEmpty())
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">{{ __('ui.info.ascensores.fuera') }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ascensoresFuera as $ascensor)
                        <div class="bg-red-50 rounded-xl px-3 py-1.5">
                            <p class="text-xs font-semibold text-red-700">{{ $ascensor->title }}</p>
                            <p class="text-[10px] text-red-400">{{ $ascensor->fuera_de_servicio_motivo ?: 'Sin información adicional' }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

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

{{-- ══ MODAL: Mapa de Baños ═══════════════════════════════════════════════ --}}
<div x-data="mapaBanos()" @mapa-banos.window="abrir()">

    {{-- Backdrop --}}
    <div x-show="abierto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         @click="cerrar()"
         class="fixed inset-0 bg-black/50 z-200">
    </div>

    {{-- Bottom sheet --}}
    <div x-show="abierto"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
         class="fixed bottom-0 left-0 right-0 z-201 bg-white rounded-t-3xl overflow-hidden flex flex-col"
         style="height:88dvh">

        {{-- Handle --}}
        <div class="flex justify-center pt-3 pb-1 shrink-0">
            <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 shrink-0">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5">Info práctica</p>
                <p class="font-extrabold text-gray-900">🚻 Baños Públicos · Valparaíso</p>
            </div>
            <button @click="cerrar()"
                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold hover:bg-gray-200 transition">✕</button>
        </div>

        {{-- Leyenda --}}
        <div class="flex items-center gap-4 px-5 py-2 shrink-0 text-xs font-semibold">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-violet-500 shrink-0"></span> Con tarifa
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span> Gratuito
            </span>
        </div>

        {{-- Mapa --}}
        <div class="flex-1 min-h-0 relative">
            <div id="mapa-banos" class="w-full h-full"></div>
            <div x-show="gpsLoading"
                 class="absolute inset-0 flex flex-col items-center justify-center gap-2 bg-white/70 backdrop-blur-sm z-400 pointer-events-none">
                <svg class="w-8 h-8 text-violet-500 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                <p class="text-xs font-semibold text-violet-600">{{ __('ui.general.localizando') }}</p>
            </div>
        </div>
    </div>
</div>

<script>
function mapaBanos() {
    return {
        abierto: false,
        mapa: null,
        gpsLoading: false,

        abrir() {
            this.abierto = true;
            document.body.style.overflow = 'hidden';
            this.$nextTick(() => this.iniciar());
        },

        cerrar() {
            this.abierto = false;
            document.body.style.overflow = '';
        },

        iniciar() {
            if (this.mapa) { this.mapa.invalidateSize(); return; }

            this.mapa = L.map('mapa-banos', {
                center: [-33.0437, -71.6182],
                zoom: 14,
                zoomControl: true,
                attributionControl: false,
            });

            if (navigator.geolocation) {
                this.gpsLoading = true;
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const uLat = pos.coords.latitude, uLng = pos.coords.longitude;
                        this.mapa.setView([uLat, uLng], 15);
                        L.marker([uLat, uLng], {
                            icon: L.divIcon({
                                className: '',
                                html: '<div style="width:16px;height:16px;background:#fc5648;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(252,86,72,.6)"></div>',
                                iconSize: [16, 16], iconAnchor: [8, 8]
                            })
                        }).addTo(this.mapa).bindPopup('📍 Tú estás aquí');
                        this.gpsLoading = false;
                    },
                    () => { this.gpsLoading = false; },
                    { enableHighAccuracy: true, timeout: 6000 }
                );
            }

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 16, subdomains: 'abcd',
            }).addTo(this.mapa);

            const iconPago   = L.divIcon({ className: '', html: '<div class="bano-dot bano-marker-pago"></div>',   iconSize:[14,14], iconAnchor:[7,7] });
            const iconGratis = L.divIcon({ className: '', html: '<div class="bano-dot bano-marker-gratis"></div>', iconSize:[14,14], iconAnchor:[7,7] });


            //{ n:'Pérgola de las Flores',          r:'Calle Condell · Plaza Aníbal Pinto',       p:'$350', lat:-33.043326, lng:-71.625280 },
                //{ n:'Plaza Victoria',                  r:'Bajo la glorieta',                          p:'$300', lat:-33.046211, lng:-71.620087 },
                //{ n:'Estacionamientos Barrio Puerto', r:'Subterráneos',                       lat:-33.03810, lng:-71.62456 },

            const pagados = [
                
                { n:'Plaza Simón Bolivar',             r:'Detras de juegos',                          p:'$300', lat:-33.045271, lng:-71.619634 },
                { n:'Estación Puerto — 1er piso',      r:'Costado Muelle Prat · buen estado',         p:'$400', lat:-33.038246, lng:-71.627580 },
                { n:'Muelle Prat',                     r:'En Galeria artesanal',                      p:'$500', lat:-33.037299, lng:-71.627599 },

                { n:'Plaza O\'Higgins',                r:'Frente al Teatro Municipal · el más nuevo', p:'$400', lat:-33.047916, lng:-71.607940 },
                { n:'Terminal de Buses',               r:'Costado de la losa de llegada',             p:'$400', lat:-33.046862, lng:-71.606610 },
                { n:'Mercado El Cardonal',             r:'2do piso · amplio y espacioso',             p:'$300', lat:-33.045168, lng:-71.607520 },
               // { n:'Parque Italia',                   r:'Detrás de Plaza Salvador Allende',          p:'$300', lat:-33.047356, lng:-71.613441 },
                { n:'Paseo 21 de Mayo',                 r:'Costado del Museo Naval',                   p:'$300', lat:-33.031343, lng:-71.630563 },
            ];

            const gratuitos = [
                { n:'Mercado Puerto',                  r:'1er piso · moderno y limpio',         lat:-33.035751, lng:-71.630092 },

                { n:'Estación Puerto — 2do piso', r:'Patio de comidas · muchos no lo saben',    lat:-33.038309, lng:-71.627456 },
                { n:'Mall Paseo Ross',            r:'Av. Argentina',                          lat:-33.048921, lng:-71.604269 },
                { n:'Jumbo Portal Valparaíso',    r:'Supermercado',                           lat:-33.044086, lng:-71.604843 },
                { n:'Museo Historia Natural',     r:'Centro',                                 lat:-33.046498, lng:-71.621173 },

                { n:'Biblioteca Santiago Severín',r:'Centro',                                 lat:-33.045031, lng:-71.619328 },
            ];

            pagados.forEach(b => {
                L.marker([b.lat, b.lng], { icon: iconPago })
                    .addTo(this.mapa)
                    .bindPopup(`<div style="font-family:'Plus Jakarta Sans',sans-serif;min-width:160px">
                        <p style="font-weight:800;font-size:13px;margin:0 0 2px">${b.n}</p>
                        <p style="font-size:11px;color:#6b7280;margin:0 0 4px">${b.r}</p>
                        <span style="background:#f3e8ff;color:#7c3aed;font-weight:700;font-size:12px;padding:2px 8px;border-radius:99px">${b.p}</span>
                    </div>`);
            });

            gratuitos.forEach(b => {
                L.marker([b.lat, b.lng], { icon: iconGratis })
                    .addTo(this.mapa)
                    .bindPopup(`<div style="font-family:'Plus Jakarta Sans',sans-serif;min-width:160px">
                        <p style="font-weight:800;font-size:13px;margin:0 0 2px">${b.n}</p>
                        <p style="font-size:11px;color:#6b7280;margin:0 0 4px">${b.r}</p>
                        <span style="background:#d1fae5;color:#065f46;font-weight:700;font-size:12px;padding:2px 8px;border-radius:99px">Gratis</span>
                    </div>`);
            });
        },
    };
}

</script>

@endsection
