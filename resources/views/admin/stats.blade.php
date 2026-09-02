<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel de Administración Pindoor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-l-4 border-pindoor-accent">
                    <div class="text-sm font-medium text-gray-500 uppercase">Locales Totales</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $totalPuntos }}</div>
                    <div class="text-xs text-green-600 font-bold mt-1">{{ $puntosActivos }} visibles en el mapa</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Dueños de Negocios</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $totalClientes }}</div>
                    <div class="text-xs text-gray-400 mt-1">Usuarios registrados como 'cliente'</div>
                </div>

                <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-2xl p-6 text-white">
                    <div class="text-sm font-medium text-gray-300 uppercase">Estado del Sistema</div>
                    <div class="text-xl font-bold">Valparaíso Online 🚠</div>
                    <p class="text-xs text-gray-400 mt-2">Todo funcionando correctamente en el puerto.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            {{-- Leads Contacto --}}
            <div class="bg-white shadow-sm sm:rounded-2xl p-6 flex items-center justify-between border-l-4 border-amber-400">
                <div>
                    <div class="text-sm font-medium text-gray-500 uppercase">Consultas de Contacto</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $totalLeads }}</div>
                    @if($leadsNuevos > 0)
                        <div class="text-xs text-amber-600 font-bold mt-1">{{ $leadsNuevos }} sin contactar</div>
                    @else
                        <div class="text-xs text-green-600 font-bold mt-1">Todos contactados</div>
                    @endif
                </div>
                <a href="{{ route('admin.leads') }}"
                   class="text-sm font-bold text-amber-600 hover:underline">
                    Ver consultas →
                </a>
            </div>

            {{-- Compartidos --}}
            <div class="bg-white shadow-sm sm:rounded-2xl p-6 flex items-center justify-between border-l-4 border-[#fc5648]">
                <div>
                    <div class="text-sm font-medium text-gray-500 uppercase">Compartidos</div>
                    <div class="flex items-end gap-4 mt-1">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $compartidosHoy }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Hoy</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $compartidosSemana }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Semana</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $compartidosMes }}</div>
                            <div class="text-[10px] text-gray-400 uppercase font-bold">Mes</div>
                        </div>
                    </div>
                </div>
                <a href="{{ route('admin.compartidos.index') }}"
                   class="text-sm font-bold text-[#fc5648] hover:underline shrink-0">
                    Ver detalle →
                </a>
            </div>

            {{-- Actividad de clientes --}}
            <div class="bg-white shadow-sm sm:rounded-2xl p-6 border-l-4 border-blue-400">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-sm font-medium text-gray-500 uppercase">Actividad de clientes</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $actividadUltimos30Dias }}</div>
                        <div class="text-xs text-gray-400 mt-1">Acciones en los últimos 30 días</div>
                    </div>
                    <a href="{{ route('admin.clientes') }}"
                       class="text-sm font-bold text-blue-500 hover:underline shrink-0">
                        Ver clientes →
                    </a>
                </div>
                @if($clientesMasActivos->isNotEmpty())
                    <div class="border-t border-gray-100 pt-3 space-y-1.5">
                        <div class="text-[10px] text-gray-400 uppercase font-bold mb-1">Negocios más activos</div>
                        @foreach($clientesMasActivos as $fila)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-700 truncate">{{ $fila->puntoInteres?->title ?? '—' }}</span>
                                <span class="text-gray-400 text-xs font-bold shrink-0 ml-2">{{ $fila->total }} acciones</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-bold text-gray-700">Nuevos Puntos de Interés</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Sector</th>
                                    <th class="px-6 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($ultimosPuntos as $punto)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium">{{ $punto->title }}</td>
                                    <td class="px-6 py-4">{{ $punto->sector }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="#" class="text-pindoor-accent font-bold hover:underline">Ver</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="font-bold text-gray-700">Últimos Dueños Registrados</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                                <tr>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3 text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($ultimosClientes as $cliente)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium">{{ $cliente->name }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $cliente->email }}</td>
                                    <td class="px-6 py-4 text-right text-xs">{{ $cliente->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-admin-layout>