<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Boletín — Suscriptores</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-sm font-medium text-gray-500 uppercase">Total de suscriptores</div>
                <div class="text-3xl font-bold text-gray-900">{{ $suscriptores->total() }}</div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3 text-left">Email</th>
                            <th class="px-5 py-3 text-left">Origen</th>
                            <th class="px-5 py-3 text-left">Fecha</th>
                            <th class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($suscriptores as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $s->email }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $s->origen }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $s->created_at->locale('es')->isoFormat('D MMM YYYY, HH:mm') }}</td>
                            <td class="px-5 py-3 text-right">
                                <form action="{{ route('admin.newsletter.destroy', $s) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar «{{ addslashes($s->email) }}»?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-bold text-red-400 hover:underline">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-gray-400 italic">
                                Todavía no hay suscriptores.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex justify-center">
                {{ $suscriptores->links() }}
            </div>

        </div>
    </div>
</x-admin-layout>
