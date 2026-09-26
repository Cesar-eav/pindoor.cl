<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Estadísticas de fichas
        </h2>
    </x-slot>

    @php $CANALES = \App\Http\Controllers\Admin\CompartidosController::CANALES; @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('admin.clientes') }}" class="text-sm text-gray-500 hover:text-gray-800 font-medium">
                ← Volver a clientes
            </a>

            {{-- Filtro de fechas --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-4">
                <div class="flex flex-wrap gap-2">
                    @foreach(['hoy' => 'Hoy', 'semana' => 'Semana', 'mes' => 'Mes', '30dias' => 'Últimos 30 días'] as $valor => $label)
                    <a href="{{ route('admin.estadisticas-fichas.index', ['rango' => $valor]) }}"
                       class="px-4 py-2 rounded-xl text-sm font-bold transition {{ $rangoActivo === $valor ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
                <form method="GET" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Desde</label>
                        <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}"
                               class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Hasta</label>
                        <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}"
                               class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                    </div>
                    <button type="submit"
                            class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-black transition">
                        Filtrar
                    </button>
                </form>
                <p class="text-xs text-gray-400">
                    Entre {{ $desde->locale('es')->isoFormat('D MMM YYYY') }} y {{ $hasta->locale('es')->isoFormat('D MMM YYYY') }}
                </p>
            </div>

            {{-- Tarjetas KPI --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white shadow-sm rounded-2xl p-6 border-l-4 border-[#fc5648]">
                    <div class="text-sm font-medium text-gray-500 uppercase">Visitas a fichas</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1">{{ $totales['visitas'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">
                        Tasa de contacto (aprox.): <strong class="text-gray-600">{{ $tasaContacto }}%</strong>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-2xl p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Clics en "Cómo llegar"</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1">{{ $totales['como_llegar'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Personas que pidieron indicaciones en el mapa</div>
                </div>

                <div class="bg-white shadow-sm rounded-2xl p-6 border-l-4 border-[#25D366]">
                    <div class="text-sm font-medium text-gray-500 uppercase">Clics en WhatsApp</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1">{{ $totales['whatsapp'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Contactos directos desde la ficha</div>
                </div>

                <div class="bg-white shadow-sm rounded-2xl p-6 border-l-4 border-purple-400">
                    <div class="text-sm font-medium text-gray-500 uppercase">Compartidos</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1">{{ $totales['compartidos'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Todas las secciones del sitio</div>
                </div>
            </div>

            {{-- Gráfico de tendencia --}}
            <div class="bg-white shadow-sm rounded-2xl p-6">
                <h3 class="font-bold text-gray-700 mb-4">Tendencia en el rango</h3>
                <canvas id="grafico-tendencia-fichas" height="90"></canvas>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Top fichas más visitadas --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-bold text-gray-700">Fichas más visitadas</h3>
                    </div>
                    @if($topFichas->isEmpty())
                        <div class="p-10 text-center text-gray-400 text-sm">
                            Sin visitas registradas en este rango.
                        </div>
                    @else
                        <table class="w-full text-left text-sm">
                            <tbody class="divide-y divide-gray-100">
                                @foreach($topFichas as $fila)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-3 text-gray-400 font-bold w-8">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900">{{ $fila->punto->title }}</span>
                                            @if($fila->punto->es_cliente)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">Cliente</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $fila->punto->categoria?->icono }} {{ $fila->punto->categoria?->nombre ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-right font-bold text-gray-700">{{ $fila->total }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Compartidos por canal --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="text-sm font-medium text-gray-500 uppercase mb-4">Compartidos por canal</div>
                    @if($totales['compartidos'] > 0)
                    <div class="space-y-3">
                        @foreach($CANALES as $canalKey => $info)
                        @php
                            $valorCanal = (int) ($porCanal[$canalKey] ?? 0);
                            $anchoPct   = $valorCanal > 0 ? max(2, round($valorCanal / $maxCanal * 100)) : 0;
                            $pctTotal   = $totales['compartidos'] > 0 ? round($valorCanal / $totales['compartidos'] * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs font-bold text-gray-600 mb-1">
                                <span>{{ $info['emoji'] }} {{ $info['label'] }}</span>
                                <span class="text-gray-400 font-semibold">{{ $valorCanal }} · {{ $pctTotal }}%</span>
                            </div>
                            <div class="h-3 rounded-full bg-gray-50 overflow-hidden">
                                <div class="h-full rounded-full transition-all" style="width:{{ $anchoPct }}%; background:{{ $info['color'] }}"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-300 italic py-6 text-center">Sin datos en este rango de fechas.</p>
                    @endif
                </div>
            </div>

            {{-- Visitas por categoría --}}
            <div class="bg-white shadow-sm rounded-2xl p-6">
                <h3 class="font-bold text-gray-700 mb-4">Visitas por categoría</h3>
                @if($porCategoria->isEmpty())
                    <p class="text-sm text-gray-300 italic py-6 text-center">Sin datos en este rango de fechas.</p>
                @endif
                <canvas id="grafico-por-categoria" height="{{ max(120, $porCategoria->count() * 36) }}" class="{{ $porCategoria->isEmpty() ? 'hidden' : '' }}"></canvas>
            </div>

            <script type="application/json" id="data-tendencia-fichas">{{ Js::from($dias) }}</script>
            <script type="application/json" id="data-por-categoria">{{ Js::from($porCategoria->map(fn ($fila) => [
                'nombre' => $fila->categoria->nombre,
                'total'  => $fila->total,
            ])) }}</script>

        </div>
    </div>

    @vite(['resources/js/admin-estadisticas-fichas.js'])
</x-admin-layout>
