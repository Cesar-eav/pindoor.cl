<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-xl text-gray-900">Categorías de eventos</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $categorias->count() }} categorías · usadas en panoramas y en la agenda de clientes</p>
            </div>
            <a href="{{ route('admin.categoria-eventos.create') }}"
               class="flex items-center gap-2 bg-[#fc5648] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#d94439] transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Nueva categoría
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
                    <thead class="bg-[#fff5f4] border-b border-red-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide w-8">#</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide">Categoría</th>
                            <th class="text-left px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide w-20">Orden</th>
                            <th class="text-center px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide w-20">En uso</th>
                            <th class="px-5 py-3 w-36"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($categorias as $cat)
                        @include('admin.categoria-eventos._fila', ['cat' => $cat])
                        @empty
                        <tr><td colspan="5" class="px-5 py-6 text-center text-gray-400 text-sm">Sin categorías de eventos todavía.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
