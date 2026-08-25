<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reclamos de perfil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-700">Solicitudes de activación</h3>
                    <span class="text-xs text-gray-400">{{ $reclamos->total() }} en total</span>
                </div>

                @if($reclamos->isEmpty())
                    <div class="p-10 text-center text-gray-400 text-sm">
                        Aún no hay reclamos de perfil.
                    </div>
                @else
                    @php
                        $badgeClasses = [
                            'amber' => 'bg-amber-100 text-amber-700',
                            'blue'  => 'bg-blue-100 text-blue-700',
                            'red'   => 'bg-red-100 text-red-600',
                            'green' => 'bg-green-100 text-green-700',
                        ];
                    @endphp
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-4">Negocio</th>
                                    <th class="px-6 py-4">Solicitante</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4">Fecha</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($reclamos as $reclamo)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('puntos.show', $reclamo->punto->slug) }}" target="_blank"
                                           class="font-medium text-gray-900 hover:underline">
                                            {{ $reclamo->punto->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800">{{ $reclamo->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $reclamo->email }}</div>
                                        @if($reclamo->whatsapp)
                                            <div class="text-xs text-gray-400">{{ $reclamo->whatsapp }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $badgeClasses[$reclamo->estadoInfo()['color']] }}">
                                            {{ $reclamo->estadoInfo()['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $reclamo->created_at->format('d-m-Y H:i') }}</td>
                                    <td class="px-6 py-4 text-right">
                                        @if($reclamo->status === 'pending')
                                            <div class="flex justify-end gap-3">
                                                <form method="POST" action="{{ route('admin.reclamos.aprobar', $reclamo) }}">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-bold">
                                                        Aprobar
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.reclamos.rechazar', $reclamo) }}"
                                                      onsubmit="return confirm('¿Rechazar este reclamo?')">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">
                                                        Rechazar
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $reclamos->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
