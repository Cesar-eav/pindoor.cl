<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mi Negocio &mdash; {{ $punto->title }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('cliente.perfil') }}" class="text-sm text-gray-400 hover:text-gray-700">&larr; Mis negocios</a>
                <a href="{{ route('cliente.perfil.editar', $punto) }}"
                   class="px-4 py-2 bg-[#fc5648] text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                    Editar Perfil
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes flash --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Cabecera compacta --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex gap-4 items-center mb-3">
                @if($punto->imagen_perfil)
                    <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                         alt="Logo {{ $punto->title }}"
                         class="w-14 h-14 rounded-2xl object-cover border border-gray-100 shrink-0">
                @else
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center shrink-0 text-2xl">🏪</div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-lg font-bold text-gray-900">{{ $punto->title }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold
                            {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $punto->activo ? 'Visible' : 'Pausado' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                        @if($punto->sector) &middot; 📍 {{ $punto->sector }} @endif
                    </p>
                </div>
                <a href="{{ route('puntos.show', $punto->slug) }}" target="_blank"
                   class="shrink-0 text-xs text-gray-400 hover:text-[#fc5648] transition">
                    Ver ficha →
                </a>
            </div>

            {{-- Quick-nav (sticky) --}}
            <div class="sticky top-0 z-20 -mx-4 sm:mx-0 bg-white/90 backdrop-blur border-b border-gray-200 mb-6">
                <div class="overflow-x-auto px-4 sm:px-0">
                    <div class="flex gap-1.5 py-2 min-w-max">
                        @if(in_array('oferta_del_dia', $modulos))
                        <a href="#oferta" class="nav-pill">🏷️ Oferta del día</a>
                        @endif
                        @if(in_array('menu_del_dia', $modulos))
                        <a href="#menu" class="nav-pill">🥘 Menú del día</a>
                        @endif
                        @if(in_array('avisos', $modulos))
                        <a href="#avisos" class="nav-pill">📢 Avisos</a>
                        @endif
                        @if(in_array('promociones', $modulos))
                        <a href="#promociones" class="nav-pill">🎁 Promociones</a>
                        @endif
                        <a href="#galeria" class="nav-pill">🖼️ Galería</a>
                        @if(in_array('carta', $modulos))
                        <a href="{{ route('cliente.perfil.editar', $punto) }}#seccion-carta" class="nav-pill">🍽️ Carta</a>
                        @endif
                        @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos))
                        <a href="{{ route('cliente.museo', $punto) }}" class="nav-pill">🎟️ Museo</a>
                        @endif
                        @if(in_array('agenda', $modulos))
                        <a href="{{ route('cliente.eventos', $punto) }}" class="nav-pill">📅 Eventos</a>
                        @endif
                        <a href="#descripcion" class="nav-pill">📝 Descripción</a>
                    </div>
                </div>
            </div>

            {{-- Layout de dos columnas --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- COLUMNA IZQUIERDA: actualizaciones frecuentes --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Oferta del día --}}
                    @if(in_array('oferta_del_dia', $modulos))
                    <div id="oferta" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-20"
                         x-data="{ activa: {{ $punto->oferta_activa ? 'true' : 'false' }} }">

                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-800">🏷️ Oferta del día</h4>
                                <p class="text-xs text-gray-400 mt-0.5">Aparece como botón en tu ficha mientras esté activa.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer mt-0.5">
                                <input type="checkbox" x-model="activa" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-green-400 rounded-full peer
                                            peer-checked:after:translate-x-full peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                            after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                        </div>

                        @if($punto->tieneOfertaActiva())
                            <div class="text-xs font-bold text-green-700 bg-green-50 rounded-lg px-3 py-2 mb-4 flex items-center gap-2">
                                🟢 Activa
                                @if($punto->oferta_expira_at)
                                    · Vence el {{ $punto->oferta_expira_at->translatedFormat('d \d\e F') }} ({{ $punto->oferta_expira_at->diffForHumans() }})
                                @else
                                    · Sin fecha de vencimiento
                                @endif
                            </div>
                        @elseif($punto->oferta_activa === false && $punto->oferta_del_dia)
                            <div class="text-xs text-gray-400 bg-gray-50 rounded-lg px-3 py-2 mb-4">⚫ Desactivada manualmente</div>
                        @elseif($punto->oferta_expira_at && $punto->oferta_expira_at->isPast())
                            <div class="text-xs text-red-500 bg-red-50 rounded-lg px-3 py-2 mb-4">🔴 Expirada el {{ $punto->oferta_expira_at->translatedFormat('d \d\e F') }}</div>
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
                                <button type="submit"
                                        class="px-5 py-2 text-white text-sm font-bold rounded-lg hover:opacity-90 transition"
                                        :class="activa ? 'bg-green-600' : 'bg-gray-400'">
                                    <span x-text="activa ? 'Activar oferta' : 'Guardar (desactivada)'"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Menú del día --}}
                    @if(in_array('menu_del_dia', $modulos))
                    @php $textoMenu = $punto->dato('menu_del_dia')['texto'] ?? ''; @endphp
                    <div id="menu" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-20">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-gray-800">🥘 Menú del día</h4>
                            @if($textoMenu)
                                <span class="text-xs text-green-600 font-semibold bg-green-50 px-2 py-0.5 rounded-full">Activo</span>
                            @else
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Sin publicar</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mb-4">Escribe el menú de hoy. Deja vacío para ocultarlo.</p>
                        <form method="POST" action="{{ route('cliente.menu.actualizar', $punto) }}">
                            @csrf @method('PATCH')
                            <div id="menu-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-36"></div>
                            <textarea id="menu_del_dia" name="menu_del_dia" class="hidden">{{ old('menu_del_dia', $textoMenu) }}</textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="px-5 py-2 bg-orange-500 text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                                    Publicar
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Avisos --}}
                    @if(in_array('avisos', $modulos))
                    @php $textoAviso = $punto->dato('avisos')['texto'] ?? ''; @endphp
                    <div id="avisos" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-20">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-gray-800">📢 Avisos</h4>
                            @if($textoAviso)
                                <span class="text-xs text-green-600 font-semibold bg-green-50 px-2 py-0.5 rounded-full">Activo</span>
                            @else
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Sin publicar</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mb-4">Comunicados importantes. Deja vacío para ocultarlo.</p>
                        <form method="POST" action="{{ route('cliente.aviso.actualizar', $punto) }}">
                            @csrf @method('PATCH')
                            <div id="aviso-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-28"></div>
                            <textarea id="aviso" name="aviso" class="hidden">{{ old('aviso', $textoAviso) }}</textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="px-5 py-2 bg-gray-700 text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                                    Publicar
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Promociones --}}
                    @if(in_array('promociones', $modulos))
                    @php $textoPromocion = $punto->dato('promociones')['texto'] ?? ''; @endphp
                    <div id="promociones" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-20">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-gray-800">🎁 Promociones</h4>
                            @if($textoPromocion)
                                <span class="text-xs text-green-600 font-semibold bg-green-50 px-2 py-0.5 rounded-full">Activa</span>
                            @else
                                <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Sin publicar</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mb-4">Descuentos y promociones. Deja vacío para ocultarlo.</p>
                        <form method="POST" action="{{ route('cliente.promocion.actualizar', $punto) }}">
                            @csrf @method('PATCH')
                            <div id="promocion-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-28"></div>
                            <textarea id="promocion" name="promocion" class="hidden">{{ old('promocion', $textoPromocion) }}</textarea>
                            <div class="flex justify-end mt-3">
                                <button type="submit" class="px-5 py-2 bg-purple-600 text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                                    Publicar
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                    {{-- Descripción --}}
                    <div id="descripcion" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 scroll-mt-20">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-bold text-gray-800">📝 Descripción del negocio</h4>
                            <a href="{{ route('cliente.perfil.editar', $punto) }}" class="text-xs text-[#fc5648] hover:underline">Editar →</a>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {!! $punto->description ?: '<span class="italic text-gray-300">Sin descripción aún.</span>' !!}
                        </p>
                        @if($punto->tags && count($punto->tags))
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach($punto->tags as $tag)
                                    <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>

                {{-- COLUMNA DERECHA: galería + módulos de contenido + búsqueda --}}
                <div class="space-y-5">

                    {{-- Galería de fotos --}}
                    @php
                        $imagenes      = $punto->imagenes->sortBy('orden')->values();
                        $totalImagenes = $imagenes->count();
                        $disponibles   = 10 - $totalImagenes;
                    @endphp
                    <div id="galeria" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 scroll-mt-20">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-bold text-gray-800">🖼️ Galería</h4>
                            <span class="text-xs font-bold tabular-nums {{ $totalImagenes >= 10 ? 'text-amber-600' : 'text-gray-400' }}">
                                {{ $totalImagenes }}/10
                            </span>
                        </div>

                        {{-- Miniaturas --}}
                        @if($imagenes->isNotEmpty())
                        <div class="grid grid-cols-3 gap-1.5 mb-3">
                            @foreach($imagenes as $imagen)
                            <div class="relative group aspect-square">
                                <img src="{{ asset('storage/' . $imagen->ruta) }}" alt="Foto"
                                     class="w-full h-full object-cover rounded-lg border border-gray-200
                                            {{ $imagen->es_principal ? 'ring-2 ring-[#fc5648]' : '' }}">
                                @if($imagen->es_principal)
                                    <span class="absolute bottom-0.5 left-0.5 text-[9px] bg-[#fc5648] text-white px-1 py-0.5 rounded font-bold leading-none">★</span>
                                @endif
                                <form method="POST"
                                      action="{{ route('cliente.imagenes.eliminar', [$punto, $imagen]) }}"
                                      class="absolute top-0.5 right-0.5 opacity-0 group-hover:opacity-100 transition"
                                      onsubmit="return confirm('¿Eliminar esta foto?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-5 h-5 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center hover:bg-red-600 leading-none">
                                        ✕
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        {{-- Upload --}}
                        @if($disponibles > 0)
                        <form method="POST" action="{{ route('cliente.imagenes.subir', $punto) }}"
                              enctype="multipart/form-data" id="gallery-form">
                            @csrf
                            <input type="file" name="imagenes[]" id="file-input"
                                   accept="image/jpeg,image/png,image/webp" multiple required class="hidden">

                            <div id="drop-zone"
                                 class="border-2 border-dashed border-gray-200 rounded-xl p-4 text-center cursor-pointer
                                        hover:border-[#fc5648] hover:bg-red-50/40 transition mb-2">
                                <div id="upload-placeholder">
                                    <p class="text-2xl mb-1">📷</p>
                                    <p class="text-xs font-semibold text-gray-600">Haz clic o arrastra fotos aquí</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">JPG, PNG o WebP · máx. 2 MB c/u</p>
                                    <p class="text-[11px] text-[#fc5648] font-medium mt-0.5">Hasta {{ $disponibles }} {{ $disponibles === 1 ? 'foto' : 'fotos' }} más</p>
                                </div>
                                <div id="upload-selection" class="hidden">
                                    <p class="text-2xl mb-1">✅</p>
                                    <p id="file-count" class="text-xs font-semibold text-gray-700"></p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">Pulsa Subir para confirmar</p>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="button" id="open-btn"
                                        class="flex-1 py-2 bg-gray-800 text-white text-sm font-bold rounded-xl hover:bg-gray-700 transition">
                                    Seleccionar fotos
                                </button>
                                <button type="submit" id="upload-btn"
                                        class="hidden flex-1 py-2 bg-[#fc5648] text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                                    Subir
                                </button>
                            </div>
                        </form>
                        @else
                        <p class="text-xs text-amber-600 bg-amber-50 rounded-xl px-4 py-3 text-center">
                            Límite de 10 fotos alcanzado.<br>Elimina una para subir otra.
                        </p>
                        @endif
                    </div>

                    {{-- Módulos de contenido (carta, museo, agenda) --}}
                    @if(in_array('carta', $modulos) || in_array('entradas', $modulos) || in_array('exposiciones', $modulos) || in_array('agenda', $modulos))
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h4 class="font-bold text-gray-800 mb-3">Contenido del negocio</h4>
                        <div class="space-y-1.5">
                            @if(in_array('carta', $modulos))
                            @php $tieneCarta = $punto->tieneCarta(); @endphp
                            <a href="{{ route('cliente.perfil.editar', $punto) }}#seccion-carta"
                               class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-orange-300 hover:bg-orange-50 transition group">
                                <span class="text-xl">🍽️</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">Carta / Menú</p>
                                    <p class="text-xs {{ $tieneCarta ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ $tieneCarta ? 'Publicada' : 'Sin publicar' }}
                                    </p>
                                </div>
                                <span class="text-gray-300 group-hover:text-orange-400 text-lg leading-none">›</span>
                            </a>
                            @endif
                            @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos))
                            <a href="{{ route('cliente.museo', $punto) }}"
                               class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-amber-300 hover:bg-amber-50 transition group">
                                <span class="text-xl">🎟️</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">Entradas y exposiciones</p>
                                    <p class="text-xs text-gray-400">Tarifas y colecciones</p>
                                </div>
                                <span class="text-gray-300 group-hover:text-amber-400 text-lg leading-none">›</span>
                            </a>
                            @endif
                            @if(in_array('agenda', $modulos))
                            <a href="{{ route('cliente.eventos', $punto) }}"
                               class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-blue-300 hover:bg-blue-50 transition group">
                                <span class="text-xl">📅</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">Agenda de eventos</p>
                                    <p class="text-xs text-gray-400">Obras, conciertos y más</p>
                                </div>
                                <span class="text-gray-300 group-hover:text-blue-400 text-lg leading-none">›</span>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Perfil de búsqueda --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-amber-800 text-sm">Perfil de búsqueda</h4>
                            <a href="{{ route('cliente.perfil.editar', $punto) }}#busqueda"
                               class="text-xs text-amber-700 hover:underline font-medium">Completar →</a>
                        </div>
                        <p class="text-xs text-amber-700 mb-2">
                            Invisible para turistas, pero el buscador lo usa para encontrarte.
                        </p>
                        @if($punto->descripcion_busqueda)
                            <p class="text-xs text-amber-900 leading-relaxed line-clamp-3">{{ $punto->descripcion_busqueda }}</p>
                        @else
                            <p class="text-xs text-amber-600 italic">Sin completar — te encontrarán menos.</p>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>

