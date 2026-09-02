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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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

                <div class="bg-white shadow-sm sm:rounded-2xl p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Clientes</div>
                    <div class="flex items-end gap-4 mt-1">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $clientesActivos30d }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Activos (30d)</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $clientesNuncaConectados }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Nunca ingresaron</div>
                        </div>
                    </div>
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-gray-700">Resumen por cliente</h3>
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
                                    <th class="px-6 py-4">Usuario</th>
                                    <th class="px-6 py-4">Último login</th>
                                    <th class="px-6 py-4 text-right">Logins semana</th>
                                    <th class="px-6 py-4 text-right">Logins mes</th>
                                    <th class="px-6 py-4 text-right">Acciones semana</th>
                                    <th class="px-6 py-4 text-right">Acciones mes</th>
                                    <th class="px-6 py-4 text-right">Detalle</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($resumenClientes as $fila)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $fila->punto->title }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $fila->punto->user?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-gray-500">
                                        @if($fila->punto->user?->last_login_at)
                                            {{ $fila->punto->user->last_login_at->diffForHumans() }}
                                        @else
                                            <span class="text-amber-500">Nunca</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">{{ $fila->logins_semana }}</td>
                                    <td class="px-6 py-4 text-right">{{ $fila->logins_mes }}</td>
                                    <td class="px-6 py-4 text-right">{{ $fila->acciones_semana }}</td>
                                    <td class="px-6 py-4 text-right">{{ $fila->acciones_mes }}</td>
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
