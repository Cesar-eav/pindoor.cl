<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Usuarios - Pindoor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-5 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-5 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    {{ session('error') }}
                </div>
            @endif
            <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Tipo</th>
                            <th class="px-6 py-4">Registro</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($usuarios as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($user->imagen_logo)
                                        <img src="{{ asset('storage/' . $user->imagen_logo) }}"
                                             alt="Logo {{ $user->name }}"
                                             class="w-9 h-9 rounded-lg object-cover border border-gray-200 shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-lg shrink-0">👤</div>
                                    @endif
                                    <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $user->type == 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                    {{ strtoupper($user->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($user->type !== 'admin' && !$user->es_sistema)
                                    <form method="POST" action="{{ route('admin.usuarios.destroy', $user) }}"
                                          class="flex flex-col items-end gap-1"
                                          onsubmit="return confirm(this.eliminar_negocio.checked
                                              ? '¿Eliminar a {{ addslashes($user->name) }}? Esto eliminará también su negocio (además de su perfil de artista u operador, si tiene, junto con sus imágenes). Esta acción no se puede deshacer.'
                                              : '¿Eliminar a {{ addslashes($user->name) }}? Su negocio NO se eliminará: quedará disponible para que alguien vuelva a reclamarlo. Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        @if($user->punto_interes_count > 0)
                                            <label class="flex items-center gap-1 text-[11px] text-gray-500">
                                                <input type="checkbox" name="eliminar_negocio" value="1" class="rounded border-gray-300 text-red-500 focus:ring-red-400">
                                                Eliminar también el negocio
                                            </label>
                                        @endif
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">
                                            Eliminar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-300">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 border-t border-gray-100">
                    {{ $usuarios->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>