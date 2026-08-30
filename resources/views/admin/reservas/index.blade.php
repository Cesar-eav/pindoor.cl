<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Reservas de Ticketera
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" class="flex flex-wrap gap-2 mb-4 items-center">
                <a href="{{ route('admin.reservas.index') }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold transition {{ !request('estado') ? 'bg-gray-900 text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-400' }}">
                    Todas
                </a>
                @foreach(\App\Models\ReservaRuta::ESTADOS_INFO as $valor => $info)
                    <a href="{{ route('admin.reservas.index', array_merge(request()->except('page'), ['estado' => $valor])) }}"
                       class="px-4 py-2 rounded-xl text-sm font-bold transition {{ request('estado') === $valor ? 'bg-gray-900 text-white' : 'bg-white text-gray-500 border border-gray-200 hover:border-gray-400' }}">
                        {{ $info['label'] }}
                    </a>
                @endforeach

                <select name="contactado" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white ml-2">
                    <option value="">Contacto: todos</option>
                    <option value="no" {{ request('contactado') === 'no' ? 'selected' : '' }}>Sin contactar ({{ $totalSinContactar }})</option>
                    <option value="si" {{ request('contactado') === 'si' ? 'selected' : '' }}>Contactados</option>
                </select>

                <select name="prueba" onchange="this.form.submit()"
                        class="px-3 py-2 border border-gray-200 rounded-xl text-sm bg-white">
                    <option value="" {{ !request('prueba') ? 'selected' : '' }}>Sin pruebas</option>
                    <option value="si" {{ request('prueba') === 'si' ? 'selected' : '' }}>Solo pruebas</option>
                    <option value="todas" {{ request('prueba') === 'todas' ? 'selected' : '' }}>Con pruebas</option>
                </select>

                <a href="{{ route('admin.pagos.prueba') }}"
                   class="px-4 py-2 rounded-xl text-sm font-bold bg-[#fff0ef] text-[#fc5648] hover:bg-[#fc5648] hover:text-white transition">
                    🧪 Probar pago
                </a>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar cliente, email, código..."
                       class="px-3 py-2 border border-gray-200 rounded-xl text-sm flex-1 min-w-[200px] focus:ring-2 focus:ring-gray-400 outline-none">
                <button type="submit" class="px-4 py-2 rounded-xl text-sm font-bold bg-gray-100 hover:bg-gray-200 transition">Buscar</button>
            </form>

            @php
            $badgeClasses = [
                'amber'  => 'bg-amber-100 text-amber-700',
                'green'  => 'bg-green-100 text-green-700',
                'red'    => 'bg-red-100 text-red-700',
                'gray'   => 'bg-gray-100 text-gray-700',
            ];
            @endphp

            <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4">Ruta / Operador</th>
                            <th class="px-6 py-4">Visita</th>
                            <th class="px-6 py-4">Personas</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-center">Contacto</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-data="{ abierto: null }">
                        @forelse($reservas as $reserva)
                        @php $info = $reserva->estadoInfo(); @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900 flex items-center gap-2">
                                    {{ $reserva->nombre_cliente }}
                                    @if($reserva->es_prueba)
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-[#fff0ef] text-[#fc5648]">PRUEBA</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">{{ $reserva->email_cliente }}</div>
                                <div class="text-xs text-gray-400">{{ $reserva->telefono_cliente }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <div class="font-medium text-gray-800">{{ $reserva->rutaOperador?->ruta?->titulo ?? '—' }}</div>
                                <div class="text-xs text-gray-400">{{ $reserva->rutaOperador?->operador?->nombre ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                <div>{{ $reserva->fecha_visita->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $reserva->horario?->hora ? \Carbon\Carbon::parse($reserva->horario->hora)->format('H:i') : '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ $reserva->cantidad_adultos }} adulto{{ $reserva->cantidad_adultos == 1 ? '' : 's' }}
                                @if($reserva->cantidad_ninos > 0)
                                    <br><span class="text-xs text-gray-400">{{ $reserva->cantidad_ninos }} niño{{ $reserva->cantidad_ninos == 1 ? '' : 's' }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-800 font-semibold whitespace-nowrap">
                                ${{ number_format($reserva->precio_total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $badgeClasses[$info['color']] }}">
                                    {{ $info['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($reserva->contactado)
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Contactado</span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500">Sin contactar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click="abierto = (abierto === {{ $reserva->id }}) ? null : {{ $reserva->id }}"
                                        class="text-xs font-bold text-gray-600 hover:text-gray-900 hover:underline transition">
                                    Gestionar
                                </button>
                            </td>
                        </tr>
                        <tr x-show="abierto === {{ $reserva->id }}" x-cloak>
                            <td colspan="8" class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                                <div class="text-xs text-gray-500 mb-3">
                                    Código: <span class="font-mono font-semibold text-gray-700">{{ $reserva->codigo_reserva }}</span>
                                    · Creada: {{ $reserva->created_at->format('d/m/Y H:i') }}
                                    @if($reserva->pagado_en)
                                        · Pagada: {{ $reserva->pagado_en->format('d/m/Y H:i') }}
                                    @endif
                                    @if($reserva->estado === 'pagada')
                                        · <a href="{{ route('admin.reservas.checkin.show', $reserva) }}" class="font-bold text-[#fc5648] hover:underline">
                                            {{ $reserva->checkin_at ? '✅ Check-in '.$reserva->checkin_at->format('d/m H:i') : 'Marcar check-in' }}
                                        </a>
                                    @endif
                                </div>
                                <form action="{{ route('admin.reservas.update', $reserva) }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-start">
                                    @csrf
                                    @method('PATCH')
                                    <div>
                                        <label class="flex items-center gap-2 text-xs font-bold uppercase text-gray-400 mb-1">
                                            <input type="checkbox" name="contactado" value="1" {{ $reserva->contactado ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-gray-900 focus:ring-gray-400">
                                            Cliente contactado
                                        </label>
                                    </div>
                                    <div class="flex-1 w-full">
                                        <label class="block text-[10px] font-bold uppercase text-gray-400 mb-1">Notas internas</label>
                                        <textarea name="notas_admin" rows="2" placeholder="Notas de seguimiento..."
                                                  class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none resize-none">{{ $reserva->notas_admin }}</textarea>
                                    </div>
                                    <button type="submit"
                                            class="shrink-0 bg-gray-900 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-black transition">
                                        Guardar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-400 italic">
                                No hay reservas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            @if($reservas->hasPages())
                <div class="mt-4">{{ $reservas->links() }}</div>
            @endif

        </div>
    </div>
</x-admin-layout>
