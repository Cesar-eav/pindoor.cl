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
        'sector'        => ['ok' => !empty(trim($punto->sector ?? '')),                             'label' => 'Sector / Cerro',           'href' => '#ubicacion'],
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

    @include('cliente.partials._sidebar', ['punto' => $punto])

    {{-- ══════════════════════════════════════════════════════════════
         CONTENIDO PRINCIPAL
         ══════════════════════════════════════════════════════════════ --}}
    <main class="flex-1 min-w-0" style="background: #f8fafc">
        <div class="max-w-3xl mx-auto px-4 lg:px-8 py-8 space-y-8">

            {{-- Flash messages --}}
            @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">{{ session('error') }}</div>
            @endif
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            {{-- Popup de datos pendientes --}}
            @if($pendientes->isNotEmpty())
            <div x-data="{ open: true }" x-show="open"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 px-4">
                <div class="relative bg-white text-gray-900 rounded-2xl shadow-xl max-w-md w-full p-6">
                    <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    <div class="flex items-start gap-3 mb-4">
                        <span class="w-10 h-10 shrink-0 rounded-full flex items-center justify-center text-lg" style="background: #fff0ef">✨</span>
                        <p class="font-extrabold text-lg leading-snug pr-6">
                            Te falta{{ $pendientes->count() > 1 ? 'n' : '' }} {{ $pendientes->count() }} dato{{ $pendientes->count() > 1 ? 's' : '' }} para que las personas puedan encontrarte
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pendientes as $check)
                        <a href="{{ $check['href'] }}" @click="open = false"
                           class="text-xs font-semibold px-3 py-1.5 rounded-full transition hover:opacity-80"
                           style="background: #fff0ef; color: #fc5648">
                            {{ $check['label'] }} →
                        </a>
                        @endforeach
                    </div>
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
                                     class="w-full h-full object-cover rounded-xl border-2 {{ $imagen->es_principal ? 'border-[#fc5648] ring-2 ring-[#fc5648] ring-offset-1' : 'border-gray-200' }}">
                                @if($imagen->es_principal)
                                    <span class="absolute top-0 left-0 right-0 text-[10px] bg-[#fc5648] text-white text-center py-1 rounded-t-lg font-extrabold uppercase tracking-wide shadow-sm">
                                        ★ Portada de tu ficha
                                    </span>
                                @endif
                                <form method="POST" action="{{ route('cliente.imagenes.eliminar', [$punto, $imagen]) }}"
                                      class="absolute top-1 right-1 z-10 opacity-0 group-hover:opacity-100 transition"
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
                                            <p class="text-xs text-gray-400">JPG, PNG o WEBP · Máx. 20 MB</p>
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
                                $sectorVacio    = empty(trim($punto->sector ?? ''));
                                $ubicVacia = $horarioVacio || $direccionVacia || $sectorVacio;
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
                                            <x-input-label for="categoria_id" value="Categoría" />
                                            <select name="categoria_id" id="categoria_id"
                                                    class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm text-sm focus:ring-blue-400 focus:border-blue-400">
                                                @foreach($categorias as $cat)
                                                    <option value="{{ $cat->id }}" {{ old('categoria_id', $punto->categoria_id) == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
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
                                            <div class="flex items-center gap-2 mb-1">
                                                <x-input-label for="sector" value="Sector / Cerro" />
                                                @if($sectorVacio)<span class="empty-hint">Sin completar</span>@endif
                                            </div>
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
                                        <x-input-label for="carta-texto" value="Descripción de la carta" />
                                        <div id="carta-editor" class="mt-1 bg-white border border-gray-200 rounded-xl text-sm min-h-44"></div>
                                        <textarea id="carta-texto" name="carta" class="hidden">{!! old('carta', $datoCarta['texto'] ?? '') !!}</textarea>
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
                                        <p class="text-xs text-gray-400 mt-1">Solo PDF · Máx. 30 MB</p>
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

                    {{-- ===== MUSEO: tarifas de entrada + exposiciones ===== --}}
                    @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos))
                    <div id="museo" class="scroll-mt-20 space-y-4">

                        @if($punto->moduloActivo('entradas'))
                        <div x-data="{
                            entradas: {{ json_encode($entradas->map(fn($e) => ['etiqueta' => $e->datos['etiqueta'] ?? '', 'precio' => $e->datos['precio'] ?? 0, 'nota' => $e->datos['nota'] ?? ''])->values()) }},
                            agregar() {
                                this.entradas.push({ label: '', precio: 0, descripcion: '' });
                            },
                            quitar(i) {
                                this.entradas.splice(i, 1);
                            }
                        }" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h4 class="font-bold text-gray-700">🎟️ Tarifas de entrada</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">Define los tipos de entrada y sus precios. Precio 0 = Gratuito.</p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('cliente.museo.entradas.guardar', $punto) }}">
                                @csrf

                                <div class="space-y-3 mb-4">
                                    <template x-for="(entrada, i) in entradas" :key="i">
                                        <div class="flex gap-3 items-start">
                                            <div class="flex-1">
                                                <input type="text"
                                                       :name="`entradas[${i}][etiqueta]`"
                                                       x-model="entrada.etiqueta"
                                                       placeholder="Ej: Adulto, Niño menor de 12, Estudiante…"
                                                       class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-amber-400">
                                            </div>
                                            <div class="w-32">
                                                <input type="number"
                                                       :name="`entradas[${i}][precio]`"
                                                       x-model="entrada.precio"
                                                       min="0" step="100"
                                                       placeholder="0"
                                                       class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-amber-400">
                                            </div>
                                            <div class="flex-1">
                                                <input type="text"
                                                       :name="`entradas[${i}][nota]`"
                                                       x-model="entrada.nota"
                                                       placeholder="Nota opcional (ej: Gratis domingos)"
                                                       class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-amber-400">
                                            </div>
                                            <button type="button" @click="quitar(i)"
                                                    class="mt-1 text-red-400 hover:text-red-600 transition text-lg leading-none">✕</button>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex items-center justify-between">
                                    <button type="button" @click="agregar()"
                                            class="text-sm text-amber-700 font-bold hover:underline">
                                        + Agregar tipo de entrada
                                    </button>
                                    <button type="submit"
                                            class="btn-save-track px-5 py-2 bg-amber-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                                        Guardar tarifas
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if($punto->moduloActivo('exposiciones'))
                        <div x-data="{ modalAbierto: false, editando: null }" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-bold text-gray-700">🖼️ Exposiciones</h4>
                                    <p class="text-xs text-gray-400 mt-0.5">Gestiona las colecciones permanentes y exposiciones temporales.</p>
                                </div>
                                <button @click="modalAbierto = true; editando = null"
                                        class="px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                                    + Nueva
                                </button>
                            </div>

                            {{-- Listado --}}
                            @if($exposiciones->count())
                            <div class="space-y-3 mb-2">
                                @foreach($exposiciones as $expo)
                                @php
                                    $expoParaEditar = [
                                        'id'           => $expo->id,
                                        'titulo'       => $expo->datos['titulo']      ?? '',
                                        'descripcion'  => $expo->datos['descripcion'] ?? '',
                                        'tipo'         => $expo->datos['tipo']        ?? 'temporal',
                                        'fecha_inicio' => $expo->datos['fecha_inicio'] ?? null,
                                        'fecha_fin'    => $expo->datos['fecha_fin']   ?? null,
                                        'imagen'       => $expo->imagen,
                                    ];
                                @endphp
                                <div class="flex items-start gap-4 border border-gray-100 rounded-xl p-4">
                                    @if($expo->imagen)
                                        <img src="{{ asset('storage/' . $expo->imagen) }}" alt="{{ $expo->datos['titulo'] ?? '' }}"
                                             class="w-16 h-16 rounded-xl object-cover border border-gray-100 shrink-0">
                                    @else
                                        <div class="w-16 h-16 rounded-xl bg-gray-50 flex items-center justify-center text-2xl shrink-0 border border-gray-100">🖼️</div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-semibold text-gray-800 text-sm">{{ $expo->datos['titulo'] ?? '' }}</p>
                                            @php $tipoExpo = $expo->datos['tipo'] ?? 'temporal'; @endphp
                                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full
                                                {{ $tipoExpo === 'permanente' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                                {{ $tipoExpo === 'permanente' ? 'Permanente' : 'Temporal' }}
                                            </span>
                                        </div>
                                        @if($expo->datos['descripcion'] ?? null)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $expo->datos['descripcion'] }}</p>
                                        @endif
                                        @if($tipoExpo === 'temporal' && (($expo->datos['fecha_inicio'] ?? null) || ($expo->datos['fecha_fin'] ?? null)))
                                            <p class="text-xs text-gray-400 mt-1">
                                                📅
                                                {{ $expo->datos['fecha_inicio'] ? \Carbon\Carbon::parse($expo->datos['fecha_inicio'])->translatedFormat('d M Y') : '—' }}
                                                →
                                                {{ $expo->datos['fecha_fin'] ? \Carbon\Carbon::parse($expo->datos['fecha_fin'])->translatedFormat('d M Y') : 'Sin fecha fin' }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex gap-2 shrink-0">
                                        <button @click="editando = {{ json_encode($expoParaEditar) }}; modalAbierto = true"
                                                class="text-xs text-blue-600 font-bold hover:underline">Editar</button>
                                        <form method="POST" action="{{ route('cliente.museo.exposicion.eliminar', [$punto, $expo]) }}"
                                              onsubmit="return confirm('¿Eliminar esta exposición?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 font-bold hover:underline">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                                <p class="text-sm text-gray-400 italic py-4 text-center">No hay exposiciones registradas aún.</p>
                            @endif

                            {{-- Modal nueva/editar exposición --}}
                            <div x-show="modalAbierto" x-cloak
                                 class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
                                 @keydown.escape.window="modalAbierto = false">
                                <div @click.stop class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
                                    <div class="flex items-center justify-between mb-5">
                                        <h3 class="font-bold text-gray-800" x-text="editando ? 'Editar exposición' : 'Nueva exposición'"></h3>
                                        <button @click="modalAbierto = false" class="text-gray-400 hover:text-gray-700 text-xl">✕</button>
                                    </div>

                                    <form method="POST" action="{{ route('cliente.museo.exposicion.guardar', $punto) }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="item_id" :value="editando ? editando.id : ''">

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-600 mb-1">Título *</label>
                                                <input type="text" name="titulo" required
                                                       :value="editando ? editando.titulo : ''"
                                                       class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-purple-400">
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-600 mb-1">Descripción</label>
                                                <textarea name="descripcion" rows="3"
                                                          class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-purple-400 resize-none"
                                                          x-text="editando ? editando.descripcion : ''"></textarea>
                                            </div>

                                            <div x-data="{ tipo: 'temporal' }" x-init="if(editando) tipo = editando.tipo">
                                                <label class="block text-xs font-bold text-gray-600 mb-1">Tipo *</label>
                                                <select name="tipo" x-model="tipo"
                                                        class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-purple-400">
                                                    <option value="permanente">Permanente</option>
                                                    <option value="temporal">Temporal</option>
                                                </select>

                                                <div x-show="tipo === 'temporal'" class="grid grid-cols-2 gap-3 mt-3">
                                                    <div>
                                                        <label class="block text-xs text-gray-500 mb-1">Inicio</label>
                                                        <input type="date" name="fecha_inicio"
                                                               :value="editando ? editando.fecha_inicio : ''"
                                                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-purple-400">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500 mb-1">Fin</label>
                                                        <input type="date" name="fecha_fin"
                                                               :value="editando ? editando.fecha_fin : ''"
                                                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-purple-400">
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-bold text-gray-600 mb-1">Imagen (opcional)</label>
                                                <input type="file" name="imagen" accept="image/*"
                                                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                                                <template x-if="editando && editando.imagen">
                                                    <p class="text-xs text-gray-400 mt-1">Ya tiene imagen. Sube una nueva para reemplazarla.</p>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="flex justify-end gap-3 mt-6">
                                            <button type="button" @click="modalAbierto = false"
                                                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Cancelar</button>
                                            <button type="submit"
                                                    class="btn-save-track px-5 py-2 bg-purple-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                                                Guardar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                    @endif

                    {{-- ===== EVENTOS: agenda cultural ===== --}}
                    @if(in_array('agenda', $modulos))
                    <div id="eventos" class="scroll-mt-20"
                         x-data="{
                            abierto: {{ $errors->any() ? 'true' : 'false' }},
                            editando: null,
                            imagenActual: null,
                            imagenPreview: null,
                            imagenError: null,
                            enviando: false,
                            resetForm() {
                                this.editando = null;
                                this.imagenActual = null;
                                this.imagenError = null;
                                this.enviando = false;
                                if (this.imagenPreview) URL.revokeObjectURL(this.imagenPreview);
                                this.imagenPreview = null;
                                this.$refs.form.reset();
                            },
                            previsualizar(input) {
                                this.imagenError = null;
                                if (this.imagenPreview) URL.revokeObjectURL(this.imagenPreview);
                                this.imagenPreview = null;
                                const file = input.files[0];
                                if (!file) return;

                                const MAX_MB = 20;
                                const TIPOS_OK = ['image/jpeg', 'image/png', 'image/webp'];

                                if (file.size > MAX_MB * 1024 * 1024) {
                                    this.imagenError = `La imagen pesa ${(file.size / (1024 * 1024)).toFixed(1)} MB. El máximo permitido es ${MAX_MB} MB.`;
                                    input.value = '';
                                    return;
                                }
                                if (!TIPOS_OK.includes(file.type)) {
                                    this.imagenError = 'Formato no permitido. Sube una imagen JPG, PNG o WEBP.';
                                    input.value = '';
                                    return;
                                }
                                try {
                                    this.imagenPreview = URL.createObjectURL(file);
                                } catch (e) {
                                    this.imagenError = 'No se pudo cargar la imagen. Intenta con otro archivo.';
                                    input.value = '';
                                }
                            },
                            editar(evento) {
                                this.editando = evento;
                                this.imagenActual = evento.imagen_url;
                                this.imagenError = null;
                                if (this.imagenPreview) URL.revokeObjectURL(this.imagenPreview);
                                this.imagenPreview = null;
                                this.abierto = true;
                                this.$nextTick(() => {
                                    const f = this.$refs.form;
                                    f.titulo.value = evento.titulo;
                                    f.tipo.value = evento.tipo;
                                    f.fecha.value = evento.fecha;
                                    f.hora.value = evento.hora || '';
                                    f.hora_fin.value = evento.hora_fin || '';
                                    f.precio.value = evento.precio || '';
                                    f.precio_texto.value = evento.precio_texto || '';
                                    f.descripcion.value = evento.descripcion || '';
                                    f.url_entradas.value = evento.url_entradas || '';
                                    f.destacado.checked = evento.destacado;
                                });
                            }
                        }" class="space-y-6">

                        {{-- ===== FORMULARIO NUEVO / EDITAR EVENTO ===== --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="font-bold text-gray-700" x-text="editando ? '✏️ Editar evento' : '📅 Programar evento'"></h4>
                                    <p class="text-xs text-gray-400 mt-0.5">Agrega obras de teatro, proyecciones, conciertos, talleres y más.</p>
                                </div>
                                <button @click="abierto = !abierto; if (!abierto) resetForm()"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition"
                                        x-text="abierto ? 'Cerrar' : '+ Nuevo evento'"></button>
                            </div>

                            <div x-show="abierto" x-transition class="border-t border-gray-100 pt-5 mt-2">
                                <form method="POST" action="{{ route('cliente.eventos.guardar', $punto) }}" enctype="multipart/form-data" x-ref="form"
                                      @submit="enviando = true">
                                    @csrf
                                    <input type="hidden" name="item_id" :value="editando ? editando.id : ''">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Título del evento *</label>
                                            <input type="text" name="titulo" required
                                                   value="{{ old('titulo') }}"
                                                   placeholder="Ej: La Tempestad — Compañía Nacional de Teatro"
                                                   class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de evento *</label>
                                            <select name="tipo" class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                                                @foreach($tiposEvento as $slug => $info)
                                                    <option value="{{ $slug }}" @selected(old('tipo') === $slug)>
                                                        {{ $info['emoji'] }} {{ $info['label'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Fecha *</label>
                                            <input type="date" name="fecha" required
                                                   value="{{ old('fecha') }}"
                                                   class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Hora de inicio</label>
                                            <input type="time" name="hora"
                                                   value="{{ old('hora') }}"
                                                   class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Hora de término</label>
                                            <input type="time" name="hora_fin"
                                                   value="{{ old('hora_fin') }}"
                                                   class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Precio (0 = gratuito)</label>
                                            <input type="number" name="precio" min="0" step="100"
                                                   value="{{ old('precio') }}"
                                                   placeholder="Ej: 5000"
                                                   class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Texto de precio (reemplaza al número)</label>
                                            <input type="text" name="precio_texto"
                                                   value="{{ old('precio_texto') }}"
                                                   placeholder="Ej: Desde $3.000 · Entrada liberada"
                                                   class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Descripción</label>
                                            <textarea name="descripcion" rows="3"
                                                      placeholder="Sinopsis, artistas, duración, clasificación..."
                                                      class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400 resize-none">{{ old('descripcion') }}</textarea>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Link de compra de entradas</label>
                                            <input type="url" name="url_entradas"
                                                   value="{{ old('url_entradas') }}"
                                                   placeholder="https://..."
                                                   class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-gray-600 mb-1">Imagen del evento (opcional)</label>
                                            <input type="file" name="imagen" accept="image/*"
                                                   @change="previsualizar($event.target)"
                                                   class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                            <p class="text-[11px] text-gray-400 mt-1">JPG, PNG o WEBP — máx. 20 MB</p>
                                            <p x-show="imagenError" x-text="imagenError" class="text-xs text-red-500 font-semibold mt-1"></p>

                                            <div class="flex items-center gap-4 mt-2" x-show="imagenActual || imagenPreview">
                                                <div x-show="imagenActual" class="text-center">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Actual</p>
                                                    <img :src="imagenActual" class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                                                </div>
                                                <div x-show="imagenPreview" class="text-center">
                                                    <p class="text-[10px] font-bold text-blue-500 uppercase mb-1">Nueva</p>
                                                    <img :src="imagenPreview" class="w-16 h-16 rounded-xl object-cover border border-blue-200">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="md:col-span-2 flex items-center gap-3">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" name="destacado" value="1"
                                                       @checked(old('destacado'))
                                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-400">
                                                <span class="text-sm text-gray-700 font-medium">Destacar este evento</span>
                                            </label>
                                            <span class="text-xs text-gray-400">(aparece primero en la ficha pública)</span>
                                        </div>
                                    </div>

                                    <div class="flex justify-end items-center mt-5 gap-3">
                                        <button type="button" @click="abierto = false; resetForm()"
                                                :disabled="enviando"
                                                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Cancelar</button>
                                        <button type="submit"
                                                :disabled="enviando"
                                                class="btn-save-track px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                            <svg x-show="enviando" x-cloak class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                            </svg>
                                            <span x-text="enviando ? 'Publicando…' : (editando ? 'Guardar cambios' : 'Publicar en agenda')"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- ===== LISTADO DE EVENTOS ===== --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h4 class="font-bold text-gray-700 mb-4">Eventos programados</h4>

                            @php
                                $proximos  = $eventos->filter(fn($e) => $e->fecha && $e->fecha->greaterThanOrEqualTo(today()))->sortBy('fecha');
                                $pasados   = $eventos->filter(fn($e) => $e->fecha && $e->fecha->lessThan(today()))->sortByDesc('fecha');
                            @endphp

                            @if($eventos->count())
                                {{-- Próximos --}}
                                @if($proximos->count())
                                <div class="space-y-3 mb-6">
                                    <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Próximos</p>
                                    @foreach($proximos as $evento)
                                    @php $tipoInfo = $evento->tipoEvento(); @endphp
                                    <div class="flex items-start gap-4 border border-gray-100 rounded-xl p-4
                                        {{ $evento->destacado ? 'border-blue-200 bg-blue-50/30' : '' }}">
                                        @if($evento->imagen)
                                            <img src="{{ asset('storage/' . $evento->imagen) }}" alt="{{ $evento->datos['titulo'] ?? '' }}"
                                                 class="w-16 h-16 rounded-xl object-cover border border-gray-100 shrink-0">
                                        @else
                                            <div class="w-16 h-16 rounded-xl bg-gray-50 flex items-center justify-center text-2xl shrink-0 border border-gray-100">
                                                {{ $tipoInfo['emoji'] }}
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-semibold text-gray-800 text-sm">{{ $evento->datos['titulo'] ?? '' }}</p>
                                                @if($evento->destacado)
                                                    <span class="text-[10px] font-black uppercase bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Destacado</span>
                                                @endif
                                                <span class="text-[10px] font-black uppercase bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                                    {{ $tipoInfo['emoji'] }} {{ $tipoInfo['label'] }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">
                                                📅 {{ $evento->fecha->translatedFormat('l d \d\e F Y') }}
                                                @if($evento->datos['hora'] ?? null)· 🕐 {{ \Carbon\Carbon::parse($evento->datos['hora'])->format('H:i') }}@endif
                                                @if($evento->datos['hora_fin'] ?? null)– {{ \Carbon\Carbon::parse($evento->datos['hora_fin'])->format('H:i') }}@endif
                                            </p>
                                            <p class="text-xs font-bold text-blue-700 mt-0.5">{{ $evento->precioEvento() }}</p>
                                        </div>
                                        <div class="flex gap-2 shrink-0">
                                            <button type="button"
                                                    @click="editar(@js([
                                                        'id'           => $evento->id,
                                                        'titulo'       => $evento->datos['titulo'] ?? '',
                                                        'tipo'         => $evento->datos['tipo'] ?? '',
                                                        'fecha'        => optional($evento->fecha)->format('Y-m-d'),
                                                        'hora'         => $evento->datos['hora'] ?? '',
                                                        'hora_fin'     => $evento->datos['hora_fin'] ?? '',
                                                        'precio'       => $evento->datos['precio'] ?? '',
                                                        'precio_texto' => $evento->datos['precio_texto'] ?? '',
                                                        'descripcion'  => $evento->datos['descripcion'] ?? '',
                                                        'url_entradas' => $evento->datos['url_entradas'] ?? '',
                                                        'destacado'    => (bool) $evento->destacado,
                                                        'imagen_url'   => $evento->imagen ? asset('storage/' . $evento->imagen) : null,
                                                    ])); window.scrollTo({ top: 0, behavior: 'smooth' })"
                                                    class="text-xs text-blue-600 font-bold hover:underline">Editar</button>
                                            <form method="POST" action="{{ route('cliente.eventos.eliminar', [$punto, $evento]) }}"
                                                  onsubmit="return confirm('¿Eliminar este evento?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 font-bold hover:underline">Eliminar</button>
                                            </form>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                {{-- Pasados --}}
                                @if($pasados->count())
                                <details class="mt-4">
                                    <summary class="text-xs font-black uppercase tracking-widest text-gray-400 cursor-pointer hover:text-gray-600">
                                        Eventos pasados ({{ $pasados->count() }})
                                    </summary>
                                    <div class="space-y-2 mt-3">
                                        @foreach($pasados as $evento)
                                        <div class="flex items-center justify-between border border-gray-100 rounded-xl p-3 opacity-60">
                                            <div>
                                                <p class="text-sm font-medium text-gray-700">{{ $evento->datos['titulo'] ?? '' }}</p>
                                                <p class="text-xs text-gray-400">{{ $evento->fecha->translatedFormat('d M Y') }}</p>
                                            </div>
                                            <form method="POST" action="{{ route('cliente.eventos.eliminar', [$punto, $evento]) }}"
                                                  onsubmit="return confirm('¿Eliminar?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600">Eliminar</button>
                                            </form>
                                        </div>
                                        @endforeach
                                    </div>
                                </details>
                                @endif
                            @else
                                <p class="text-sm text-gray-400 italic text-center py-6">No hay eventos en la agenda. ¡Programa el primero!</p>
                            @endif
                        </div>

                    </div>
                    @endif

                    {{-- ===== CATÁLOGO: productos (tiendas / artesanía) ===== --}}
                    @if(in_array($punto->categoria_id, [13, 14]))
                    <div id="catalogo" class="scroll-mt-20 space-y-4">

                        {{-- Agregar producto --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-gray-800 mb-4">🛍️ Agregar producto</h3>
                            <form action="{{ route('cliente.productos.store') }}" method="POST" enctype="multipart/form-data"
                                  class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @csrf
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nombre *</label>
                                    <input type="text" name="nombre" required placeholder="Ej: Polera estampada"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Precio</label>
                                    <input type="text" name="precio" placeholder="Ej: $12.000 / Desde $5.000"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Imagen</label>
                                    <input type="file" name="imagen" accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-[#fff0ef] file:text-[#fc5648] hover:file:bg-[#ffe0dd] cursor-pointer">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Descripción corta</label>
                                    <input type="text" name="descripcion" placeholder="Descripción opcional del producto"
                                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                                </div>
                                <div class="sm:col-span-2">
                                    <button type="submit"
                                            class="btn-save-track bg-[#fc5648] text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                                        + Agregar producto
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Lista de productos --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-800">Mis productos <span class="text-gray-400 font-normal">({{ $productos->count() }})</span></h3>
                            </div>

                            @if($productos->isEmpty())
                            <div class="px-6 py-12 text-center text-gray-400">
                                <div class="text-4xl mb-3">🛍️</div>
                                <p class="font-semibold">Aún no tienes productos cargados.</p>
                                <p class="text-sm mt-1">Agrégalos arriba y aparecerán en tu catálogo público.</p>
                            </div>
                            @else
                            <div class="divide-y divide-gray-50">
                                @foreach($productos as $producto)
                                <div class="flex items-center gap-4 px-6 py-4">
                                    {{-- Imagen --}}
                                    <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                                        @if($producto->imagen)
                                            <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-2xl text-gray-300">📦</div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-800 truncate">{{ $producto->nombre }}</p>
                                        @if($producto->precio)
                                        <p class="text-sm text-[#fc5648] font-bold">{{ $producto->precio }}</p>
                                        @endif
                                        @if($producto->descripcion)
                                        <p class="text-xs text-gray-400 truncate">{{ $producto->descripcion }}</p>
                                        @endif
                                    </div>

                                    {{-- Acciones --}}
                                    <div class="flex items-center gap-3 shrink-0">
                                        <button onclick="toggleEditarProducto({{ $producto->id }})"
                                                class="text-xs font-bold text-blue-600 hover:underline">Editar</button>
                                        <form action="{{ route('cliente.productos.destroy', $producto) }}" method="POST"
                                              onsubmit="return confirm('¿Eliminar «{{ addslashes($producto->nombre) }}»?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-red-400 hover:underline">Eliminar</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Fila edición inline --}}
                                <div id="editar-producto-{{ $producto->id }}" class="hidden bg-gray-50 px-6 py-4 border-t border-dashed border-gray-200">
                                    <form action="{{ route('cliente.productos.update', $producto) }}" method="POST"
                                          enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @csrf
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Nombre</label>
                                            <input type="text" name="nombre" value="{{ $producto->nombre }}" required
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Precio</label>
                                            <input type="text" name="precio" value="{{ $producto->precio }}"
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Descripción</label>
                                            <input type="text" name="descripcion" value="{{ $producto->descripcion }}"
                                                   class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Nueva imagen</label>
                                            <input type="file" name="imagen" accept="image/*"
                                                   class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#fff0ef] file:text-[#fc5648]">
                                        </div>
                                        <div class="flex items-end gap-2">
                                            <button type="submit"
                                                    class="bg-gray-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-black transition">
                                                Guardar
                                            </button>
                                            <button type="button" onclick="toggleEditarProducto({{ $producto->id }})"
                                                    class="px-4 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-500 hover:bg-gray-100 transition">
                                                Cancelar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

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
        editorEl._quill    = q;
        editorEl._textarea = textarea;
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
    initEditor('#carta-editor', '#carta-texto', null, true);
    @endif
    @if($tieneModuloAlojamiento)
    initEditor('#habitaciones-editor', '#habitaciones', null, true);
    initEditor('#politicas-editor',    '#politicas',    null, true);
    @endif

    // ── Sync Quill textareas on any form submit ──────────────────────
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            this.querySelectorAll('[id$="-editor"]').forEach(container => {
                if (container._quill && container._textarea) {
                    container._textarea.value = container._quill.root.innerHTML;
                }
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

    // ── Guardar: spinner + volver a la sección ──────────────────────
    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', function () {
            const section = this.closest('.scroll-mt-20');
            if (section?.id) sessionStorage.setItem('_pindoor_section', section.id);
            this.innerHTML = '<svg class="animate-spin inline w-4 h-4 mr-1.5 -mt-0.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>Guardando…';
        });
    });

    // ── Igual que arriba, pero para los botones de Museo/Eventos/Catálogo
    //    que ya tienen su propio color/estilo (no reusan .btn-save) ──
    document.querySelectorAll('.btn-save-track').forEach(btn => {
        btn.addEventListener('click', function () {
            const section = this.closest('.scroll-mt-20');
            if (section?.id) sessionStorage.setItem('_pindoor_section', section.id);
        });
    });

    @if(session('success'))
    const _sec = sessionStorage.getItem('_pindoor_section');
    if (_sec) {
        sessionStorage.removeItem('_pindoor_section');
        requestAnimationFrame(() => {
            document.getElementById(_sec)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }
    @endif
});
</script>

<script>
function toggleEditarProducto(id) {
    document.getElementById('editar-producto-' + id).classList.toggle('hidden');
}
</script>

{{-- Toast de éxito fijo --}}
@if(session('success'))
<div id="toast-ok" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:9999;display:flex;align-items:flex-start;gap:12px;background:#fff;border:1px solid #bbf7d0;box-shadow:0 20px 60px rgba(0,0,0,.18);border-radius:20px;padding:20px 22px;max-width:360px;width:90%">
    <div style="width:32px;height:32px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg style="width:16px;height:16px;color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <div style="flex:1;min-width:0">
        <p style="margin:0;font-size:14px;font-weight:700;color:#111827">¡Guardado!</p>
        <p style="margin:4px 0 0;font-size:12px;color:#6b7280;line-height:1.4">{{ session('success') }}</p>
    </div>
    <button onclick="document.getElementById('toast-ok').remove()" style="background:none;border:none;cursor:pointer;color:#d1d5db;padding:0;flex-shrink:0">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>
<script>setTimeout(() => document.getElementById('toast-ok')?.remove(), 5000);</script>
@endif
</x-app-layout>
