<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estadística de "Compartir"</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filtro de fechas --}}
            <form method="GET" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Desde</label>
                    <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}"
                           class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Hasta</label>
                    <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}"
                           class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <button type="submit"
                        class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-black transition">
                    Filtrar
                </button>
                <a href="{{ route('admin.compartidos.index') }}"
                   class="text-sm font-bold text-gray-400 hover:text-gray-600 transition">
                    Últimos 30 días
                </a>
            </form>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-sm font-medium text-gray-500 uppercase">Total de veces compartido</div>
                <div class="text-3xl font-bold text-gray-900">{{ $totalGeneral }}</div>
                <div class="text-xs text-gray-400 mt-1">
                    Entre {{ $desde->locale('es')->isoFormat('D MMM YYYY') }} y {{ $hasta->locale('es')->isoFormat('D MMM YYYY') }}
                </div>
            </div>

            {{-- Compartidos por día --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-sm font-medium text-gray-500 uppercase mb-4">Compartidos por día</div>
                @if($totalGeneral > 0)
                <div class="overflow-x-auto pb-1" style="scrollbar-width:none;">
                    <div class="flex items-end gap-1.5" style="min-width:{{ max($dias->count() * 28, 100) }}px; height:140px;">
                        @foreach($dias as $dia)
                        @php $alturaPct = $dia->total > 0 ? max(4, round($dia->total / $maxDia * 100)) : 0; @endphp
                        <div class="flex-1 flex flex-col items-center justify-end h-full group relative" style="min-width:20px;">
                            <div class="absolute -top-6 left-1/2 -translate-x-1/2 hidden group-hover:block bg-gray-900 text-white text-[10px] font-bold px-2 py-1 rounded-lg whitespace-nowrap z-10">
                                {{ $dia->total }} · {{ \Carbon\Carbon::parse($dia->fecha)->locale('es')->isoFormat('D MMM') }}
                            </div>
                            <div class="w-full rounded-t-md transition-all {{ $dia->total > 0 ? 'bg-[#fc5648] group-hover:bg-gray-900' : 'bg-gray-100' }}"
                                 style="height:{{ $alturaPct }}%; min-height:2px;"></div>
                            <span class="text-[9px] text-gray-400 mt-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-300 italic py-6 text-center">Sin datos en este rango de fechas.</p>
                @endif
            </div>

            {{-- Por categoría y canal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 text-sm font-medium text-gray-500 uppercase">
                    Desde dónde se comparte
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3 text-left">Categoría</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-right">🟢 WhatsApp</th>
                            <th class="px-5 py-3 text-right">📤 Nativo</th>
                            <th class="px-5 py-3 text-right">🔗 Copiado</th>
                            <th class="px-5 py-3 text-right">📅 Calendario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($porCategoria as $fila)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-2 font-bold text-gray-800">
                                    {{ $fila->emoji }} {{ $fila->label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-gray-900">{{ $fila->total }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['whatsapp'] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['nativo'] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['copiar'] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['calendario'] ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400 italic">
                                Todavía no se ha compartido nada en este rango.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Por página y canal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 text-sm font-medium text-gray-500 uppercase">
                    Qué se comparte
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3 text-left">Página</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-right">🟢 WhatsApp</th>
                            <th class="px-5 py-3 text-right">📤 Nativo</th>
                            <th class="px-5 py-3 text-right">🔗 Copiado</th>
                            <th class="px-5 py-3 text-right">📅 Calendario</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($compartidos as $fila)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3">
                                <a href="{{ $fila->url }}" target="_blank" rel="noopener" title="{{ $fila->url }}"
                                   class="inline-flex items-center gap-2 hover:underline">
                                    <span class="shrink-0 px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-bold whitespace-nowrap">
                                        {{ $fila->seccion['emoji'] }} {{ $fila->seccion['label'] }}
                                    </span>
                                    @if($fila->seccion['detalle'])
                                    <span class="text-[#fc5648] truncate max-w-70">{{ $fila->seccion['detalle'] }}</span>
                                    @endif
                                </a>
                            </td>
                            <td class="px-5 py-3 text-right font-bold text-gray-900">{{ $fila->total }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['whatsapp'] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['nativo'] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['copiar'] ?? 0 }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $fila->por_canal['calendario'] ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400 italic">
                                Todavía no se ha compartido nada en este rango.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Actividad reciente --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 text-sm font-medium text-gray-500 uppercase">
                    Actividad reciente
                    <span class="normal-case font-normal text-gray-400">— últimos {{ $recientes->count() }} envíos</span>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3 text-left">Cuándo</th>
                            <th class="px-5 py-3 text-left">Canal</th>
                            <th class="px-5 py-3 text-left">Página compartida</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $canalInfo = [
                                'whatsapp'   => ['emoji' => '🟢', 'label' => 'WhatsApp'],
                                'nativo'     => ['emoji' => '📤', 'label' => 'Nativo'],
                                'copiar'     => ['emoji' => '🔗', 'label' => 'Copiado'],
                                'calendario' => ['emoji' => '📅', 'label' => 'Calendario'],
                            ];
                        @endphp
                        @forelse($recientes as $item)
                        @php $canal = $canalInfo[$item->canal] ?? ['emoji' => '❔', 'label' => $item->canal]; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-gray-500 whitespace-nowrap">
                                {{ $item->created_at->locale('es')->isoFormat('D MMM, HH:mm') }}
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600">
                                    {{ $canal['emoji'] }} {{ $canal['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <a href="{{ $item->url }}" target="_blank" rel="noopener"
                                   class="text-[#fc5648] hover:underline break-all">{{ $item->url }}</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-gray-400 italic">
                                Todavía no se ha compartido nada en este rango.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-admin-layout>
