<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('cliente.perfil.ver', $punto) }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Mi negocio</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión del Museo &mdash; {{ $punto->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ===== TARIFAS DE ENTRADA ===== --}}
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
                                class="px-5 py-2 bg-amber-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                            Guardar tarifas
                        </button>
                    </div>
                </form>
            </div>
            @else
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-sm text-amber-700">
                    El módulo <strong>Entradas y tarifas</strong> no está habilitado. Contacta al administrador para activarlo.
                </div>
            @endif

            {{-- ===== EXPOSICIONES ===== --}}
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
                                        class="px-5 py-2 bg-purple-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @else
                <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5 text-sm text-purple-700">
                    El módulo <strong>Exposiciones</strong> no está habilitado. Contacta al administrador para activarlo.
                </div>
            @endif

            {{-- Oferta del día (si está habilitada) --}}
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

        </div>
    </div>
</x-app-layout>
