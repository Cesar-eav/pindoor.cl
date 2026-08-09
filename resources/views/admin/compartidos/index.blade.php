<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estadística de "Compartir"</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-sm font-medium text-gray-500 uppercase">Total de veces compartido</div>
                <div class="text-3xl font-bold text-gray-900">{{ $totalGeneral }}</div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3 text-left">Página</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-right">🟢 WhatsApp</th>
                            <th class="px-5 py-3 text-right">📤 Nativo</th>
                            <th class="px-5 py-3 text-right">🔗 Copiado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($compartidos as $fila)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <a href="{{ $fila->url }}" target="_blank" rel="noopener"
                                   class="text-[#fc5648] hover:underline break-all">{{ $fila->url }}</a>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-gray-900">{{ $fila->total }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['whatsapp'] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['nativo'] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['copiar'] ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-gray-400 italic">
                                Todavía no se ha compartido nada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-admin-layout>
