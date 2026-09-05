<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard de clientes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <a href="{{ route('admin.clientes') }}" class="text-sm text-gray-500 hover:text-gray-800 font-medium">
                ← Volver a clientes
            </a>

            {{-- Tarjetas numéricas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm sm:rounded-2xl p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Logins de clientes</div>
                    <div class="flex items-end gap-4 mt-1">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $loginsHoy }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Hoy</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $loginsSemana }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Semana</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $loginsMes }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Mes</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-2xl p-6 border-l-4 border-[#fc5648]">
                    <div class="text-sm font-medium text-gray-500 uppercase">Acciones en el panel</div>
                    <div class="flex items-end gap-4 mt-1">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $accionesHoy }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Hoy</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $accionesSemana }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Semana</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $accionesMes }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Mes</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Segmentación de clientes por actividad real --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm sm:rounded-2xl p-5 border-l-4 border-green-500">
                    <div class="text-xs font-bold text-gray-500 uppercase">Activos</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $segmentosResumen['activo'] }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Actividad últimos 7 días</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-2xl p-5 border-l-4 border-amber-400">
                    <div class="text-xs font-bold text-gray-500 uppercase">Tibios</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $segmentosResumen['tibio'] }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Entre 8 y 30 días</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-2xl p-5 border-l-4 border-gray-400">
                    <div class="text-xs font-bold text-gray-500 uppercase">Inactivos</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $segmentosResumen['inactivo'] }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Más de 30 días sin actividad</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-2xl p-5 border-l-4 border-[#fc5648]">
                    <div class="text-xs font-bold text-gray-500 uppercase">Nunca ingresaron</div>
                    <div class="text-2xl font-bold text-gray-900 mt-1">{{ $segmentosResumen['nunca_conecto'] }}</div>
                    <div class="text-[11px] text-gray-400 mt-1">Requieren seguimiento</div>
                </div>
            </div>

            {{-- Gráficos --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                    <h3 class="font-bold text-gray-700 mb-4">Logins por día (últimos 30 días)</h3>
                    <canvas id="chart-logins" height="220"></canvas>
                </div>
                <div class="bg-white shadow-sm sm:rounded-2xl p-6">
                    <h3 class="font-bold text-gray-700 mb-4">Acciones por tipo (últimos 30 días)</h3>
                    <canvas id="chart-acciones" height="220"></canvas>
                </div>
            </div>

            <script type="application/json" id="data-logins-por-dia">{{ Js::from($loginsPorDia) }}</script>
            <script type="application/json" id="data-acciones-por-tipo">{{ Js::from($accionesPorTipo) }}</script>

            {{-- Tabla resumen por cliente --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden"
                 x-data="{ busqueda: '', segmentoFiltro: 'todos' }">
                <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="font-bold text-gray-700">Resumen por cliente</h3>
                    @if($resumenClientes->isNotEmpty())
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" x-model="busqueda" placeholder="Buscar negocio..."
                                   class="text-sm rounded-lg border-gray-200 focus:border-[#fc5648] focus:ring-[#fc5648]">
                            <select x-model="segmentoFiltro"
                                    class="text-sm rounded-lg border-gray-200 focus:border-[#fc5648] focus:ring-[#fc5648]">
                                <option value="todos">Todos los segmentos</option>
                                <option value="activo">Activos</option>
                                <option value="tibio">Tibios</option>
                                <option value="inactivo">Inactivos</option>
                                <option value="nunca_conecto">Nunca ingresaron</option>
                            </select>
                        </div>
                    @endif
                </div>

                @if($resumenClientes->isEmpty())
                    <div class="p-10 text-center text-gray-400 text-sm">
                        Aún no hay negocios activados como clientes.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-4">Negocio</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4">Última actividad</th>
                                    <th class="px-6 py-4 text-right">Acciones (semana / mes)</th>
                                    <th class="px-6 py-4 text-right">Logins (semana / mes)</th>
                                    <th class="px-6 py-4 text-right">Detalle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php
                                    $badges = [
                                        'activo'        => ['bg-green-100 text-green-700', 'Activo'],
                                        'tibio'         => ['bg-amber-100 text-amber-700', 'Tibio'],
                                        'inactivo'      => ['bg-gray-100 text-gray-600', 'Inactivo'],
                                        'nunca_conecto' => ['bg-red-100 text-[#fc5648]', 'Nunca ingresó'],
                                    ];
                                @endphp
                                @foreach($resumenClientes as $fila)
                                <tr class="hover:bg-gray-50 transition"
                                    x-show="('{{ Str::lower(addslashes($fila->punto->title)) }}'.includes(busqueda.toLowerCase())) &&
                                            (segmentoFiltro === 'todos' || segmentoFiltro === '{{ $fila->segmento }}')">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $fila->punto->title }}</div>
                                        <div class="text-xs text-gray-400">{{ $fila->punto->user?->name ?? '—' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php [$clases, $etiqueta] = $badges[$fila->segmento]; @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $clases }}">
                                            {{ $etiqueta }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $fila->ultima_actividad->diffForHumans() }}</td>
                                    <td class="px-6 py-4 text-right text-gray-500">
                                        {{ $fila->acciones_semana }} <span class="text-gray-300">/</span> {{ $fila->acciones_mes }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-500">
                                        {{ $fila->logins_semana }} <span class="text-gray-300">/</span> {{ $fila->logins_mes }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.clientes.actividad', $fila->punto) }}"
                                           class="text-xs text-blue-500 hover:text-blue-700 font-medium">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>

    @vite(['resources/js/admin-clientes-dashboard.js'])
</x-admin-layout>
