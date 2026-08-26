<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Operadores turísticos</h2>
            <a href="{{ route('admin.operadores.create') }}"
               class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                + Nuevo operador
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                @if($operadores->isEmpty())
                    <div class="p-16 text-center text-gray-400 text-sm">
                        Aún no hay operadores turísticos registrados.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-4">Operador</th>
                                    <th class="px-6 py-4">Ciudad</th>
                                    <th class="px-6 py-4">Cuenta</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($operadores as $operador)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($operador->imagen_perfil)
                                                <img src="{{ asset('storage/' . $operador->imagen_perfil) }}"
                                                     class="w-10 h-10 rounded-full object-cover shrink-0" alt="">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-lg shrink-0">🧭</div>
                                            @endif
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $operador->nombre }}</div>
                                                <div class="text-xs text-gray-400">{{ $operador->slug }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $operador->ciudad ?: '—' }}</td>
                                    <td class="px-6 py-4">
                                        @if($operador->usuario)
                                            <div class="font-medium text-gray-800">{{ $operador->usuario->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $operador->usuario->email }}</div>
                                        @else
                                            <span class="text-gray-400 text-xs">Sin cuenta</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold
                                            {{ $operador->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $operador->activo ? 'Activo' : 'Pausado' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-4">
                                            <a href="{{ route('admin.operadores.rutas.index', $operador) }}"
                                               class="text-xs text-[#fc5648] hover:underline font-bold">
                                                🎟️ Ticketera
                                            </a>
                                            <a href="{{ route('admin.operadores.edit', $operador) }}"
                                               class="text-xs text-gray-500 hover:text-gray-800 font-medium">
                                                Editar
                                            </a>
                                            <form method="POST" action="{{ route('admin.operadores.toggle', $operador) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                        class="text-xs font-medium {{ $operador->activo ? 'text-amber-500 hover:text-amber-700' : 'text-green-500 hover:text-green-700' }}">
                                                    {{ $operador->activo ? 'Pausar' : 'Activar' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.operadores.destroy', $operador) }}"
                                                  onsubmit="return confirm('¿Eliminar el perfil de {{ addslashes($operador->nombre) }}? Esta acción no se puede deshacer.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($operadores->hasPages())
                        <div class="p-4 border-t border-gray-100">
                            {{ $operadores->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
