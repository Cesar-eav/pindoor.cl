<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mi Negocio &mdash; {{ $punto->title }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('cliente.perfil') }}" class="text-sm text-gray-400 hover:text-gray-700">&larr; Mis negocios</a>
                <a href="{{ route('cliente.perfil.editar', $punto) }}"
                   class="px-4 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                    Editar Perfil
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Cabecera del negocio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex gap-5 items-start">
                @if($punto->imagen_perfil)
                    <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                         alt="Logo {{ $punto->title }}"
                         class="w-20 h-20 rounded-2xl object-cover border border-gray-100 shrink-0">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center shrink-0">
                        <span class="text-3xl">🏪</span>
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xl font-bold text-gray-900">{{ $punto->title }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold
                            {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $punto->activo ? 'Visible en el mapa' : 'Pausado' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                        &middot; 📍 {{ $punto->sector }}
                    </p>
                    @if($punto->horario)
                        <p class="text-sm text-gray-400 mt-1">🕐 {{ $punto->horario }}</p>
                    @endif
                    @if($punto->enlace)
                        <a href="{{ $punto->enlace }}" target="_blank"
                           class="text-xs text-pindoor-accent hover:underline mt-1 block truncate">
                            {{ $punto->enlace }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Oferta del día --}}
            @if(in_array('oferta_del_dia', $modulos))
            <div x-data="{ activa: {{ $punto->oferta_activa ? 'true' : 'false' }} }"
                 class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-gray-700">Oferta del día</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Se muestra como botón en tu ficha pública mientras esté activa.</p>
                    </div>

                    {{-- Toggle activa/inactiva --}}
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="activa" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-green-400 rounded-full peer
                                    peer-checked:after:translate-x-full peer-checked:after:border-white
                                    after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                    after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                </div>

                {{-- Estado actual --}}
                @if($punto->tieneOfertaActiva())
                    <div class="text-xs font-bold text-green-700 bg-green-50 rounded-lg px-3 py-2 mb-4 flex items-center gap-2">
                        <span>🟢</span>
                        Activa
                        @if($punto->oferta_expira_at)
                            · Vence el {{ $punto->oferta_expira_at->translatedFormat('d \d\e F') }}
                            ({{ $punto->oferta_expira_at->diffForHumans() }})
                        @else
                            · Sin fecha de vencimiento
                        @endif
                    </div>
                @elseif($punto->oferta_activa === false && $punto->oferta_del_dia)
                    <div class="text-xs text-gray-400 bg-gray-50 rounded-lg px-3 py-2 mb-4">
                        ⚫ Desactivada manualmente
                    </div>
                @elseif($punto->oferta_expira_at && $punto->oferta_expira_at->isPast())
                    <div class="text-xs text-red-500 bg-red-50 rounded-lg px-3 py-2 mb-4">
                        🔴 Expirada el {{ $punto->oferta_expira_at->translatedFormat('d \d\e F') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('cliente.oferta.actualizar', $punto) }}">
                    @csrf @method('PATCH')

                    {{-- Checkbox real para el toggle --}}
                    <input type="hidden" name="oferta_activa" value="0">
                    <input type="checkbox" name="oferta_activa" value="1" x-bind:checked="activa" class="hidden">

                    <div id="oferta-editor"
                         class="bg-white border border-gray-200 rounded-xl text-sm min-h-28"></div>
                    <textarea id="oferta_del_dia" name="oferta_del_dia" class="hidden">{{ old('oferta_del_dia', $punto->oferta_del_dia) }}</textarea>

                    {{-- Duración --}}
                    <div x-show="activa" class="mt-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Vigencia</label>
                        <select name="duracion_dias"
                                class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-green-400">
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

            {{-- Menú del día: actualización rápida --}}
            @if(in_array('menu_del_dia', $modulos))
            @php $textoMenu = $punto->dato('menu_del_dia')['texto'] ?? ''; @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-bold text-gray-700 mb-1">Menú del día</h4>
                <p class="text-xs text-gray-400 mb-4">
                    Escribe el menú de hoy para que los turistas lo vean en tu ficha. Deja vacío para ocultarlo.
                </p>

                <form method="POST" action="{{ route('cliente.menu.actualizar', $punto) }}">
                    @csrf @method('PATCH')
                    <div id="menu-editor"
                         class="bg-white border border-gray-200 rounded-xl text-sm min-h-36"></div>
                    <textarea id="menu_del_dia" name="menu_del_dia" class="hidden">{{ old('menu_del_dia', $textoMenu) }}</textarea>

                    <div class="flex justify-between items-center mt-3">
                        @if($textoMenu)
                            <span class="text-xs text-gray-400">Activo — visible en tu ficha</span>
                        @else
                            <span class="text-xs text-gray-300 italic">Sin menú publicado</span>
                        @endif
                        <button type="submit"
                                class="px-5 py-2 bg-orange-500 text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                            Publicar
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Avisos --}}
            @if(in_array('avisos', $modulos))
            @php $textoAviso = $punto->dato('avisos')['texto'] ?? ''; @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-bold text-gray-700 mb-1">📢 Avisos</h4>
                <p class="text-xs text-gray-400 mb-4">
                    Comunicados importantes para tus visitantes. Deja vacío para ocultarlo.
                </p>
                <form method="POST" action="{{ route('cliente.aviso.actualizar', $punto) }}">
                    @csrf @method('PATCH')
                    <div id="aviso-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-28"></div>
                    <textarea id="aviso" name="aviso" class="hidden">{{ old('aviso', $textoAviso) }}</textarea>
                    <div class="flex justify-between items-center mt-3">
                        @if($textoAviso)
                            <span class="text-xs text-gray-400">Activo — visible en tu ficha</span>
                        @else
                            <span class="text-xs text-gray-300 italic">Sin aviso publicado</span>
                        @endif
                        <button type="submit"
                                class="px-5 py-2 bg-gray-700 text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                            Publicar
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Promociones --}}
            @if(in_array('promociones', $modulos))
            @php $textoPromocion = $punto->dato('promociones')['texto'] ?? ''; @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-bold text-gray-700 mb-1">🎁 Promociones</h4>
                <p class="text-xs text-gray-400 mb-4">
                    Descuentos y promociones especiales. Deja vacío para ocultarlo.
                </p>
                <form method="POST" action="{{ route('cliente.promocion.actualizar', $punto) }}">
                    @csrf @method('PATCH')
                    <div id="promocion-editor" class="bg-white border border-gray-200 rounded-xl text-sm min-h-28"></div>
                    <textarea id="promocion" name="promocion" class="hidden">{{ old('promocion', $textoPromocion) }}</textarea>
                    <div class="flex justify-between items-center mt-3">
                        @if($textoPromocion)
                            <span class="text-xs text-gray-400">Activa — visible en tu ficha</span>
                        @else
                            <span class="text-xs text-gray-300 italic">Sin promoción publicada</span>
                        @endif
                        <button type="submit"
                                class="px-5 py-2 bg-purple-600 text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                            Publicar
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- Descripción del negocio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-gray-700">Descripción del negocio</h4>
                    <a href="{{ route('cliente.perfil.editar', $punto) }}"
                       class="text-xs text-pindoor-accent hover:underline">Editar</a>
                </div>
                <div class="text-sm text-gray-600 leading-relaxed [&_p]:mb-3 [&_p:last-child]:mb-0">
                    {!! $punto->description ?: 'Sin descripción aún.' !!}
                </div>

                @if($punto->tags && count($punto->tags))
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($punto->tags as $tag)
                            <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Accesos rápidos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Carta --}}
                @if(in_array('carta', $modulos))
                @php $tieneCarta = $punto->tieneCarta(); @endphp
                <a href="{{ route('cliente.perfil.editar', $punto) }}#seccion-carta"
                   class="bg-white rounded-2xl border border-orange-200 p-5 flex items-center gap-4 hover:border-orange-400 hover:shadow-sm transition group">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-2xl group-hover:bg-orange-100 transition">🍽️</div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Carta / Menú</p>
                        <p class="text-xs mt-0.5 {{ $tieneCarta ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                            {{ $tieneCarta ? 'Activa — visible en tu ficha' : 'Sin carta publicada' }}
                        </p>
                    </div>
                </a>
                @endif

                {{-- Entradas y exposiciones --}}
                @if(in_array('entradas', $modulos) || in_array('exposiciones', $modulos))
                <a href="{{ route('cliente.museo', $punto) }}"
                   class="bg-white rounded-2xl border border-amber-200 p-5 flex items-center gap-4 hover:border-amber-400 hover:shadow-sm transition group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-2xl group-hover:bg-amber-100 transition">🎟️</div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Entradas y exposiciones</p>
                        <p class="text-xs text-gray-400 mt-0.5">Gestiona tarifas y colecciones</p>
                    </div>
                </a>
                @endif

                {{-- Agenda de eventos --}}
                @if(in_array('agenda', $modulos))
                <a href="{{ route('cliente.eventos', $punto) }}"
                   class="bg-white rounded-2xl border border-blue-200 p-5 flex items-center gap-4 hover:border-blue-400 hover:shadow-sm transition group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-2xl group-hover:bg-blue-100 transition">📅</div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Agenda de eventos</p>
                        <p class="text-xs text-gray-400 mt-0.5">Programa obras, conciertos y más</p>
                    </div>
                </a>
                @endif

                {{-- Ver ficha pública (siempre visible) --}}
                <a href="{{ route('puntos.show', $punto->slug) }}" target="_blank"
                   class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-4 hover:border-gray-300 hover:shadow-sm transition group">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-2xl group-hover:bg-gray-100 transition">🔗</div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Ver ficha pública</p>
                        <p class="text-xs text-gray-400 mt-0.5">Cómo te ven los turistas</p>
                    </div>
                </a>

            </div>

            {{-- Estado del perfil de búsqueda --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-amber-800">Perfil de búsqueda</h4>
                    <a href="{{ route('cliente.perfil.editar', $punto) }}#busqueda"
                       class="text-xs text-amber-700 hover:underline font-medium">Completar</a>
                </div>
                <p class="text-xs text-amber-700 mb-3">
                    Este texto es invisible para los turistas, pero lo usa el buscador para encontrar tu negocio.
                    Cuanto más detallado, mejor te encontrarán.
                </p>
                @if($punto->descripcion_busqueda)
                    <p class="text-sm text-amber-900 leading-relaxed line-clamp-3">
                        {{ $punto->descripcion_busqueda }}
                    </p>
                @else
                    <p class="text-sm text-amber-600 italic">
                        Aún no has completado tu perfil de búsqueda.
                        <a href="{{ route('cliente.perfil.editar', $punto) }}#busqueda" class="font-bold hover:underline">
                            Hazlo ahora
                        </a>
                    </p>
                @endif
            </div>

        </div>
    </div>

{{-- Quill para oferta del día y menú del día --}}
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
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

        if (textarea.value.trim()) {
            quill.clipboard.dangerouslyPasteHTML(textarea.value);
        }

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
});
</script>

</x-app-layout>
