<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Actividad de {{ $punto->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('admin.clientes') }}" class="text-sm text-gray-500 hover:text-gray-800 font-medium">
                ← Volver a clientes
            </a>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-700">Registro de actividad</h3>
                    <span class="text-xs text-gray-400">{{ $actividades->total() }} en total</span>
                </div>

                @if($actividades->isEmpty())
                    <div class="p-10 text-center text-gray-400 text-sm">
                        Este negocio aún no tiene actividad registrada.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-4">Acción</th>
                                    <th class="px-6 py-4">Detalle</th>
                                    <th class="px-6 py-4">Usuario</th>
                                    <th class="px-6 py-4 text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($actividades as $actividad)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $actividad->tipo)) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $actividad->detalle ?? '—' }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $actividad->user?->name ?? '—' }}</td>
                                    <td class="px-6 py-4 text-right text-xs text-gray-400">
                                        {{ $actividad->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $actividades->links() }}
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-gray-700">Historial reconstruido (antes de este registro)</h3>
                    <p class="text-xs text-gray-400 mt-1">
                        No es un log exacto: son las fechas de creación/edición de lo que existe hoy (fotos, eventos,
                        exposiciones, productos, módulos y la ficha), reconstruidas para dar contexto anterior a este
                        registro de actividad. No refleja ediciones intermedias ni contenido eliminado.
                    </p>
                </div>

                @if($historico->isEmpty())
                    <div class="p-10 text-center text-gray-400 text-sm">
                        No hay contenido previo del cual reconstruir historial.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-4">Acción</th>
                                    <th class="px-6 py-4">Detalle</th>
                                    <th class="px-6 py-4 text-right">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($historico as $evento)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $evento->tipo)) }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $evento->detalle ?? '—' }}</td>
                                    <td class="px-6 py-4 text-right text-xs text-gray-400">
                                        {{ $evento->fecha->format('d/m/Y H:i') }}
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
</x-admin-layout>
