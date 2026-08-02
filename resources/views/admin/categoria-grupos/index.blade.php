<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-xl text-gray-900">Grupos de categorías</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $grupos->count() }} grupos · así se ven agrupados los íconos de categoría en el inicio</p>
            </div>
            <a href="{{ route('admin.categoria-grupos.create') }}"
               class="flex items-center gap-2 bg-[#fc5648] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#d94439] transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Nuevo grupo
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto px-4 sm:px-6" style="max-width:90%">

            @if(session('success'))
                <div class="mb-5 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9a1 1 0 012 0v4a1 1 0 01-2 0V9zm0-3a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide w-16">Ícono</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide">Grupo</th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide w-24">Orden</th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-gray-500 uppercase tracking-wide w-32">Categorías</th>
                            <th class="px-5 py-3 w-36"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($grupos as $grupo)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3">
                                <span class="w-9 h-9 rounded-xl bg-[#fff0ef] text-[#fc5648] flex items-center justify-center">
                                    <i class="fa-solid fa-{{ $grupo->icono ?: 'tag' }}"></i>
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <p class="font-bold text-gray-900">{{ $grupo->nombre }}</p>
                                <p class="text-xs text-gray-400">{{ $grupo->slug }}</p>
                            </td>
                            <td class="px-5 py-3 text-center text-gray-500">{{ $grupo->orden }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="text-xs font-bold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">{{ $grupo->categorias_count }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('admin.categoria-grupos.edit', $grupo) }}" class="text-xs font-bold text-blue-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('admin.categoria-grupos.destroy', $grupo) }}"
                                          onsubmit="return confirm('¿Eliminar «{{ addslashes($grupo->nombre) }}»? Sus categorías quedarán sin grupo.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-bold text-red-500 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-5 py-6 text-center text-gray-400 text-sm">Aún no hay grupos creados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
