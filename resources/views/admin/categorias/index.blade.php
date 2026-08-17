<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-xl text-gray-900">Categorías</h2>
                <p class="text-sm text-gray-400 mt-0.5">{{ $categorias->count() }} categorías · gestiona módulos y configuración</p>
            </div>
            <a href="{{ route('admin.categorias.create') }}"
               class="flex items-center gap-2 bg-[#fc5648] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#d94439] transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Nueva categoría
            </a>
        </div>
    </x-slot>

    @php
        $catalogo  = \App\Models\PuntoInteres::catalogoModulos();
        $clientes  = $categorias->where('es_cliente', true)->sortBy('orden');
        $atractivos = $categorias->where('es_cliente', false)->sortBy('orden');
    @endphp

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

            {{-- ── SECCIÓN: NEGOCIOS (clientes) ───────────────────────────── --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#fc5648] text-white text-xs">🏪</span>
                        <h3 class="font-extrabold text-gray-900">Negocios / Clientes</h3>
                    </div>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $clientes->count() }} categorías</span>
                    <div class="flex-1 h-px bg-linear-to-r from-[#fc5648]/20 to-transparent"></div>
                </div>
                <p class="text-xs text-gray-400 mb-4">Disponibles para que los negocios registrados elijan su tipo. Soportan módulos por defecto.</p>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-[#fff5f4] border-b border-red-100">
                            <tr>
                                <th class="text-center px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide w-12">Orden</th>
                                <th class="text-left px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide">Categoría</th>
                                <th class="text-left px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide">Tipo</th>
                                <th class="text-left px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide">Módulos por defecto</th>
                                <th class="text-center px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide w-20">Puntos</th>
                                <th class="text-center px-5 py-3 text-xs font-bold text-[#fc5648] uppercase tracking-wide w-20">En home</th>
                                <th class="px-5 py-3 w-36"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($clientes as $cat)
                            @include('admin.categorias._fila', ['cat' => $cat, 'catalogo' => $catalogo])
                            @empty
                            <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400 text-sm">Sin categorías de negocios.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            {{-- ── SECCIÓN: ATRACTIVOS TURÍSTICOS ────────────────────────── --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-blue-500 text-white text-xs">🗺️</span>
                        <h3 class="font-extrabold text-gray-900">Atractivos turísticos</h3>
                    </div>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $atractivos->count() }} categorías</span>
                    <div class="flex-1 h-px bg-linear-to-r from-blue-200 to-transparent"></div>
                </div>
                <p class="text-xs text-gray-400 mb-4">Usadas para clasificar puntos de interés editorial (miradores, arte, monumentos, etc.).</p>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-50 border-b border-blue-100">
                            <tr>
                                <th class="text-center px-5 py-3 text-xs font-bold text-blue-600 uppercase tracking-wide w-12">Orden</th>
                                <th class="text-left px-5 py-3 text-xs font-bold text-blue-600 uppercase tracking-wide">Categoría</th>
                                <th class="text-left px-5 py-3 text-xs font-bold text-blue-600 uppercase tracking-wide">Tipo</th>
                                <th class="text-left px-5 py-3 text-xs font-bold text-blue-600 uppercase tracking-wide">Módulos por defecto</th>
                                <th class="text-center px-5 py-3 text-xs font-bold text-blue-600 uppercase tracking-wide w-20">Puntos</th>
                                <th class="text-center px-5 py-3 text-xs font-bold text-blue-600 uppercase tracking-wide w-20">En home</th>
                                <th class="px-5 py-3 w-36"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($atractivos as $cat)
                            @include('admin.categorias._fila', ['cat' => $cat, 'catalogo' => $catalogo])
                            @empty
                            <tr><td colspan="7" class="px-5 py-6 text-center text-gray-400 text-sm">Sin categorías de atractivos.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            {{-- ── Catálogo de módulos ──────────────────────────────────── --}}
            <div class="mt-4">
                <div class="flex items-center gap-3 mb-3">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide">Catálogo de módulos disponibles</h3>
                    <div class="flex-1 h-px bg-gray-100"></div>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($catalogo as $key => $mod)
                    <div class="bg-white rounded-xl border border-gray-100 px-4 py-3 flex items-start gap-3">
                        <span class="text-xl shrink-0">{{ $mod['emoji'] }}</span>
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $mod['label'] }}</p>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $mod['desc'] }}</p>
                            <span class="mt-1 inline-block text-[10px] font-mono bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $key }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
