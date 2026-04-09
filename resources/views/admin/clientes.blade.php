<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Clientes (Negocios)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Panel: activar un punto como cliente --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-700 mb-1">Activar un negocio como cliente</h3>
                <p class="text-xs text-gray-400 mb-4">Selecciona un punto de interés existente para convertirlo en cliente y crear sus credenciales de acceso.</p>

                @if($puntosDisponibles->isEmpty())
                    <p class="text-sm text-gray-400 italic">Todos los puntos ya están activados como clientes.</p>
                @else
                    <div class="flex flex-wrap gap-3">
                        @foreach($puntosDisponibles as $punto)
                            <a href="{{ route('admin.clientes.activar.form', $punto) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-pindoor-accent hover:text-white text-gray-700 text-sm font-medium rounded-lg transition">
                                {{ $punto->title }}
                                <span class="text-xs opacity-60">{{ $punto->sector }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Tabla de clientes activos --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-700">Clientes registrados</h3>
                    <span class="text-xs text-gray-400">{{ $clientes->total() }} en total</span>
                </div>

                @if($clientes->isEmpty())
                    <div class="p-10 text-center text-gray-400 text-sm">
                        Aún no hay negocios activados como clientes.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                <tr>
                                    <th class="px-6 py-4">Negocio</th>
                                    <th class="px-6 py-4">Categoría</th>
                                    <th class="px-6 py-4">Sector</th>
                                    <th class="px-6 py-4">Usuario vinculado</th>
                                    <th class="px-6 py-4">Estado</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($clientes as $punto)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $punto->title }}</div>
                                        @if($punto->imagen_perfil)
                                            <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                                                 class="w-8 h-8 rounded-full object-cover mt-1" alt="logo">
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">{{ $punto->sector }}</td>
                                    <td class="px-6 py-4">
                                        @if($punto->user && $punto->user->type === 'cliente')
                                            <div class="font-medium text-gray-800">{{ $punto->user->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $punto->user->email }}</div>
                                        @else
                                            <a href="{{ route('admin.clientes.activar.form', $punto) }}"
                                               class="text-xs text-pindoor-accent font-bold hover:underline">
                                                + Crear credenciales
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $punto->activo ? 'Activo' : 'Pausado' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.puntos.edit', $punto) }}"
                                               class="text-xs text-gray-500 hover:text-gray-800 font-medium">
                                                Editar
                                            </a>
                                            <form method="POST" action="{{ route('admin.clientes.desactivar', $punto) }}"
                                                  onsubmit="return confirm('¿Quitar este negocio como cliente?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">
                                                    Desactivar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 border-t border-gray-100">
                        {{ $clientes->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
