<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Check-in de reserva
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @error('checkin')
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    {{ $message }}
                </div>
            @enderror

            @php $info = $reserva->estadoInfo(); @endphp

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase text-gray-400">Código</span>
                    <span class="font-mono font-extrabold text-lg text-gray-900">{{ $reserva->codigo_reserva }}</span>
                </div>

                <dl class="space-y-2 text-sm mb-6">
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Estado</dt>
                        <dd class="font-bold text-gray-900">{{ $info['label'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Cliente</dt>
                        <dd class="font-bold text-gray-900">{{ $reserva->nombre_cliente }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Ruta</dt>
                        <dd class="font-bold text-gray-900">{{ $reserva->rutaOperador?->ruta?->titulo ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Fecha</dt>
                        <dd class="font-bold text-gray-900">{{ $reserva->fecha_visita->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-400">Personas</dt>
                        <dd class="font-bold text-gray-900">
                            {{ $reserva->cantidad_adultos }} adulto{{ $reserva->cantidad_adultos == 1 ? '' : 's' }}
                            @if($reserva->cantidad_ninos > 0)
                                , {{ $reserva->cantidad_ninos }} niño{{ $reserva->cantidad_ninos == 1 ? '' : 's' }}
                            @endif
                        </dd>
                    </div>
                </dl>

                @if($reserva->checkin_at)
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm font-bold text-center">
                        ✅ Ingreso registrado el {{ $reserva->checkin_at->format('d/m/Y H:i') }}
                        @if($reserva->checkinPor)
                            <div class="font-normal text-xs mt-1">por {{ $reserva->checkinPor->name }}</div>
                        @endif
                    </div>
                @elseif($reserva->estado !== 'pagada')
                    <div class="bg-amber-50 border border-amber-200 text-amber-700 rounded-xl px-4 py-3 text-sm font-bold text-center">
                        Esta reserva no está pagada — no se puede marcar el ingreso.
                    </div>
                @else
                    <form action="{{ route('admin.reservas.checkin.store', $reserva) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full bg-[#fc5648] text-white rounded-xl px-5 py-3 font-bold hover:bg-[#e2483e] transition">
                            Confirmar ingreso
                        </button>
                    </form>
                @endif
            </div>

            <a href="{{ route('admin.reservas.index') }}" class="inline-block mt-6 text-sm font-bold text-gray-500 hover:underline">
                ← Volver a reservas
            </a>
        </div>
    </div>
</x-admin-layout>
