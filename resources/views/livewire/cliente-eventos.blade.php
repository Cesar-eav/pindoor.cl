<div class="space-y-6">

    {{-- Mensaje de éxito --}}
    @if($mensaje)
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition
         class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-5 py-3">
        {{ $mensaje }}
    </div>
    @endif

    {{-- ===== FORMULARIO NUEVO / EDITAR EVENTO ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

        <div class="flex items-center justify-between mb-4">
            <div>
                <h4 class="font-bold text-gray-700">{{ $editandoId ? '✏️ Editar evento' : '📅 Programar evento' }}</h4>
                <p class="text-xs text-gray-400 mt-0.5">Agrega obras de teatro, proyecciones, conciertos, talleres y más.</p>
            </div>
            <button type="button"
                    wire:click="{{ $mostrarForm ? 'cancelar' : 'nuevo' }}"
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                {{ $mostrarForm ? 'Cerrar' : '+ Nuevo evento' }}
            </button>
        </div>

        @if($mostrarForm)
        <div class="border-t border-gray-100 pt-5 mt-2">
            <form wire:submit.prevent="guardar">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Título del evento *</label>
                        <input type="text" wire:model="titulo"
                               placeholder="Ej: La Tempestad — Compañía Nacional de Teatro"
                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                        @error('titulo') <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de evento *</label>
                        <select wire:model="tipo" class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                            <option value="">Selecciona…</option>
                            @foreach($tiposEvento as $slug => $info)
                                <option value="{{ $slug }}">{{ $info['emoji'] }} {{ $info['label'] }}</option>
                            @endforeach
                        </select>
                        @error('tipo') <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Fecha *</label>
                        <input type="date" wire:model="fecha"
                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                        @error('fecha') <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Hora de inicio</label>
                        <input type="time" wire:model="hora"
                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Hora de término</label>
                        <input type="time" wire:model="hora_fin"
                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Precio (0 = gratuito)</label>
                        <input type="number" wire:model="precio" min="0" step="100"
                               placeholder="Ej: 5000"
                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Texto de precio (reemplaza al número)</label>
                        <input type="text" wire:model="precio_texto"
                               placeholder="Ej: Desde $3.000 · Entrada liberada"
                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 mb-1">Descripción</label>
                        <textarea wire:model="descripcion" rows="8"
                                  placeholder="Sinopsis, artistas, duración, clasificación..."
                                  class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400 resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Link de compra de entradas</label>
                        <input type="url" wire:model="url_entradas"
                               placeholder="https://..."
                               class="w-full border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                        @error('url_entradas') <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        <p class="text-[11px] text-gray-400 mt-1">Se ignora si activas la venta de entradas por Pindoor abajo.</p>
                    </div>

                    <div class="md:col-span-2 border border-gray-100 rounded-xl p-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="entradas_flow_activo"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-400">
                            <span class="text-sm text-gray-700 font-medium">Vender entradas por Pindoor (pago online con Flow)</span>
                        </label>
                        @if($entradas_flow_activo)
                        <div class="mt-3">
                            <label class="block text-xs font-bold text-gray-600 mb-1">Cupo máximo de entradas</label>
                            <input type="number" wire:model="cupo_maximo" min="1" step="1"
                                   placeholder="Vacío = sin límite"
                                   class="w-full max-w-xs border-gray-300 rounded-xl text-sm shadow-sm focus:ring-blue-400">
                            @error('cupo_maximo') <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Imagen del evento (opcional)</label>
                        <input type="file" wire:model="imagen" accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[11px] text-gray-400 mt-1">JPG, PNG o WEBP — máx. 20 MB</p>
                        <p wire:loading wire:target="imagen" class="text-xs text-gray-400 mt-1">Subiendo imagen…</p>
                        @error('imagen') <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p> @enderror

                        <div class="flex items-center gap-4 mt-2">
                            @if($imagenActualUrl && !$imagen)
                                <div class="text-center">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Actual</p>
                                    <img src="{{ $imagenActualUrl }}" class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                                </div>
                            @endif
                            @if($imagen)
                                <div class="text-center">
                                    <p class="text-[10px] font-bold text-blue-500 uppercase mb-1">Nueva</p>
                                    <img src="{{ $imagen->temporaryUrl() }}" class="w-16 h-16 rounded-xl object-cover border border-blue-200">
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="md:col-span-2 flex items-center gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="destacado"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-400">
                            <span class="text-sm text-gray-700 font-medium">Destacar este evento</span>
                        </label>
                        <span class="text-xs text-gray-400">(aparece primero en la ficha pública)</span>
                    </div>
                </div>

                <div class="flex justify-end items-center mt-5 gap-3">
                    <button type="button" wire:click="cancelar"
                            wire:loading.attr="disabled" wire:target="guardar"
                            class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Cancelar</button>
                    <button type="submit"
                            wire:loading.attr="disabled" wire:target="guardar"
                            class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg wire:loading wire:target="guardar" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        <span wire:loading.remove wire:target="guardar">{{ $editandoId ? 'Guardar cambios' : 'Publicar en agenda' }}</span>
                        <span wire:loading wire:target="guardar">Publicando…</span>
                    </button>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- ===== LISTADO DE EVENTOS ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h4 class="font-bold text-gray-700 mb-4">Eventos programados</h4>

        @php
            $proximos = $eventos->filter(fn($e) => $e->fecha && $e->fecha->greaterThanOrEqualTo(today()))->sortBy('fecha');
            $pasados  = $eventos->filter(fn($e) => $e->fecha && $e->fecha->lessThan(today()))->sortByDesc('fecha');
        @endphp

        @if($eventos->count())
            {{-- Próximos --}}
            @if($proximos->count())
            <div class="space-y-3 mb-6">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Próximos</p>
                @foreach($proximos as $evento)
                @php
                    $tipoInfo = $evento->tipoEvento();
                    $ventaActiva = $evento->datos['entradas_flow_activo'] ?? false;
                    $pagadas = $ventaActiva ? $evento->entradas->where('estado', 'pagada') : collect();
                    $totalVendidas = $pagadas->sum('cantidad_entradas');
                    $totalRecaudado = $pagadas->sum('monto_total');
                @endphp
                <div class="border border-gray-100 rounded-xl p-4
                    {{ $evento->destacado ? 'border-blue-200 bg-blue-50/30' : '' }}">
                <div class="flex items-start gap-4">
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
                        <button type="button" wire:click="editar({{ $evento->id }})"
                                class="text-xs text-blue-600 font-bold hover:underline">Editar</button>
                        <button type="button" wire:click="eliminar({{ $evento->id }})"
                                wire:confirm="¿Eliminar este evento?"
                                class="text-xs text-red-500 font-bold hover:underline">Eliminar</button>
                    </div>
                </div>

                @if($ventaActiva)
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-600">
                        🎟️ {{ $totalVendidas }} entrada(s) vendida(s) · ${{ number_format($totalRecaudado, 0, ',', '.') }}
                    </p>
                    @if($evento->entradas->isNotEmpty())
                    <details class="mt-2">
                        <summary class="text-xs font-bold text-blue-600 cursor-pointer hover:underline">
                            Ver compradores ({{ $evento->entradas->count() }})
                        </summary>
                        @php
                            $badgeClasses = [
                                'amber' => 'bg-amber-100 text-amber-700',
                                'green' => 'bg-green-100 text-green-700',
                                'red'   => 'bg-red-100 text-red-700',
                                'gray'  => 'bg-gray-100 text-gray-700',
                            ];
                        @endphp
                        <div class="mt-2 overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="text-left text-gray-400 uppercase text-[10px] tracking-wide">
                                        <th class="py-1 pr-3">Cliente</th>
                                        <th class="py-1 pr-3">Contacto</th>
                                        <th class="py-1 pr-3">Cant.</th>
                                        <th class="py-1 pr-3">Monto</th>
                                        <th class="py-1 pr-3">Estado</th>
                                        <th class="py-1 pr-3">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evento->entradas->sortByDesc('created_at') as $entrada)
                                    @php $estadoInfo = $entrada->estadoInfo(); @endphp
                                    <tr class="border-t border-gray-50">
                                        <td class="py-1.5 pr-3 font-medium text-gray-700">{{ $entrada->nombre_cliente }}</td>
                                        <td class="py-1.5 pr-3 text-gray-500">{{ $entrada->email_cliente }}<br>{{ $entrada->telefono_cliente }}</td>
                                        <td class="py-1.5 pr-3 text-gray-700">{{ $entrada->cantidad_entradas }}</td>
                                        <td class="py-1.5 pr-3 text-gray-700">${{ number_format($entrada->monto_total, 0, ',', '.') }}</td>
                                        <td class="py-1.5 pr-3">
                                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $badgeClasses[$estadoInfo['color']] ?? $badgeClasses['gray'] }}">
                                                {{ $estadoInfo['label'] }}
                                            </span>
                                        </td>
                                        <td class="py-1.5 pr-3 text-gray-400">{{ $entrada->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                    @endif
                </div>
                @endif
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
                    @php
                        $ventaActivaPasado = $evento->datos['entradas_flow_activo'] ?? false;
                        $pagadasPasado = $ventaActivaPasado ? $evento->entradas->where('estado', 'pagada') : collect();
                    @endphp
                    <div class="flex items-center justify-between border border-gray-100 rounded-xl p-3 opacity-60">
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $evento->datos['titulo'] ?? '' }}</p>
                            <p class="text-xs text-gray-400">{{ $evento->fecha->translatedFormat('d M Y') }}</p>
                            @if($ventaActivaPasado)
                            <p class="text-xs text-gray-400">
                                🎟️ {{ $pagadasPasado->sum('cantidad_entradas') }} vendida(s) · ${{ number_format($pagadasPasado->sum('monto_total'), 0, ',', '.') }}
                            </p>
                            @endif
                        </div>
                        <button type="button" wire:click="eliminar({{ $evento->id }})"
                                wire:confirm="¿Eliminar?"
                                class="text-xs text-red-400 hover:text-red-600">Eliminar</button>
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
