<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ticketera — {{ $operador->nombre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                @if($operador->rutas->isEmpty())
                    <div class="p-16 text-center text-gray-400 text-sm">
                        Este operador aún no tiene rutas asignadas.
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($operador->rutas as $ruta)
                        <div class="p-5 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 truncate">{{ $ruta->titulo }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    @if($ruta->pivot->ticketing_activo)
                                        <span class="text-green-600 font-bold">🎟️ Ticketing activo</span>
                                        · desde ${{ number_format($ruta->pivot->precio_individual ?? 0, 0, ',', '.') }}
                                    @else
                                        <span class="text-gray-400">Ticketing inactivo</span>
                                    @endif
                                </p>
                            </div>
                            <a href="{{ route('admin.operadores.rutas.edit', [$operador, $ruta->pivot->id]) }}"
                               class="shrink-0 bg-[#fc5648] hover:bg-[#e64536] text-white text-xs font-bold px-4 py-2 rounded-xl transition">
                                Configurar
                            </a>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-3">Asignar una ruta existente</h3>
                @if($rutasDisponibles->isEmpty())
                    <p class="text-sm text-gray-400">No hay rutas publicadas disponibles para asignar.</p>
                @else
                    <form action="{{ route('admin.operadores.rutas.store', $operador) }}" method="POST" class="flex gap-3">
                        @csrf
                        <select name="ruta_id" required
                                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            <option value="">Selecciona una ruta…</option>
                            @foreach($rutasDisponibles as $ruta)
                                <option value="{{ $ruta->id }}">{{ $ruta->titulo }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                                class="bg-teal-600 hover:bg-teal-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition">
                            Asignar
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
