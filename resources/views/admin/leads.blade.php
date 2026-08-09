<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Consultas de Contacto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtro --}}
            <div class="flex gap-2 mb-4">
                <a href="{{ route('admin.leads') }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition {{ $filtro === 'pendiente' ? 'bg-gray-900 text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-400' }}">
                    Pendientes ({{ $pendientesCount }})
                </a>
                <a href="{{ route('admin.leads', ['estado' => 'todos']) }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition {{ $filtro === 'todos' ? 'bg-gray-900 text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-400' }}">
                    Todas
                </a>
            </div>

            @php
            $badgeClasses = [
                'amber'  => 'bg-amber-100 text-amber-700',
                'blue'   => 'bg-blue-100 text-blue-700',
                'violet' => 'bg-violet-100 text-violet-700',
                'gray'   => 'bg-gray-100 text-gray-700',
                'red'    => 'bg-red-100 text-red-700',
                'green'  => 'bg-green-100 text-green-700',
            ];
            @endphp

            <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4">Email / Teléfono</th>
                            <th class="px-6 py-4">Mensaje</th>
                            <th class="px-6 py-4">Fecha</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-data="{ abierto: null }">
                        @forelse($leads as $lead)
                        @php $info = $lead->estadoInfo(); @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ $lead->nombre }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div>{{ $lead->email }}</div>
                                @if($lead->telefono)
                                    <div class="text-xs text-gray-400">{{ $lead->telefono }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 max-w-xs">
                                <span class="line-clamp-2 text-xs">{{ $lead->mensaje ?? '—' }}</span>
                                @if($lead->observaciones)
                                    <p class="text-[11px] text-violet-500 font-semibold mt-1 line-clamp-2">📝 {{ $lead->observaciones }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400 whitespace-nowrap">
                                {{ $lead->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $badgeClasses[$info['color']] }}">
                                    {{ $info['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click="abierto = (abierto === {{ $lead->id }}) ? null : {{ $lead->id }}"
                                        class="text-xs font-bold text-gray-600 hover:text-gray-900 hover:underline transition">
                                    Gestionar
                                </button>
                            </td>
                        </tr>
                        <tr x-show="abierto === {{ $lead->id }}" x-cloak>
                            <td colspan="6" class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                <form action="{{ route('admin.leads.update', $lead) }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-start">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Estado</label>
                                        <select name="estado" class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none bg-white">
                                            @foreach(\App\Models\LeadContacto::ESTADOS as $valor => $estadoInfo)
                                                <option value="{{ $valor }}" {{ $lead->estado === $valor ? 'selected' : '' }}>{{ $estadoInfo['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1 w-full">
                                        <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Observaciones</label>
                                        <textarea name="observaciones" rows="2" placeholder="Notas de seguimiento…"
                                                  class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none resize-none">{{ $lead->observaciones }}</textarea>
                                    </div>
                                    <button type="submit"
                                            class="shrink-0 bg-gray-900 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-black transition">
                                        Guardar
                                    </button>
                                </form>
                                <form action="{{ route('admin.leads.destroy', $lead) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar esta consulta? No se puede deshacer.')" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-700 hover:underline transition">
                                        Eliminar consulta
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">
                                No hay consultas {{ $filtro === 'pendiente' ? 'pendientes' : '' }}.
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
</x-admin-layout>
