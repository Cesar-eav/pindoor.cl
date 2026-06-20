<x-app-layout>

@php
    $punto->loadMissing('imagenes');
    $logoUrl = $punto->imagen_perfil
        ? asset('storage/'.$punto->imagen_perfil)
        : (auth()->user()->imagen_logo ? asset('storage/'.auth()->user()->imagen_logo) : null);

    $tieneModuloAlojamiento = count(array_intersect(['habitaciones','servicios','politicas'], $modulos)) > 0;
    $tieneActividadDiaria   = count(array_intersect(['oferta_del_dia','menu_del_dia','avisos','promociones'], $modulos)) > 0;
    $tieneContenido         = in_array('carta', $modulos) || in_array('entradas', $modulos) || in_array('exposiciones', $modulos) || in_array('agenda', $modulos) || in_array($punto->categoria_id, [13,14]);
    $catalogoServicios      = $tieneModuloAlojamiento ? App\Models\PuntoInteres::catalogoServicios() : [];

    $imagenes      = $punto->imagenes->sortBy('orden')->values();
    $totalImagenes = $imagenes->count();
    $disponibles   = 10 - $totalImagenes;

    $checks = [
        'imagen_perfil' => ['ok' => (bool) $logoUrl,                                               'label' => 'Logo / foto de perfil',    'href' => '#imagen-perfil'],
        'description'   => ['ok' => !empty(trim(strip_tags($punto->description ?? ''))),            'label' => 'Descripción del espacio',  'href' => '#descripcion'],
        'galeria'       => ['ok' => $imagenes->isNotEmpty(),                                         'label' => 'Al menos 1 foto',          'href' => '#galeria'],
        'horario'       => ['ok' => !empty(trim($punto->horario ?? '')),                            'label' => 'Horario de atención',      'href' => '#ubicacion'],
        'direccion'     => ['ok' => !empty(trim($punto->direccion ?? '')),                          'label' => 'Dirección',                'href' => '#ubicacion'],
    ];
    if ($datoCarta !== null) {
        $checks['carta'] = ['ok' => !empty($datoCarta['url'] ?? '') || !empty($datoCarta['texto'] ?? ''), 'label' => 'Carta / Menú', 'href' => '#carta'];
    }
    $pendientes = collect($checks)->where('ok', false);

    $textoMenu      = $punto->dato('menu_del_dia')['texto'] ?? '';
    $textoAviso     = $punto->dato('avisos')['texto'] ?? '';
    $textoPromocion = $punto->dato('promociones')['texto'] ?? '';
@endphp

