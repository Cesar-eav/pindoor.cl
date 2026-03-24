<x-app-layout>
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
</x-app-layout>