{{-- Quill --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<style>
    .nav-pill {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.5rem;
        background: white;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        white-space: nowrap;
        transition: border-color 0.15s, color 0.15s;
        text-decoration: none;
    }
    .nav-pill:hover {
        border-color: #fc5648;
        color: #fc5648;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Quill editors ──────────────────────────────────────────────────────
    const toolbar = [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['clean']
    ];

    function initEditor(editorId, textareaId, formEl) {
        const editorEl = document.querySelector(editorId);
        const textarea = document.querySelector(textareaId);
        if (!editorEl || !textarea || !formEl) return;
        const quill = new Quill(editorEl, { theme: 'snow', modules: { toolbar } });
        if (textarea.value.trim()) quill.clipboard.dangerouslyPasteHTML(textarea.value);
        const sync = () => { textarea.value = quill.root.innerHTML; };
        quill.on('text-change', sync);
        formEl.addEventListener('submit', sync);
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

    // ── Galería: file picker UX ────────────────────────────────────────────
    const fileInput     = document.getElementById('file-input');
    const dropZone      = document.getElementById('drop-zone');
    const placeholder   = document.getElementById('upload-placeholder');
    const selectionInfo = document.getElementById('upload-selection');
    const fileCountEl   = document.getElementById('file-count');
    const openBtn       = document.getElementById('open-btn');
    const uploadBtn     = document.getElementById('upload-btn');

    if (fileInput && dropZone) {
        dropZone.addEventListener('click', () => fileInput.click());
        openBtn.addEventListener('click', () => fileInput.click());

        // Drag-and-drop
        dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('border-[#fc5648]', 'bg-red-50/40'); });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('border-[#fc5648]', 'bg-red-50/40'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('border-[#fc5648]', 'bg-red-50/40');
            const dt = new DataTransfer();
            Array.from(e.dataTransfer.files).forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
            fileInput.dispatchEvent(new Event('change'));
        });

        fileInput.addEventListener('change', function () {
            const n = this.files.length;
            if (n === 0) return;
            fileCountEl.textContent = n === 1 ? '1 foto seleccionada' : `${n} fotos seleccionadas`;
            placeholder.classList.add('hidden');
            selectionInfo.classList.remove('hidden');
            openBtn.classList.add('hidden');
            uploadBtn.classList.remove('hidden');
        });
    }
});
</script>

</x-app-layout>
