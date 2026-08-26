<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🧪 Probar pago con Flow
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <p class="text-sm text-gray-500 mb-6">
                Crea una reserva de prueba y te redirige a Flow ({{ config('services.flow.sandbox') ? 'sandbox' : 'producción' }})
                exactamente igual que una reserva real. Queda marcada como <strong>PRUEBA</strong> en
                <a href="{{ route('admin.reservas.index') }}" class="underline">Reservas</a> y no afecta el cupo real.
            </p>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.pagos.prueba.store') }}"
                  class="bg-white shadow-sm sm:rounded-2xl border border-gray-100 p-6 space-y-5"
                  x-data="{
                      rutaOperadorId: '{{ old('ruta_operador_id') }}',
                      horarios: {{ $rutasOperador->mapWithKeys(fn($ro) => [$ro->id => $ro->horariosActivos]) ->toJson() }},
                  }">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Ruta / Operador</label>
                    <select name="ruta_operador_id" x-model="rutaOperadorId" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none">
                        <option value="">Selecciona...</option>
                        @foreach($rutasOperador as $ro)
                            <option value="{{ $ro->id }}" {{ old('ruta_operador_id') == $ro->id ? 'selected' : '' }}>
                                {{ $ro->ruta?->titulo }} — {{ $ro->operador?->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if($rutasOperador->isEmpty())
                        <p class="text-xs text-red-500 mt-1">No hay ninguna ruta con ticketing activo todavía.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Horario</label>
                    <select name="horario_id" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none">
                        <template x-for="h in (horarios[rutaOperadorId] || [])" :key="h.id">
                            <option :value="h.id" x-text="h.hora.substring(0,5)"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Fecha de visita</label>
                    <input type="date" name="fecha_visita" value="{{ old('fecha_visita', now()->addDay()->format('Y-m-d')) }}" required
                           min="{{ now()->format('Y-m-d') }}"
                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Adultos</label>
                        <input type="number" name="cantidad_adultos" value="{{ old('cantidad_adultos', 1) }}" min="1" max="30" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Niños</label>
                        <input type="number" name="cantidad_ninos" value="{{ old('cantidad_ninos', 0) }}" min="0" max="30"
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Nombre cliente</label>
                    <input type="text" name="nombre_cliente" value="{{ old('nombre_cliente', auth()->user()->name) }}" required
                           class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Email cliente</label>
                        <input type="email" name="email_cliente" value="{{ old('email_cliente', auth()->user()->email) }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-gray-400 mb-1">Teléfono cliente</label>
                        <input type="text" name="telefono_cliente" value="{{ old('telefono_cliente', '+56900000000') }}" required
                               class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-gray-400 outline-none">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-[#fc5648] text-white px-5 py-3 rounded-xl text-sm font-bold hover:opacity-90 transition">
                    Ir a pagar con Flow
                </button>
            </form>

        </div>
    </div>
</x-admin-layout>
