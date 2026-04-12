<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Consultas de Publicita
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4">Email / Teléfono</th>
                            <th class="px-6 py-4">Negocio</th>
                            <th class="px-6 py-4">Mensaje</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($leads as $lead)
                        <tr class="hover:bg-gray-50 transition {{ $lead->contactado ? '' : 'bg-amber-50' }}">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $lead->nombre }}</td>
                            <td class="px-6 py-4 text-gray-600">
                                <div>{{ $lead->email }}</div>
                                @if($lead->telefono)
                                    <div class="text-xs text-gray-400">{{ $lead->telefono }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium">{{ $lead->negocio }}</td>
                            <td class="px-6 py-4 text-gray-500 max-w-xs">
                                <span class="line-clamp-2 text-xs">{{ $lead->mensaje ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400 whitespace-nowrap">
                                {{ $lead->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold
                                    {{ $lead->contactado ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $lead->contactado ? 'Contactado' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.leads.toggle', $lead) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-bold {{ $lead->contactado ? 'text-gray-400 hover:text-orange-500' : 'text-green-600 hover:text-green-800' }} hover:underline transition">
                                        {{ $lead->contactado ? 'Marcar pendiente' : 'Marcar contactado' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 italic">
                                No hay consultas todavía.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($leads->hasPages())
                <div class="mt-4">{{ $leads->links() }}</div>
            @endif

        </div>
    </div>
</x-app-layout>
