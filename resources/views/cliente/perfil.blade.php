<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mi Negocio &mdash; {{ $punto->title }}
            </h2>
            <a href="{{ route('cliente.perfil.editar') }}"
               class="px-4 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                Editar Perfil
            </a>
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

                <form method="POST" action="{{ route('cliente.oferta.actualizar') }}">
                    @csrf @method('PATCH')

                    {{-- Checkbox real para el toggle --}}
                    <input type="hidden" name="oferta_activa" value="0">
                    <input type="checkbox" name="oferta_activa" value="1" x-bind:checked="activa" class="hidden">

                    <textarea
                        name="oferta_del_dia"
                        rows="4"
                        maxlength="1000"
                        placeholder="Ej: Happy hour 18–20 h · Capuchino + croissant $3.500..."
                        class="w-full border-gray-300 rounded-xl shadow-sm text-sm focus:ring-green-400 resize-none"
                    >{{ old('oferta_del_dia', $punto->oferta_del_dia) }}</textarea>

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

            {{-- Menú del día: actualización rápida --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-bold text-gray-700 mb-1">Menú del día</h4>
                <p class="text-xs text-gray-400 mb-4">
                    Escribe el menú de hoy para que los turistas lo vean en tu ficha. Deja vacío para ocultarlo.
                </p>

                <form method="POST" action="{{ route('cliente.menu.actualizar') }}">
                    @csrf @method('PATCH')
                    <textarea
                        name="menu_del_dia"
                        rows="5"
                        maxlength="2000"
                        placeholder="Ej:&#10;PRIMER PLATO — Sopa del día&#10;SEGUNDO PLATO — Cazuela de vacuno&#10;POSTRE — Arroz con leche&#10;Todo por $5.500"
                        class="w-full border-gray-300 rounded-xl shadow-sm text-sm focus:ring-orange-400 resize-none"
                    >{{ old('menu_del_dia', $punto->menu_del_dia) }}</textarea>

                    <div class="flex justify-between items-center mt-3">
                        @if($punto->menu_del_dia)
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

            {{-- Descripción del negocio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-gray-700">Descripción del negocio</h4>
                    <a href="{{ route('cliente.perfil.editar') }}"
                       class="text-xs text-pindoor-accent hover:underline">Editar</a>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $punto->description ?: 'Sin descripción aún.' }}
                </p>

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

            {{-- Estado del perfil de búsqueda --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-amber-800">Perfil de búsqueda</h4>
                    <a href="{{ route('cliente.perfil.editar') }}#busqueda"
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
                        <a href="{{ route('cliente.perfil.editar') }}#busqueda" class="font-bold hover:underline">
                            Hazlo ahora
                        </a>
                    </p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
