<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.clientes') }}" class="text-gray-400 hover:text-gray-700 text-sm">&larr; Clientes</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Módulos — {{ $punto->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.clientes.modulos', $punto) }}">
                @csrf @method('PUT')

                @php
                    $grupos = collect($catalogo)
                        ->map(fn($m, $key) => array_merge($m, ['slug' => $key]))
                        ->groupBy('grupo');
                    $config = [
                        'Transversal' => ['emoji' => '🌐', 'border' => 'border-gray-200',   'header' => 'bg-gray-50',    'badge' => 'bg-gray-200 text-gray-700'],
                        'Gastronomía' => ['emoji' => '🍽️', 'border' => 'border-orange-200', 'header' => 'bg-orange-50',  'badge' => 'bg-orange-100 text-orange-700'],
                        'Alojamiento' => ['emoji' => '🛏️', 'border' => 'border-indigo-200', 'header' => 'bg-indigo-50',  'badge' => 'bg-indigo-100 text-indigo-700'],
                        'Museo'       => ['emoji' => '🏛️', 'border' => 'border-amber-200',  'header' => 'bg-amber-50',   'badge' => 'bg-amber-100 text-amber-700'],
                        'Cultura'     => ['emoji' => '🎭', 'border' => 'border-blue-200',   'header' => 'bg-blue-50',    'badge' => 'bg-blue-100 text-blue-700'],
                    ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($grupos as $grupo => $modulos)
                    @php $c = $config[$grupo] ?? $config['General']; @endphp

                    <div class="bg-white rounded-2xl border {{ $c['border'] }} overflow-hidden shadow-sm">

                        {{-- Header del grupo --}}
                        <div class="{{ $c['header'] }} px-5 py-3 flex items-center gap-2 border-b {{ $c['border'] }}">
                            <span class="text-lg">{{ $c['emoji'] }}</span>
                            <span class="font-black text-sm text-gray-700 uppercase tracking-wide">{{ $grupo }}</span>
                            <span class="ml-auto text-xs {{ $c['badge'] }} font-bold px-2 py-0.5 rounded-full">
                                {{ $modulos->count() }} {{ $modulos->count() === 1 ? 'módulo' : 'módulos' }}
                            </span>
                        </div>

                        {{-- Checkboxes --}}
                        <div class="divide-y divide-gray-100">
                            @foreach($modulos as $modulo)
                            <label class="flex items-center gap-3 px-5 py-3.5 cursor-pointer hover:bg-gray-50 transition">
                                <input type="checkbox"
                                       name="modulos[]"
                                       value="{{ $modulo['slug'] }}"
                                       @checked(in_array($modulo['slug'], $punto->modulos_habilitados ?? []))
                                       class="rounded border-gray-300 text-pindoor-accent focus:ring-pindoor-accent shrink-0">
                                <span class="text-lg">{{ $modulo['emoji'] }}</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $modulo['label'] }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $modulo['desc'] }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center mt-6">
                    <a href="{{ route('admin.clientes') }}"
                       class="text-sm text-gray-500 hover:text-gray-700">Cancelar</a>
                    <button type="submit"
                            class="px-6 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                        Guardar módulos
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
