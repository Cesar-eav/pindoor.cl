<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Categorías</h2>
            <a href="{{ route('admin.categorias.create') }}"
               class="bg-[#fc5648] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#d94439] transition">
                + Nueva categoría
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Icono</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Nombre</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Slug</th>
                            <th class="text-left px-5 py-3 font-semibold text-gray-500">Tipo</th>
                            <th class="text-right px-5 py-3 font-semibold text-gray-500">Puntos</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($categorias as $cat)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3 text-xl">{{ $cat->icono }}</td>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ $cat->nombre }}</td>
                            <td class="px-5 py-3 text-gray-400 font-mono text-xs">{{ $cat->slug }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $cat->tipo ?? '—' }}</td>
                            <td class="px-5 py-3 text-right text-gray-500">{{ $cat->puntos_interes_count }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.categorias.edit', $cat) }}"
                                       class="text-xs font-bold text-blue-600 hover:underline">Editar</a>
                                    <form action="{{ route('admin.categorias.destroy', $cat) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar «{{ $cat->nombre }}»?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-bold text-red-500 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-gray-400">No hay categorías.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
