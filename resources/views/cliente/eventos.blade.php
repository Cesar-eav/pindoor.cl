<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('cliente.perfil') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Mi negocio</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Agenda Cultural &mdash; {{ $punto->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if(!$punto->moduloActivo('agenda'))
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-sm text-blue-700">
                    El módulo <strong>Agenda cultural</strong> no está habilitado. Contacta al administrador para activarlo.
                </div>
            @else

            {{-- ===== FORMULARIO NUEVO / EDITAR EVENTO ===== --}}
            <div x-data="{
                abierto: {{ $errors->any() ? 'true' : 'false' }},
                editando: null,
                resetForm() { this.editando = null; }
            }" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-gray-700">📅 Programar evento</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Agrega obras de teatro, proyecciones, conciertos, talleres y más.</p>
                    </div>
                    <button @click="abierto = !abierto"
                            class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition"
                            x-text="abierto ? 'Cerrar' : '+ Nuevo evento'"></button>
                </div>

                <div x-show="abierto" x-transition class="border-t border-gray-100 pt-5 mt-2">
                    <form method="POST" action="{{ route('cliente.eventos.guardar') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="evento_id" :value="editando ? editando.id : ''">

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
                                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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

                        <div class="flex justify-end mt-5 gap-3">
                            <button type="button" @click="abierto = false"
                                    class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700">Cancelar</button>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                                Publicar en agenda
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== LISTADO DE EVENTOS ===== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-bold text-gray-700 mb-4">Eventos programados</h4>

                @php
                    $proximos  = $eventos->where('fecha', '>=', today()->toDateString())->sortBy('fecha');
                    $pasados   = $eventos->where('fecha', '<', today()->toDateString())->sortByDesc('fecha');
                @endphp

                @if($eventos->count())
                    {{-- Próximos --}}
                    @php
                        $proximos = $eventos->filter(fn($e) => $e->fecha && $e->fecha->greaterThanOrEqualTo(today()))->sortBy('fecha');
                        $pasados  = $eventos->filter(fn($e) => $e->fecha && $e->fecha->lessThan(today()))->sortByDesc('fecha');
                    @endphp

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
                                <form method="POST" action="{{ route('cliente.eventos.eliminar', $evento) }}"
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
                                <form method="POST" action="{{ route('cliente.eventos.eliminar', $evento) }}"
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

            {{-- Oferta del día (si habilitada) --}}
            @if($punto->moduloActivo('oferta_del_dia'))
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-gray-700">🏷️ Oferta del día</p>
                    <p class="text-xs text-gray-500 mt-0.5">Gestiona la oferta desde tu dashboard principal.</p>
                </div>
                <a href="{{ route('cliente.perfil') }}"
                   class="text-sm text-pindoor-accent font-bold hover:underline">Ir al dashboard →</a>
            </div>
            @endif

            @endif {{-- fin @if moduloActivo('agenda') --}}

        </div>
    </div>
</x-app-layout>
