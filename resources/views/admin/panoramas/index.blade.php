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
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Configuración periodo --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 flex flex-wrap items-center gap-4">
                <span class="text-sm font-semibold text-gray-600">🗓️ Periodo público:</span>
                <form action="{{ route('admin.panoramas.configuracion') }}" method="POST"
                      class="flex items-center gap-2">
                    @csrf
                    <span class="text-sm text-gray-500">Mostrar los próximos</span>
                    <input type="number" name="panoramas_limite_dias"
                           value="{{ $limiteDias }}" min="1" max="365"
                           class="w-16 px-2 py-1 border border-gray-200 rounded-lg text-sm text-center font-bold focus:ring-2 focus:ring-[#fc5648] outline-none">
                    <span class="text-sm text-gray-500">días</span>
                    <button type="submit"
                            class="bg-gray-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg hover:bg-gray-700 transition">
                        Guardar
                    </button>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold text-gray-500 w-12">Ord.</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-500 w-20">Imagen</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-500">Título · Categoría</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-500 hidden md:table-cell">Fecha / Hora</th>
                            <th class="text-left px-4 py-3 font-semibold text-gray-500 hidden lg:table-cell">Ubicación</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-500">Activo</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($panoramas as $panorama)
                        @php
                            $cat = $panorama->categoria
                                ? (\App\Models\Panorama::CATEGORIAS[$panorama->categoria] ?? null)
                                : null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Orden --}}
                            <td class="px-4 py-3 text-gray-400 font-mono text-center text-xs">{{ $panorama->orden }}</td>

                            {{-- Imagen --}}
                            <td class="px-4 py-3">
                                @if($panorama->imagen)
                                    <img src="{{ asset('storage/' . $panorama->imagen) }}"
                                         alt="{{ $panorama->titulo }}"
                                         class="w-14 h-14 object-cover rounded-lg border border-gray-100">
                                @else
                                    <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300 text-xl">📷</div>
                                @endif
                            </td>

                            {{-- Título + categoría --}}
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-800 leading-snug">{{ $panorama->titulo }}</p>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    @if($cat)
                                    <span class="inline-flex items-center gap-0.5 text-[11px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                        {{ $cat['emoji'] }} {{ $cat['label'] }}
                                    </span>
                                    @endif
                                    @if($panorama->es_gratuito)
                                    <span class="text-[11px] font-bold bg-green-50 text-green-700 px-2 py-0.5 rounded-full">🎟️ Gratis</span>
                                    @endif
                                    @if($panorama->enlace)
                                    <span class="text-[11px] font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">🔗 Enlace</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Fecha / Hora --}}
                            <td class="px-4 py-3 text-gray-500 hidden md:table-cell whitespace-nowrap">
                                @if($panorama->fecha)
                                    @if($panorama->fecha_fin && !$panorama->fecha->isSameDay($panorama->fecha_fin))
                                        <p class="font-medium text-gray-700">
                                            {{ $panorama->fecha->format('d/m/Y') }}
                                            <span class="text-gray-400">→</span>
                                            {{ $panorama->fecha_fin->format('d/m/Y') }}
                                        </p>
                                    @else
                                        <p class="font-medium text-gray-700">{{ $panorama->fecha->format('d/m/Y') }}</p>
                                    @endif
                                @endif
                                @if($panorama->hora)
                                    <p class="text-xs text-gray-400 mt-0.5">🕐 {{ $panorama->hora }}</p>
                                @endif
                                @if(!$panorama->fecha && !$panorama->hora)
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- Ubicación --}}
                            <td class="px-4 py-3 text-gray-500 text-xs hidden lg:table-cell">
                                {{ $panorama->ubicacion ?: '—' }}
                            </td>

                            {{-- Activo --}}
                            <td class="px-4 py-3 text-center">
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

                            {{-- Acciones --}}
                            <td class="px-4 py-3 text-right">
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
                                No hay panoramas.
                                <a href="{{ route('admin.panoramas.create') }}"
                                   class="text-[#fc5648] font-bold hover:underline">Crea el primero.</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
