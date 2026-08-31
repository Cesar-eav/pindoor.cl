@php
    $horariosData = $rutaOperador->horarios->map(fn($h) => [
        '_key'        => 'h' . $h->id,
        'id'          => $h->id,
        'tipo'        => $h->tipo,
        'dias_semana' => $h->dias_semana ?? [],
        'fecha'       => $h->fecha?->format('Y-m-d') ?? '',
        'hora'        => substr($h->hora, 0, 5),
        'cupo_maximo' => $h->cupo_maximo,
    ])->values();
    $bloqueosData = $rutaOperador->bloqueos->map(fn($b) => [
        '_key'   => 'b' . $b->id,
        'fecha'  => $b->fecha->format('Y-m-d'),
        'motivo' => $b->motivo ?? '',
    ])->values();
@endphp
<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ticketera — {{ $rutaOperador->ruta->titulo }}
            </h2>
            <a href="{{ route('admin.operadores.rutas.index', $operador) }}" class="text-sm text-gray-500 hover:text-gray-800">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="ticketeraForm(@js($horariosData), @js($bloqueosData))">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.operadores.rutas.update', [$operador, $rutaOperador]) }}" method="POST">
                @csrf @method('PUT')

                {{-- Precios --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-700">Precios y ticketing</h3>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="ticketing_activo" value="1"
                                   @checked(old('ticketing_activo', $rutaOperador->ticketing_activo))
                                   class="rounded text-[#fc5648] focus:ring-[#fc5648]">
                            <span class="text-sm font-bold text-gray-700">Ticketing activo</span>
                        </label>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">1 persona sola ($)</label>
                            <input type="number" name="precio_individual" min="0"
                                   value="{{ old('precio_individual', $rutaOperador->precio_individual ?? 25000) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Por adulto (2+ personas) ($)</label>
                            <input type="number" name="precio_grupo_adulto" min="0"
                                   value="{{ old('precio_grupo_adulto', $rutaOperador->precio_grupo_adulto ?? 15000) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Por niño ($)</label>
                            <input type="number" name="precio_nino" min="0"
                                   value="{{ old('precio_nino', $rutaOperador->precio_nino ?? 10000) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Edad máxima niño</label>
                            <input type="number" name="edad_maxima_nino" min="0" max="99"
                                   value="{{ old('edad_maxima_nino', $rutaOperador->edad_maxima_nino ?? 14) }}" required
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Notas internas (opcional)</label>
                            <input type="text" name="notas_operador" value="{{ old('notas_operador', $rutaOperador->notas_operador) }}"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-3">
                        Ej: 1 adulto solo = $25.000 flat. 1 adulto + 1 niño (2 personas) = $15.000 + $10.000 = $25.000. 2 adultos + 1 niño = $40.000.
                    </p>
                </div>

                {{-- Horarios --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-sm font-bold text-gray-700 mb-4">Horarios y disponibilidad</h3>

                    <template x-for="(h, index) in horarios" :key="h._key">
                        <div class="border border-gray-100 rounded-xl p-4 mb-3">
                            <input type="hidden" :name="`horarios[${index}][id]`" :value="h.id">

                            <div class="flex flex-wrap items-end gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Tipo</label>
                                    <select :name="`horarios[${index}][tipo]`" x-model="h.tipo"
                                            class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                        <option value="semanal">Recurrente (días de semana)</option>
                                        <option value="fecha">Fecha puntual</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Hora</label>
                                    <input type="time" :name="`horarios[${index}][hora]`" x-model="h.hora" required
                                           class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-500 mb-1">Cupo máximo</label>
                                    <input type="number" min="1" :name="`horarios[${index}][cupo_maximo]`" x-model="h.cupo_maximo" required
                                           class="w-24 px-3 py-2 border border-gray-200 rounded-lg text-sm">
                                </div>

                                <button type="button" @click="quitar(index)"
                                        class="text-xs text-red-400 hover:text-red-600 font-bold px-2 py-2">
                                    Quitar
                                </button>
                            </div>

                            <div x-show="h.tipo === 'semanal'" class="mt-3 flex flex-wrap gap-3">
                                <template x-for="dia in diasSemana" :key="dia.valor">
                                    <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-600">
                                        <input type="checkbox"
                                               :checked="h.dias_semana.includes(dia.valor)"
                                               @change="toggleDia(h, dia.valor, $event.target.checked)"
                                               class="rounded text-[#fc5648] focus:ring-[#fc5648]">
                                        <span x-text="dia.nombre"></span>
                                    </label>
                                </template>
                                <template x-for="dv in h.dias_semana" :key="'dv'+dv">
                                    <input type="hidden" :name="`horarios[${index}][dias_semana][]`" :value="dv">
                                </template>
                            </div>

                            <div x-show="h.tipo === 'fecha'" class="mt-3">
                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Fecha</label>
                                <input type="date" :name="`horarios[${index}][fecha]`" x-model="h.fecha"
                                       class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>
                        </div>
                    </template>

                    <button type="button" @click="agregar()"
                            class="text-sm font-bold text-[#fc5648] hover:underline">
                        + Agregar horario
                    </button>
                </div>

                {{-- Fechas bloqueadas --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="text-sm font-bold text-gray-700 mb-1">Fechas bloqueadas</h3>
                    <p class="text-xs text-gray-400 mb-4">Cierra fechas puntuales (feriados, mantenciones) para todos los horarios de esta ruta.</p>

                    <template x-for="(b, index) in bloqueos" :key="b._key">
                        <div class="flex flex-wrap items-end gap-3 border border-gray-100 rounded-xl p-4 mb-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Fecha</label>
                                <input type="date" :name="`bloqueos[${index}][fecha]`" x-model="b.fecha" required
                                       class="px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>

                            <div class="flex-1 min-w-48">
                                <label class="block text-[11px] font-semibold text-gray-500 mb-1">Motivo (opcional)</label>
                                <input type="text" :name="`bloqueos[${index}][motivo]`" x-model="b.motivo"
                                       placeholder="Ej: Feriado, mantención"
                                       class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                            </div>

                            <button type="button" @click="quitarBloqueo(index)"
                                    class="text-xs text-red-400 hover:text-red-600 font-bold px-2 py-2">
                                Quitar
                            </button>
                        </div>
                    </template>

                    <button type="button" @click="agregarBloqueo()"
                            class="text-sm font-bold text-[#fc5648] hover:underline">
                        + Bloquear fecha
                    </button>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-[#fc5648] hover:bg-[#e64536] text-white font-bold px-6 py-2.5 rounded-xl text-sm transition">
                        Guardar configuración
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function ticketeraForm(horariosIniciales, bloqueosIniciales) {
        return {
            diasSemana: [
                { valor: 1, nombre: 'Lun' }, { valor: 2, nombre: 'Mar' }, { valor: 3, nombre: 'Mié' },
                { valor: 4, nombre: 'Jue' }, { valor: 5, nombre: 'Vie' }, { valor: 6, nombre: 'Sáb' },
                { valor: 7, nombre: 'Dom' },
            ],
            horarios: horariosIniciales,
            bloqueos: bloqueosIniciales,
            agregar() {
                this.horarios.push({
                    _key: 'nuevo' + Date.now() + Math.random(),
                    id: null, tipo: 'semanal', dias_semana: [], fecha: '', hora: '10:00', cupo_maximo: 15,
                });
            },
            quitar(index) {
                this.horarios.splice(index, 1);
            },
            toggleDia(h, valor, marcado) {
                if (marcado && !h.dias_semana.includes(valor)) {
                    h.dias_semana.push(valor);
                } else if (!marcado) {
                    h.dias_semana = h.dias_semana.filter(d => d !== valor);
                }
            },
            agregarBloqueo() {
                this.bloqueos.push({
                    _key: 'nuevo' + Date.now() + Math.random(),
                    fecha: '', motivo: '',
                });
            },
            quitarBloqueo(index) {
                this.bloqueos.splice(index, 1);
            },
        };
    }
    </script>
</x-admin-layout>