<div class="flex bg-white" style="min-height: calc(100vh - 3.5rem)">

    {{-- ══════════════════════════════════════════════════════════════
         SIDEBAR IZQUIERDO
         ══════════════════════════════════════════════════════════════ --}}
    <aside class="hidden lg:flex flex-col w-60 shrink-0 sticky top-14 overflow-y-auto" style="height: calc(100vh - 3.5rem); background: #0f172a;">

        {{-- Identidad del negocio --}}
        <div class="p-5" style="border-bottom: 1px solid rgba(255,255,255,0.08)">
            <div class="flex items-center gap-3">

                {{-- Logo clickeable — mini-form, XHR con preview y progreso --}}
                <form id="logo-sidebar-form" method="POST" action="{{ route('cliente.perfil.actualizar', $punto) }}" enctype="multipart/form-data" class="shrink-0">
                    @csrf @method('PUT')
                    <input type="file" id="logo-sidebar-input" name="imagen_perfil"
                           accept="image/jpeg,image/png,image/jpg,image/webp"
                           class="hidden">
                    <label for="logo-sidebar-input" class="relative cursor-pointer group block w-11 h-11">
                        @if($logoUrl)
                            <img id="logo-sidebar-preview" src="{{ $logoUrl }}" alt="Logo" class="w-11 h-11 rounded-xl object-cover" style="border: 2px solid rgba(255,255,255,0.15)">
                        @else
                            <div id="logo-sidebar-placeholder" class="w-11 h-11 rounded-xl flex items-center justify-center text-xl" style="background: rgba(255,255,255,0.08)">🏪</div>
                        @endif
                        {{-- Overlay cámara --}}
                        <div id="logo-camera-overlay" class="absolute inset-0 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity" style="background: rgba(0,0,0,0.55)">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        {{-- Overlay progreso --}}
                        <div id="logo-progress-overlay" class="absolute inset-0 rounded-xl flex flex-col items-center justify-center hidden" style="background: rgba(0,0,0,0.72)">
                            <span id="logo-progress-pct" class="text-white font-black" style="font-size:0.65rem; line-height:1">0%</span>
                        </div>
                    </label>
                </form>

                <div class="min-w-0">
                    <p class="text-sm font-bold text-white truncate leading-tight">{{ $punto->title }}</p>
                    <span class="inline-block mt-1 text-[11px] px-2 py-0.5 rounded-full font-bold {{ $punto->activo ? 'bg-green-500/20 text-green-400' : 'bg-orange-500/20 text-orange-400' }}">
                        {{ $punto->activo ? '● Visible' : '● Pausado' }}
                    </span>
                </div>
            </div>
            @if($pendientes->isNotEmpty())
            <div class="mt-3 rounded-xl px-3 py-2" style="background: rgba(252,86,72,0.15); border: 1px solid rgba(252,86,72,0.3)">
                <p class="text-[11px] font-bold" style="color: #fc5648">⚠ {{ $pendientes->count() }} campo{{ $pendientes->count() > 1 ? 's' : '' }} pendiente{{ $pendientes->count() > 1 ? 's' : '' }}</p>
            </div>
            @endif
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 p-3 space-y-6 overflow-y-auto">

            @if($tieneActividadDiaria)
            <div>
                <p class="sidebar-group-label" style="color: #fc5648">Actividad de hoy</p>
                <div class="space-y-0.5">
                    @if(in_array('oferta_del_dia', $modulos))
                    <a href="#oferta" class="sidebar-link">🏷️ Oferta del día</a>
                    @endif
                    @if(in_array('menu_del_dia', $modulos))
                    <a href="#menu" class="sidebar-link">🥘 Menú del día</a>
                    @endif
                    @if(in_array('avisos', $modulos))
                    <a href="#avisos" class="sidebar-link">📢 Avisos</a>
                    @endif
                    @if(in_array('promociones', $modulos))
                    <a href="#promociones" class="sidebar-link">🎁 Promociones</a>
                    @endif
                </div>
            </div>
            @endif

            <div>
                <p class="sidebar-group-label" style="color: #60a5fa">Tu perfil</p>
                <div class="space-y-0.5">
                    <a href="#descripcion" class="sidebar-link justify-between">
                        <span>📝 Descripción</span>
                        @if(!$checks['description']['ok'])<span class="sidebar-dot"></span>@endif
                    </a>
                    <a href="#galeria" class="sidebar-link justify-between">
                        <span>🖼️ Galería</span>
                        @if(!$checks['galeria']['ok'])<span class="sidebar-dot"></span>@endif
                    </a>
                    <a href="#imagen-perfil" class="sidebar-link justify-between">
                        <span>🏷️ Logo / imagen</span>
                        @if(!$checks['imagen_perfil']['ok'])<span class="sidebar-dot"></span>@endif
                    </a>
                    <a href="#ubicacion" class="sidebar-link justify-between">
                        <span>📍 Ubicación</span>
                        @if(!$checks['horario']['ok'] || !$checks['direccion']['ok'])<span class="sidebar-dot"></span>@endif
                    </a>
                    <a href="#contacto" class="sidebar-link">🔗 Contacto y redes</a>
                    <a href="#busqueda" class="sidebar-link">🔍 Búsqueda SEO</a>
                </div>
            </div>

            @if($tieneContenido || $tieneModuloAlojamiento)
            <div>
                <p class="sidebar-group-label" style="color: #34d399">Contenido</p>
                <div class="space-y-0.5">
                    @if(in_array('carta', $modulos))
                    <a href="#carta" class="sidebar-link justify-between">
                        <span>🍽️ Carta / Menú</span>
                        @if(isset($checks['carta']) && !$checks['carta']['ok'])<span class="sidebar-dot"></span>@endif
                    </a>
                    @endif
                    @if($tieneModuloAlojamiento)
                    <a href="#alojamiento" class="sidebar-link">🛏️ Alojamiento</a>
                    @endif
                    @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos))
                    <a href="{{ route('cliente.museo', $punto) }}" class="sidebar-link sidebar-link-external">🎟️ Museo <span class="ml-auto">↗</span></a>
                    @endif
                    @if(in_array('agenda', $modulos))
                    <a href="{{ route('cliente.eventos', $punto) }}" class="sidebar-link sidebar-link-external">📅 Eventos <span class="ml-auto">↗</span></a>
                    @endif
                    @if(in_array($punto->categoria_id, [13, 14]))
                    <a href="{{ route('cliente.productos.index') }}" class="sidebar-link sidebar-link-external">🛍️ Catálogo <span class="ml-auto">↗</span></a>
                    @endif
                </div>
            </div>
            @endif

        </nav>

        {{-- Footer --}}
        <div class="p-4 space-y-2" style="border-top: 1px solid rgba(255,255,255,0.08)">
            <a href="{{ route('puntos.show', $punto->slug) }}" target="_blank"
               class="flex items-center justify-between text-xs font-medium transition"
               style="color: rgba(255,255,255,0.5)"
               onmouseover="this.style.color='#fc5648'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                Ver ficha pública
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            @if(auth()->user()->puntoInteres()->count() > 1)
            <a href="{{ route('cliente.perfil') }}" class="block text-xs transition" style="color: rgba(255,255,255,0.35)">← Mis negocios</a>
            @endif
        </div>

    </aside>

    {{-- ══════════════════════════════════════════════════════════════
         CONTENIDO PRINCIPAL
         ══════════════════════════════════════════════════════════════ --}}
    <main class="flex-1 min-w-0" style="background: #f8fafc">
        <div class="max-w-3xl mx-auto px-4 lg:px-8 py-8 space-y-8">

            {{-- Flash messages --}}
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">{{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            {{-- Banner de completitud --}}
            @if($pendientes->isNotEmpty())
            <div x-data="{ open: true }" x-show="open">
                <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="text-xl shrink-0 mt-0.5">⚠️</span>
                        <div>
                            <p class="text-sm font-bold text-amber-800 mb-2">Faltan {{ $pendientes->count() }} dato{{ $pendientes->count() > 1 ? 's' : '' }} para que los turistas puedan encontrarte</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($pendientes as $check)
                                <a href="{{ $check['href'] }}"
                                   class="text-xs bg-amber-100 text-amber-700 font-semibold px-3 py-1 rounded-full hover:bg-amber-200 transition">
                                    {{ $check['label'] }} →
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <button @click="open = false" class="text-amber-400 hover:text-amber-600 shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            @endif

            {{-- ──────────────────────────────────────────────────────────────
                 GRUPO: ACTIVIDAD DE HOY (módulos dinámicos, cada uno su form)
                 ────────────────────────────────────────────────────────────── --}}
            @if($tieneActividadDiaria)
            <div>
                <div class="group-header" style="--group-color: #fc5648">
                    <span class="group-header-label" style="color: #fc5648">Actividad de hoy</span>
                    <div class="group-header-line" style="background: linear-gradient(to right, rgba(252,86,72,0.4), transparent)"></div>
                </div>
                <div class="space-y-5">

                    {{-- Oferta del día --}}
                    @if(in_array('oferta_del_dia', $modulos))
                    <div id="oferta" class="section-card section-card-red scroll-mt-20"
                         x-data="{ activa: {{ $punto->oferta_activa ? 'true' : 'false' }} }">
                        <div class="section-card-head flex items-start justify-between">
                            <div>
                                <h3 class="section-title">🏷️ Oferta del día</h3>
                                <p class="section-sub">Aparece como botón en tu ficha mientras esté activa</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer mt-1">
                                <input type="checkbox" x-model="activa" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-green-400 rounded-full peer
                                            peer-checked:after:translate-x-full peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                            after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>
                        <div class="section-card-body">
                        @if($punto->tieneOfertaActiva())
                            <div class="text-xs font-bold text-green-700 bg-green-50 rounded-xl px-3 py-2 mb-4 flex items-center gap-2">
                                🟢 Activa
                                @if($punto->oferta_expira_at) · Vence el {{ $punto->oferta_expira_at->translatedFormat('d \d\e F') }} ({{ $punto->oferta_expira_at->diffForHumans() }}) @else · Sin fecha de vencimiento @endif
                            </div>
                        @elseif($punto->oferta_activa === false && $punto->oferta_del_dia)
                            <div class="text-xs text-gray-400 bg-gray-50 rounded-xl px-3 py-2 mb-4">⚫ Desactivada</div>
                        @elseif($punto->oferta_expira_at && $punto->oferta_expira_at->isPast())
                            <div class="text-xs text-red-500 bg-red-50 rounded-xl px-3 py-2 mb-4">🔴 Expirada el {{ $punto->oferta_expira_at->translatedFormat('d \d\e F') }}</div>
                        @endif
                        <form method="POST" action="{{ route('cliente.oferta.actualizar', $punto) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="oferta_activa" value="0">
                            <input type="checkbox" name="oferta_activa" value="1" x-bind:checked="activa" class="hidden">
                            <div id="oferta-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-28"></div>
                            <textarea id="oferta_del_dia" name="oferta_del_dia" class="hidden">{{ old('oferta_del_dia', $punto->oferta_del_dia) }}</textarea>
                            <div x-show="activa" class="mt-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Vigencia</label>
                                <select name="duracion_dias" class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-green-400">
                                    <option value="">Sin fecha de vencimiento</option>
                                    <option value="1"  @selected(old('duracion_dias') == 1)>Solo hoy (1 día)</option>
                                    <option value="2"  @selected(old('duracion_dias') == 2)>2 días</option>
                                    <option value="3"  @selected(old('duracion_dias') == 3)>3 días</option>
                                    <option value="5"  @selected(old('duracion_dias') == 5)>5 días</option>
                                    <option value="7"  @selected(old('duracion_dias') == 7)>1 semana</option>
                                    <option value="14" @selected(old('duracion_dias') == 14)>2 semanas</option>
                                    <option value="30" @selected(old('duracion_dias') == 30)>1 mes</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Al vencer se ocultará automáticamente del mapa.</p>
                            </div>
                            <div class="flex justify-end mt-4">
                                <button type="submit" class="btn-save btn-save-red" :class="activa ? '' : 'opacity-60'">
                                    <span x-text="activa ? 'Activar oferta' : 'Guardar (desactivada)'"></span>
                                </button>
                            </div>
                        </form>
                        </div>{{-- /section-card-body --}}
                    </div>
                    @endif

                    {{-- Menú del día --}}
                    @if(in_array('menu_del_dia', $modulos))
                    <div id="menu" class="section-card section-card-red scroll-mt-20">
                        <div class="section-card-head flex items-center justify-between">
                            <div>
                                <h3 class="section-title">🥘 Menú del día</h3>
                                <p class="section-sub">Escribe el menú de hoy. Deja vacío para ocultarlo.</p>
                            </div>
                            @if($textoMenu)<span class="status-badge-green">Activo</span>@else<span class="status-badge-gray">Sin publicar</span>@endif
                        </div>
                        <div class="section-card-body">
                            <form method="POST" action="{{ route('cliente.menu.actualizar', $punto) }}">
                                @csrf @method('PATCH')
                                <div id="menu-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-36"></div>
                                <textarea id="menu_del_dia" name="menu_del_dia" class="hidden">{{ old('menu_del_dia', $textoMenu) }}</textarea>
                                <div class="flex justify-end mt-3">
                                    <button type="submit" class="btn-save btn-save-red">Publicar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- Avisos --}}
                    @if(in_array('avisos', $modulos))
                    <div id="avisos" class="section-card section-card-red scroll-mt-20">
                        <div class="section-card-head flex items-center justify-between">
                            <div>
                                <h3 class="section-title">📢 Avisos</h3>
                                <p class="section-sub">Comunicados importantes. Deja vacío para ocultarlo.</p>
                            </div>
                            @if($textoAviso)<span class="status-badge-green">Activo</span>@else<span class="status-badge-gray">Sin publicar</span>@endif
                        </div>
                        <div class="section-card-body">
                            <form method="POST" action="{{ route('cliente.aviso.actualizar', $punto) }}">
                                @csrf @method('PATCH')
                                <div id="aviso-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-28"></div>
                                <textarea id="aviso" name="aviso" class="hidden">{{ old('aviso', $textoAviso) }}</textarea>
                                <div class="flex justify-end mt-3">
                                    <button type="submit" class="btn-save btn-save-red">Publicar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    {{-- Promociones --}}
                    @if(in_array('promociones', $modulos))
                    <div id="promociones" class="section-card section-card-red scroll-mt-20">
                        <div class="section-card-head flex items-center justify-between">
                            <div>
                                <h3 class="section-title">🎁 Promociones</h3>
                                <p class="section-sub">Descuentos y promociones especiales. Deja vacío para ocultarlo.</p>
                            </div>
                            @if($textoPromocion)<span class="status-badge-green">Activa</span>@else<span class="status-badge-gray">Sin publicar</span>@endif
                        </div>
                        <div class="section-card-body">
                            <form method="POST" action="{{ route('cliente.promocion.actualizar', $punto) }}">
                                @csrf @method('PATCH')
                                <div id="promocion-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-28"></div>
                                <textarea id="promocion" name="promocion" class="hidden">{{ old('promocion', $textoPromocion) }}</textarea>
                                <div class="flex justify-end mt-3">
                                    <button type="submit" class="btn-save btn-save-red">Publicar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
            @endif

            {{-- ──────────────────────────────────────────────────────────────
                 GRUPO: TU PERFIL
                 ────────────────────────────────────────────────────────────── --}}
            <div>
                <div class="group-header">
                    <span class="group-header-label" style="color: #3b82f6">Tu perfil</span>
                    <div class="group-header-line" style="background: linear-gradient(to right, rgba(59,130,246,0.4), transparent)"></div>
                </div>
                <div class="space-y-5">

                    {{-- Galería de fotos (su propio form, FUERA de form-perfil) --}}
                    <div id="galeria" class="section-card section-card-blue scroll-mt-20">
                        <div class="section-card-head flex items-center justify-between">
                            <div>
                                <h3 class="section-title">🖼️ Galería de fotos</h3>
                                <p class="section-sub">Las fotos aparecen en tu ficha pública</p>
                            </div>
                            <span class="text-sm font-bold tabular-nums {{ $totalImagenes >= 10 ? 'text-amber-600' : 'text-gray-400' }}">{{ $totalImagenes }}/10</span>
                        </div>
                        <div class="section-card-body">
                        @if($imagenes->isNotEmpty())
                        <div class="grid grid-cols-4 gap-2 mb-4">
                            @foreach($imagenes as $imagen)
                            <div class="relative group aspect-square">
                                <img src="{{ asset('storage/' . $imagen->ruta) }}" alt="Foto"
                                     class="w-full h-full object-cover rounded-xl border border-gray-200 {{ $imagen->es_principal ? 'ring-2 ring-[#fc5648]' : '' }}">
                                @if($imagen->es_principal)
                                    <span class="absolute bottom-1 left-1 text-[9px] bg-[#fc5648] text-white px-1.5 py-0.5 rounded font-bold">★ Principal</span>
                                @endif
                                <form method="POST" action="{{ route('cliente.imagenes.eliminar', [$punto, $imagen]) }}"
                                      class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition"
                                      onsubmit="return confirm('¿Eliminar esta foto?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-6 h-6 bg-red-500 text-white rounded-full text-[11px] flex items-center justify-center hover:bg-red-600 shadow">✕</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        @if($disponibles > 0)
                        <form method="POST" action="{{ route('cliente.imagenes.subir', $punto) }}" enctype="multipart/form-data" id="gallery-form">
                            @csrf
                            <input type="file" name="imagenes[]" id="file-input" accept="image/jpeg,image/png,image/webp" multiple required class="hidden">
                            <div id="drop-zone" class="border-2 border-dashed border-gray-200 rounded-xl p-5 text-center cursor-pointer hover:border-[#fc5648] hover:bg-red-50/40 transition">
                                <div id="upload-placeholder">
                                    <p class="text-2xl mb-1">📷</p>
                                    <p class="text-sm font-semibold text-gray-600">Haz clic o arrastra fotos aquí</p>
                                    <p class="text-xs text-gray-400 mt-0.5">JPG, PNG o WebP · máx. 10 MB c/u · hasta {{ $disponibles }} {{ $disponibles === 1 ? 'foto' : 'fotos' }} más</p>
                                </div>
                                <div id="upload-selection" class="hidden">
                                    <p id="file-count" class="text-sm font-semibold text-gray-700"></p>
                                    <p class="text-xs text-gray-400 mt-0.5">Revisa las fotos abajo y pulsa Subir</p>
                                </div>
                            </div>
                            {{-- Previsualizaciones --}}
                            <div id="gallery-thumbs" class="hidden gap-1.5 mt-3" style="display:none; grid-template-columns: repeat(5, 1fr)"></div>
                            {{-- Barra de progreso --}}
                            <div id="gallery-prog-wrap" class="hidden mt-3">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs text-gray-500 font-medium">Subiendo fotos...</span>
                                    <span id="gallery-prog-pct" class="text-xs font-bold" style="color:#fc5648">0%</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div id="gallery-prog-bar" class="h-full rounded-full" style="width:0%; background:#fc5648; transition: width 0.2s"></div>
                                </div>
                            </div>
                            <div class="flex gap-2 mt-3">
                                <button type="button" id="open-btn" class="flex-1 py-2 bg-gray-800 text-white text-sm font-bold rounded-xl hover:bg-gray-700 transition">Seleccionar fotos</button>
                                <button type="submit" id="upload-btn" class="hidden flex-1 py-2 bg-[#fc5648] text-white text-sm font-bold rounded-xl hover:opacity-90 transition">Subir</button>
                            </div>
                        </form>
                        @else
                        <p class="text-xs text-amber-600 bg-amber-50 rounded-xl px-4 py-3 text-center">Límite de 10 fotos alcanzado. Elimina una para subir otra.</p>
                        @endif
                        </div>{{-- /section-card-body --}}
                    </div>

                    {{-- FORMULARIO DE PERFIL ESTÁTICO --}}
                    <form id="form-perfil" method="POST" action="{{ route('cliente.perfil.actualizar', $punto) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="space-y-5">

                            {{-- Logo / imagen de perfil --}}
                            <div id="imagen-perfil" class="section-card section-card-blue scroll-mt-20"
                                 x-data="{ preview: '{{ $logoUrl ?? '' }}' }">
                                <div class="section-card-head flex items-center justify-between">
                                    <div>
                                        <h3 class="section-title">🏷️ Logo / imagen de perfil</h3>
                                        <p class="section-sub">Aparece junto a tu nombre en el mapa y en tu ficha</p>
                                    </div>
                                    @if(!$logoUrl)<span class="empty-hint">⚠ Sin logo</span>@endif
                                </div>
                                <div class="section-card-body">
                                    <input type="file" name="imagen_perfil" id="imagen_perfil"
                                           accept="image/jpeg,image/png,image/jpg,image/webp"
                                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : preview"
                                           class="hidden" />
                                    <div class="flex items-center gap-6">
                                        <div class="shrink-0 relative group cursor-pointer w-24 h-24"
                                             onclick="document.getElementById('imagen_perfil').click()">
                                            <template x-if="preview">
                                                <img :src="preview" class="w-24 h-24 rounded-2xl object-cover border border-gray-200">
                                            </template>
                                            <template x-if="!preview">
                                                <div class="w-24 h-24 rounded-2xl bg-amber-50 border-2 border-dashed border-amber-300 flex items-center justify-center text-4xl">🏪</div>
                                            </template>
                                            <div class="absolute inset-0 rounded-2xl bg-black/40 flex flex-col items-center justify-center gap-1
                                                        opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span class="text-white text-[10px] font-bold">Cambiar</span>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700 mb-1">Haz clic en la imagen para cambiarla</p>
                                            <p class="text-xs text-gray-400">JPG, PNG o WEBP · Máx. 2 MB</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
                                        <button type="submit" class="btn-save btn-save-blue">Guardar imagen</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Descripción --}}
                            @php $descVacia = empty(trim(strip_tags($punto->description ?? ''))); @endphp
                            <div id="descripcion" class="section-card section-card-blue scroll-mt-20">
                                <div class="section-card-head flex items-center justify-between">
                                    <div>
                                        <h3 class="section-title">📝 Descripción del espacio</h3>
                                        <p class="section-sub">Texto principal visible en tu ficha pública</p>
                                    </div>
                                    @if($descVacia)<span class="empty-hint">⚠ Sin completar</span>@endif
                                </div>
                                <div class="section-card-body">
                                    <div id="description-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-44"></div>
                                    <textarea id="description" name="description" class="hidden">{!! old('description', $punto->description) !!}</textarea>
                                    <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
                                        <button type="submit" class="btn-save btn-save-blue">Guardar descripción</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Ubicación y horario --}}
                            @php
                                $horarioVacio  = empty(trim($punto->horario ?? ''));
                                $direccionVacia = empty(trim($punto->direccion ?? ''));
                                $ubicVacia = $horarioVacio || $direccionVacia;
                            @endphp
                            <div id="ubicacion" class="section-card section-card-blue scroll-mt-20">
                                <div class="section-card-head flex items-center justify-between">
                                    <div>
                                        <h3 class="section-title">📍 Ubicación y horario</h3>
                                        <p class="section-sub">Información práctica para el visitante</p>
                                    </div>
                                    @if($ubicVacia)<span class="empty-hint">⚠ Campos sin completar</span>@endif
                                </div>
                                <div class="section-card-body">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="sm:col-span-2">
                                            <div class="flex items-center gap-2 mb-1">
                                                <x-input-label for="horario" value="Horario de atención" />
                                                @if($horarioVacio)<span class="empty-hint">Sin completar</span>@endif
                                            </div>
                                            <x-text-input id="horario" name="horario" class="block w-full {{ $horarioVacio ? 'border-amber-300 focus:border-amber-400 focus:ring-amber-300' : '' }}"
                                                          value="{{ old('horario', $punto->horario) }}"
                                                          placeholder="Lun–Vie 09:00–20:00" />
                                        </div>
                                        <div>
                                            <x-input-label for="sector" value="Sector / Cerro" />
                                            @include('admin.partials._sector-select', ['selected' => old('sector', $punto->sector)])
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <x-input-label for="direccion" value="Dirección" />
                                                @if($direccionVacia)<span class="empty-hint">Sin completar</span>@endif
                                            </div>
                                            <x-text-input id="direccion" name="direccion" class="block w-full {{ $direccionVacia ? 'border-amber-300 focus:border-amber-400 focus:ring-amber-300' : '' }}"
                                                          value="{{ old('direccion', $punto->direccion) }}"
                                                          placeholder="Calle Example 123" />
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-5 pt-4 border-t border-gray-100">
                                        <button type="submit" class="btn-save btn-save-blue">Guardar ubicación</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Contacto y redes --}}
                            @php
                                $enlaceVacio = empty(trim($punto->enlace ?? ''));
                                $tagsVacios  = empty($punto->tags) || count($punto->tags) === 0;
                                $contactoVacio = $enlaceVacio || $tagsVacios;
                            @endphp
                            <div id="contacto" class="section-card section-card-blue scroll-mt-20">
                                <div class="section-card-head flex items-center justify-between">
                                    <div>
                                        <h3 class="section-title">🔗 Contacto y redes</h3>
                                        <p class="section-sub">Medios para que el visitante te encuentre</p>
                                    </div>
                                    @if($contactoVacio)<span class="empty-hint">⚠ Campos sin completar</span>@endif
                                </div>
                                <div class="section-card-body">
                                    <div class="space-y-4">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <x-input-label for="enlace" value="Web o Instagram" />
                                                @if($enlaceVacio)<span class="empty-hint">Sin completar</span>@endif
                                            </div>
                                            <x-text-input id="enlace" name="enlace" type="url" class="block w-full {{ $enlaceVacio ? 'border-amber-300 focus:border-amber-400 focus:ring-amber-300' : '' }}"
                                                          value="{{ old('enlace', $punto->enlace) }}"
                                                          placeholder="https://instagram.com/minegocio" />
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <x-input-label for="tags" value="Etiquetas (separadas por coma)" />
                                                @if($tagsVacios)<span class="empty-hint">Sin completar</span>@endif
                                            </div>
                                            <x-text-input id="tags" name="tags" class="block w-full {{ $tagsVacios ? 'border-amber-300 focus:border-amber-400 focus:ring-amber-300' : '' }}"
                                                          value="{{ old('tags', is_array($punto->tags) ? implode(', ', $punto->tags) : '') }}"
                                                          placeholder="café, vegano, terraza, wifi" />
                                            <p class="text-xs text-gray-400 mt-1">Aparecen como chips en tu ficha pública.</p>
                                        </div>
                                        <div>
                                            <x-input-label for="video_url" value="Video de YouTube (opcional)" />
                                            <x-text-input id="video_url" name="video_url" type="url" class="block mt-1 w-full"
                                                          value="{{ old('video_url', $punto->video_url) }}"
                                                          placeholder="https://www.youtube.com/watch?v=..." />
                                            <p class="text-xs text-gray-400 mt-1">Se incrusta en tu ficha pública.</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-5 pt-4 border-t border-gray-100">
                                        <button type="submit" class="btn-save btn-save-blue">Guardar contacto</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Búsqueda SEO --}}
                            @php $busquedaVacia = empty(trim($punto->descripcion_busqueda ?? '')); @endphp
                            <div id="busqueda" class="section-card section-card-amber scroll-mt-20">
                                <div class="section-card-head flex items-center justify-between">
                                    <div>
                                        <h3 class="section-title">🔍 Perfil de búsqueda</h3>
                                        <p class="section-sub">Invisible para turistas, pero clave para que te encuentren en el buscador.</p>
                                    </div>
                                    @if($busquedaVacia)<span class="empty-hint">⚠ Sin completar</span>@endif
                                </div>
                                <div class="section-card-body">
                                    <p class="text-xs text-amber-700 mb-3">Describe en detalle: tipo de café, platos, ambiente, opciones especiales, servicios, etc.</p>
                                    <textarea id="descripcion_busqueda" name="descripcion_busqueda" rows="6"
                                              class="block w-full border-amber-200 bg-amber-50 rounded-xl shadow-sm text-sm focus:ring-amber-400 resize-none"
                                              placeholder="Cafetería especialidad, V60, chemex, leche de avena, desayunos, vegano, sin gluten, terraza, perros permitidos, wifi...">{{ old('descripcion_busqueda', $punto->descripcion_busqueda) }}</textarea>
                                    <div class="flex justify-end mt-4 pt-4 border-t border-amber-100">
                                        <button type="submit" class="btn-save" style="background:#f59e0b" onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'">Guardar</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

            {{-- ──────────────────────────────────────────────────────────────
                 GRUPO: CONTENIDO (formularios del perfil estático continuado)
                 ────────────────────────────────────────────────────────────── --}}
            @if($tieneContenido || $tieneModuloAlojamiento)
            <div>
                <div class="group-header">
                    <span class="group-header-label" style="color: #10b981">Contenido</span>
                    <div class="group-header-line" style="background: linear-gradient(to right, rgba(16,185,129,0.4), transparent)"></div>
                </div>
                <div class="space-y-5">

                    {{-- Carta / Menú permanente --}}
                    @if(in_array('carta', $modulos))
                    <form method="POST" action="{{ route('cliente.perfil.actualizar', $punto) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div id="carta" class="section-card section-card-green scroll-mt-20">
                            <div class="section-card-head">
                                <h3 class="section-title">🍽️ Carta / Menú permanente</h3>
                                <p class="section-sub">Se muestra en tu ficha pública con el botón "Ver carta"</p>
                            </div>
                            <div class="section-card-body">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                    <div>
                                        <x-input-label for="carta" value="Descripción de la carta" />
                                        <div id="carta-editor" class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-44"></div>
                                        <textarea id="carta" name="carta" class="hidden">{!! old('carta', $datoCarta['texto'] ?? '') !!}</textarea>
                                    </div>
                                    <div>
                                        <x-input-label for="carta_pdf" value="Carta en PDF (opcional)" />
                                        @if($datoCarta['pdf_ruta'] ?? null)
                                            <div class="flex items-center gap-3 mt-1 mb-2">
                                                <a href="{{ asset('storage/' . $datoCarta['pdf_ruta']) }}" target="_blank"
                                                   class="text-xs text-[#fc5648] font-bold hover:underline">📄 Ver carta actual</a>
                                                <label class="flex items-center gap-1 text-xs text-gray-400 cursor-pointer">
                                                    <input type="checkbox" name="eliminar_carta_pdf" value="1" class="rounded"> Eliminar PDF
                                                </label>
                                            </div>
                                        @endif
                                        <input type="file" name="carta_pdf" id="carta_pdf" accept="application/pdf"
                                               class="block w-full mt-1 text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer" />
                                        <p class="text-xs text-gray-400 mt-1">Solo PDF · Máx. 5 MB</p>
                                    </div>
                                </div>
                                <div class="flex justify-end mt-5 pt-4 border-t border-gray-100">
                                    <button type="submit" class="btn-save btn-save-green">Guardar carta</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endif

                    {{-- Alojamiento --}}
                    @if($tieneModuloAlojamiento)
                    <form method="POST" action="{{ route('cliente.perfil.actualizar', $punto) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div id="alojamiento" class="section-card section-card-green scroll-mt-20">
                            <div class="section-card-head">
                                <h3 class="section-title">🛏️ Alojamiento</h3>
                                <p class="section-sub">Información específica para huéspedes</p>
                            </div>
                            <div class="section-card-body">
                            <div class="space-y-5">
                                @if(in_array('habitaciones', $modulos))
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <x-input-label for="precio_desde" value="Precio desde" />
                                        <x-text-input id="precio_desde" name="precio_desde" class="block mt-1 w-full"
                                                      value="{{ old('precio_desde', $datoAlojamiento['precio_desde'] ?? '') }}"
                                                      placeholder="Ej: $15.000 / noche" />
                                    </div>
                                    <div>
                                        <x-input-label for="check_in" value="Check-in" />
                                        <x-text-input id="check_in" name="check_in" class="block mt-1 w-full"
                                                      value="{{ old('check_in', $datoAlojamiento['entrada'] ?? '') }}"
                                                      placeholder="14:00" />
                                    </div>
                                    <div>
                                        <x-input-label for="check_out" value="Check-out" />
                                        <x-text-input id="check_out" name="check_out" class="block mt-1 w-full"
                                                      value="{{ old('check_out', $datoAlojamiento['salida'] ?? '') }}"
                                                      placeholder="11:00" />
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="habitaciones" value="Habitaciones disponibles" />
                                    <div id="habitaciones-editor" class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-40"></div>
                                    <textarea id="habitaciones" name="habitaciones" class="hidden">{!! old('habitaciones', $datoAlojamiento['habitaciones'] ?? '') !!}</textarea>
                                </div>
                                @endif
                                @if(in_array('servicios', $modulos))
                                <div>
                                    <x-input-label value="Servicios incluidos" />
                                    <p class="text-xs text-gray-400 mt-1 mb-4">Selecciona todo lo que ofreces.</p>
                                    @php $seleccionados = old('servicios_incluidos', $datoAlojamiento['servicios'] ?? []); @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                                        @foreach($catalogoServicios as $grupo => $servicios)
                                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $grupo }}</p>
                                            </div>
                                            <div class="p-2 space-y-1">
                                                @foreach($servicios as $slug => $servicio)
                                                <label class="flex items-center gap-2 cursor-pointer hover:bg-indigo-50 border border-transparent hover:border-indigo-200 rounded-xl px-3 py-2 transition">
                                                    <input type="checkbox" name="servicios_incluidos[]" value="{{ $slug }}"
                                                           @checked(in_array($slug, $seleccionados))
                                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400">
                                                    <span class="text-base">{{ $servicio['emoji'] }}</span>
                                                    <span class="text-xs font-medium text-gray-700">{{ $servicio['label'] }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @if(in_array('politicas', $modulos))
                                <div>
                                    <x-input-label for="politicas" value="Políticas del establecimiento" />
                                    <div id="politicas-editor" class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-40"></div>
                                    <textarea id="politicas" name="politicas" class="hidden">{!! old('politicas', $datoAlojamiento['politicas'] ?? '') !!}</textarea>
                                </div>
                                @endif
                            </div>
                            <div class="flex justify-end mt-5 pt-4 border-t border-gray-100">
                                <button type="submit" class="btn-save btn-save-green">Guardar alojamiento</button>
                            </div>
                            </div>{{-- /section-card-body --}}
                        </div>
                    </form>
                    @endif

                    {{-- Links a secciones externas --}}
                    @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos) || in_array('agenda', $modulos) || in_array($punto->categoria_id, [13,14]))
                    <div class="section-card section-card-green">
                        <div class="section-card-head">
                            <h3 class="section-title">⚙️ Gestión avanzada</h3>
                            <p class="section-sub">Secciones con su propio panel de edición</p>
                        </div>
                        <div class="section-card-body">
                        <div class="space-y-2">
                            @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos))
                            <a href="{{ route('cliente.museo', $punto) }}"
                               class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-amber-300 hover:bg-amber-50 transition group">
                                <span class="text-xl">🎟️</span>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-800">Entradas y exposiciones</p>
                                    <p class="text-xs text-gray-400">Tarifas y colecciones del museo</p>
                                </div>
                                <span class="text-gray-300 group-hover:text-amber-400 text-lg">›</span>
                            </a>
                            @endif
                            @if(in_array('agenda', $modulos))
                            <a href="{{ route('cliente.eventos', $punto) }}"
                               class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50 transition group">
                                <span class="text-xl">📅</span>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-800">Agenda de eventos</p>
                                    <p class="text-xs text-gray-400">Obras, conciertos y actividades</p>
                                </div>
                                <span class="text-gray-300 group-hover:text-blue-400 text-lg">›</span>
                            </a>
                            @endif
                            @if(in_array($punto->categoria_id, [13, 14]))
                            @php $totalProductos = $punto->productos()->count(); @endphp
                            <a href="{{ route('cliente.productos.index') }}"
                               class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-[#fc5648]/40 hover:bg-red-50 transition group">
                                <span class="text-xl">🛍️</span>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-800">Catálogo de productos</p>
                                    <p class="text-xs {{ $totalProductos ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ $totalProductos ? $totalProductos . ' producto' . ($totalProductos !== 1 ? 's' : '') : 'Sin productos aún' }}
                                    </p>
                                </div>
                                <span class="text-gray-300 group-hover:text-[#fc5648] text-lg">›</span>
                            </a>
                            @endif
                        </div>
                        </div>{{-- /section-card-body --}}
                    </div>
                    @endif

                </div>
            </div>
            @endif

            <div class="pb-12"></div>

        </div>
    </main>

</div>

{{-- Scripts --}}
@vite('resources/js/quill-editor.js')

<style>
/* ── Sidebar ─────────────────────────────────────── */
.sidebar-group-label {
    font-size: 0.65rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 0 0.75rem;
    margin-bottom: 0.375rem;
    display: block;
}
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.625rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: rgba(255,255,255,0.6);
    transition: all 0.15s;
    text-decoration: none;
}
.sidebar-link:hover { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.95); }
.sidebar-link.active { background: rgba(252,86,72,0.18); color: #fc5648; font-weight: 700; }
.sidebar-link-external { color: rgba(255,255,255,0.35); font-size: 0.75rem; }
.sidebar-link-external:hover { color: rgba(255,255,255,0.65); }
.sidebar-dot { width: 0.5rem; height: 0.5rem; background: #f59e0b; border-radius: 9999px; flex-shrink: 0; margin-left: auto; }

/* ── Separadores de grupo ────────────────────────── */
.group-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}
.group-header-label {
    font-size: 0.7rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    white-space: nowrap;
}
.group-header-line { flex: 1; height: 2px; border-radius: 9999px; }

/* ── Section cards ───────────────────────────────── */
.section-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 1px 4px 0 rgb(0 0 0 / .06), 0 0 0 1px rgb(0 0 0 / .04);
    border: none;
    padding: 0;
    overflow: hidden;
}
.section-card-head {
    padding: 1.1rem 1.5rem 0.9rem;
    border-bottom: 1px solid #f3f4f6;
}
.section-card-body { padding: 1.25rem 1.5rem 1.5rem; }
.section-card-red  .section-card-head { background: linear-gradient(135deg, #fff5f5 0%, #fff 100%); border-left: 3px solid #fc5648; }
.section-card-blue .section-card-head { background: linear-gradient(135deg, #eff6ff 0%, #fff 100%); border-left: 3px solid #3b82f6; }
.section-card-green .section-card-head { background: linear-gradient(135deg, #f0fdf4 0%, #fff 100%); border-left: 3px solid #10b981; }
.section-card-amber .section-card-head { background: linear-gradient(135deg, #fffbeb 0%, #fff 100%); border-left: 3px solid #f59e0b; }

.section-title { font-size: 0.9375rem; font-weight: 800; color: #0f172a; }
.section-sub   { font-size: 0.75rem; color: #94a3b8; margin-top: 0.125rem; }

/* ── Botones de guardar por grupo ────────────────── */
.btn-save {
    display: inline-flex; align-items: center;
    padding: 0.5rem 1.25rem;
    font-size: 0.8125rem; font-weight: 700;
    border-radius: 0.625rem; border: none; cursor: pointer;
    transition: all 0.15s;
    color: white;
}
.btn-save-red   { background: #fc5648; }
.btn-save-red:hover { background: #e04a3c; }
.btn-save-blue  { background: #3b82f6; }
.btn-save-blue:hover { background: #2563eb; }
.btn-save-green { background: #10b981; }
.btn-save-green:hover { background: #059669; }
.btn-save { background: #fc5648; }
.btn-save:hover { background: #e04a3c; }

/* ── Status badges ───────────────────────────────── */
.status-badge-green { font-size: 0.7rem; font-weight: 700; background: #dcfce7; color: #15803d; padding: 0.2rem 0.7rem; border-radius: 9999px; }
.status-badge-gray  { font-size: 0.7rem; font-weight: 700; background: #f1f5f9; color: #94a3b8; padding: 0.2rem 0.7rem; border-radius: 9999px; }
.empty-hint { font-size: 0.68rem; font-weight: 700; background: #fffbeb; color: #d97706; border: 1px solid #fde68a; padding: 0.1rem 0.5rem; border-radius: 9999px; white-space: nowrap; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Quill editors ───────────────────────────────────────────────
    const toolbar = [['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],['clean']];
    const toolbarFull = [['bold','italic','underline'],[{'list':'ordered'},{'list':'bullet'}],[{'header':[2,3,false]}],['clean']];

    function initEditor(editorId, textareaId, formEl, fullToolbar) {
        const editorEl = document.querySelector(editorId);
        const textarea = document.querySelector(textareaId);
        if (!editorEl || !textarea) return;
        const q = new Quill(editorEl, { theme: 'snow', modules: { toolbar: fullToolbar ? toolbarFull : toolbar } });
        if (textarea.value.trim()) q.clipboard.dangerouslyPasteHTML(textarea.value);
        q.on('text-change', () => { textarea.value = q.root.innerHTML; });
        if (formEl) formEl.addEventListener('submit', () => { textarea.value = q.root.innerHTML; });
    }

    @if(in_array('oferta_del_dia', $modulos))
    initEditor('#oferta-editor', '#oferta_del_dia',
        document.querySelector('form[action="{{ route('cliente.oferta.actualizar', $punto) }}"]'));
    @endif
    @if(in_array('menu_del_dia', $modulos))
    initEditor('#menu-editor', '#menu_del_dia',
        document.querySelector('form[action="{{ route('cliente.menu.actualizar', $punto) }}"]'));
    @endif
    @if(in_array('avisos', $modulos))
    initEditor('#aviso-editor', '#aviso',
        document.querySelector('form[action="{{ route('cliente.aviso.actualizar', $punto) }}"]'));
    @endif
    @if(in_array('promociones', $modulos))
    initEditor('#promocion-editor', '#promocion',
        document.querySelector('form[action="{{ route('cliente.promocion.actualizar', $punto) }}"]'));
    @endif

    initEditor('#description-editor', '#description', document.getElementById('form-perfil'), true);

    @if(in_array('carta', $modulos))
    initEditor('#carta-editor', '#carta', null, true);
    @endif
    @if($tieneModuloAlojamiento)
    initEditor('#habitaciones-editor', '#habitaciones', null, true);
    initEditor('#politicas-editor',    '#politicas',    null, true);
    @endif

    // ── Sync Quill textareas on any form submit ──────────────────────
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            this.querySelectorAll('.ql-editor').forEach(editor => {
                const container = editor.closest('[id$="-editor"]');
                if (!container) return;
                const textareaId = '#' + container.id.replace('-editor', '');
                const textarea = document.querySelector(textareaId);
                if (textarea) textarea.value = editor.innerHTML;
            });
        });
    });

    // ── Galería: file picker con preview y progreso ─────────────────
    const fileInput     = document.getElementById('file-input');
    const dropZone      = document.getElementById('drop-zone');
    const placeholder   = document.getElementById('upload-placeholder');
    const selectionInfo = document.getElementById('upload-selection');
    const fileCountEl   = document.getElementById('file-count');
    const openBtn       = document.getElementById('open-btn');
    const uploadBtn     = document.getElementById('upload-btn');
    const galleryForm   = document.getElementById('gallery-form');
    const galleryThumbs = document.getElementById('gallery-thumbs');
    const progWrap      = document.getElementById('gallery-prog-wrap');
    const progBar       = document.getElementById('gallery-prog-bar');
    const progPct       = document.getElementById('gallery-prog-pct');

    if (fileInput && dropZone) {
        dropZone.addEventListener('click', () => fileInput.click());
        openBtn?.addEventListener('click', (e) => { e.stopPropagation(); fileInput.click(); });

        dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-[#fc5648]','bg-red-50/40'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-[#fc5648]','bg-red-50/40'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('border-[#fc5648]','bg-red-50/40');
            const dt = new DataTransfer();
            Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        });

        fileInput.addEventListener('change', function () {
            const files = Array.from(this.files);
            if (!files.length) return;

            // Contador
            fileCountEl.textContent = files.length === 1 ? '1 foto seleccionada' : `${files.length} fotos seleccionadas`;
            placeholder?.classList.add('hidden');
            selectionInfo?.classList.remove('hidden');
            openBtn?.classList.add('hidden');
            uploadBtn?.classList.remove('hidden');

            // Previsualizaciones en miniatura
            galleryThumbs.innerHTML = '';
            galleryThumbs.style.display = 'grid';
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.cssText = 'width:100%;aspect-ratio:1;object-fit:cover;border-radius:0.625rem;border:1px solid #e5e7eb';
                    galleryThumbs.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });

        // Subida vía XHR con progreso
        galleryForm?.addEventListener('submit', function (e) {
            e.preventDefault();

            uploadBtn.disabled  = true;
            uploadBtn.textContent = 'Subiendo…';
            progWrap.classList.remove('hidden');

            const fd  = new FormData(this);
            const xhr = new XMLHttpRequest();

            xhr.upload.onprogress = ev => {
                if (!ev.lengthComputable) return;
                const pct = Math.round(ev.loaded / ev.total * 100);
                progBar.style.width = pct + '%';
                progPct.textContent = pct + '%';
            };

            xhr.onload = () => {
                progBar.style.width = '100%';
                progPct.textContent = '100%';
                setTimeout(() => window.location.reload(), 400);
            };

            xhr.onerror = () => {
                progWrap.classList.add('hidden');
                uploadBtn.disabled   = false;
                uploadBtn.textContent = 'Subir';
                alert('Error al subir las fotos. Intenta de nuevo.');
            };

            xhr.open('POST', this.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.send(fd);
        });
    }

    // ── Logo sidebar: preview inmediato + XHR con progreso ──────────
    const logoInput    = document.getElementById('logo-sidebar-input');
    const logoForm     = document.getElementById('logo-sidebar-form');
    const logoOverlay  = document.getElementById('logo-progress-overlay');
    const logoPct      = document.getElementById('logo-progress-pct');
    const camOverlay   = document.getElementById('logo-camera-overlay');

    logoInput?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Preview inmediato
        const reader = new FileReader();
        reader.onload = ev => {
            let preview = document.getElementById('logo-sidebar-preview');
            const holder = document.getElementById('logo-sidebar-placeholder');
            if (!preview) {
                preview = document.createElement('img');
                preview.id        = 'logo-sidebar-preview';
                preview.className = 'w-11 h-11 rounded-xl object-cover';
                preview.style.border = '2px solid rgba(255,255,255,0.15)';
                holder?.replaceWith(preview);
            }
            preview.src = ev.target.result;
        };
        reader.readAsDataURL(file);

        // Ocultar cámara, mostrar progreso
        camOverlay?.classList.add('!opacity-0');
        logoOverlay?.classList.remove('hidden');

        // XHR
        const fd  = new FormData(logoForm);
        const xhr = new XMLHttpRequest();

        xhr.upload.onprogress = ev => {
            if (!ev.lengthComputable) return;
            const p = Math.round(ev.loaded / ev.total * 100);
            if (logoPct) logoPct.textContent = p + '%';
        };

        xhr.onload = () => {
            if (logoPct) logoPct.textContent = '✓';
            setTimeout(() => window.location.reload(), 500);
        };

        xhr.onerror = () => {
            logoOverlay?.classList.add('hidden');
            camOverlay?.classList.remove('!opacity-0');
        };

        xhr.open('POST', logoForm.action);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(fd);
    });

    // ── Sidebar active highlight on scroll ──────────────────────────
    const sections = document.querySelectorAll('[id]');
    const sidebarLinks = document.querySelectorAll('.sidebar-link[href^="#"]');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                sidebarLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + entry.target.id) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    sections.forEach(s => observer.observe(s));
});
</script>

</x-app-layout>
