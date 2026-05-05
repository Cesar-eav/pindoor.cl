<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panoramas — Pindoor</h2>
            <a href="{{ route('admin.panoramas.create') }}"
               class="bg-[#fc5648] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#d94439] transition">
                + Nuevo panorama
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500 w-16">Orden</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500 w-20">Imagen</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Título</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Ubicación</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Fecha / Hora</th>
                            <th class="text-center px-5 py-3 font-semibold text-gray-500">Activo</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($panoramas as $panorama)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3 text-gray-400 font-mono text-center">{{ $panorama->orden }}</td>
                            <td class="px-5 py-3">
                                @if($panorama->imagen)
                                    <img src="{{ asset('storage/' . $panorama->imagen) }}"
                                         alt="{{ $panorama->titulo }}"
                                         class="w-14 h-14 object-cover rounded-lg border border-gray-100">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300 text-xl">📷</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ $panorama->titulo }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $panorama->ubicacion ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-500">
                                @if($panorama->fecha)
                                    {{ $panorama->fecha->format('d/m/Y') }}
                                @endif
                                @if($panorama->hora)
                                    <span class="text-gray-400">· {{ $panorama->hora }}</span>
                                @endif
                                @if(!$panorama->fecha && !$panorama->hora) — @endif
                            </td>
                            <td class="px-5 py-3 text-center">
                                <form action="{{ route('admin.panoramas.toggle', $panorama) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="text-xs font-bold px-3 py-1 rounded-full transition
                                                   {{ $panorama->activo
                                                        ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                        {{ $panorama->activo ? 'Visible' : 'Oculto' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.panoramas.edit', $panorama) }}"
                                       class="text-xs font-bold text-blue-600 hover:underline">Editar</a>
                                    <form action="{{ route('admin.panoramas.destroy', $panorama) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar «{{ addslashes($panorama->titulo) }}»?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-bold text-red-500 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-gray-400">
                                No hay panoramas. <a href="{{ route('admin.panoramas.create') }}" class="text-[#fc5648] font-bold hover:underline">Crea el primero.</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
