<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Clientes (Negocios)
            </h2>
            <a href="{{ route('admin.clientes.dashboard') }}"
               class="inline-flex items-center gap-2 bg-[#fc5648] text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                📊 Ver dashboard
            </a>
        </div>
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
                <h3 class="font-bold text-gray-700 mb-1">Activar un espacio como cliente</h3>
                <p class="text-xs text-gray-400 mb-4">Busca un punto de interés existente para convertirlo en cliente y crear sus credenciales de acceso.</p>

                <livewire:selector-espacio-cliente />
            </div>

            {{-- Pendientes de aprobación --}}
            @if($pendientes->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-amber-200 overflow-hidden">
                <div class="p-6 border-b border-amber-100 bg-amber-50 flex items-center justify-between">
                    <h3 class="font-bold text-amber-800">⏳ Pendientes de aprobación</h3>
                    <span class="text-xs text-amber-600">{{ $pendientes->count() }} esperando revisión</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-4">Negocio</th>
                                <th class="px-6 py-4">Categoría</th>
                                <th class="px-6 py-4">WhatsApp</th>
                                <th class="px-6 py-4">Email usuario</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($pendientes as $punto)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-900">{{ $punto->title }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $punto->categoria?->nombre ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $punto->contacto_whatsapp ?: '—' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $punto->user?->email ?? '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-3">
                                        <a href="{{ route('admin.puntos.edit', $punto) }}"
                                           class="text-xs text-gray-500 hover:text-gray-800 font-medium">
                                            Editar
                                        </a>
                                        <form method="POST" action="{{ route('admin.clientes.aprobar', $punto) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-green-600 hover:text-green-800 font-bold">
                                                Aprobar
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.clientes.rechazar', $punto) }}"
                                              onsubmit="return confirm('¿Rechazar «{{ $punto->title }}»? Queda oculto pero editable.')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-600 font-medium">
                                                Rechazar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Destacados del home: curación + orden --}}
            @if($clientesDestacables->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-700 mb-1">Destacados del home</h3>
                <p class="text-xs text-gray-400 mb-4">
                    Marca qué negocios aparecen en la sección "Destacados" de la portada y arrastra para
                    definir el orden en que se muestran.
                </p>

                <form action="{{ route('admin.clientes.destacados') }}" method="POST">
                    @csrf
                    <div id="destacados-grid" class="divide-y divide-gray-50 border border-gray-100 rounded-xl">
                        @foreach($clientesDestacables as $punto)
                        <div draggable="true" data-tile data-id="{{ $punto->id }}"
                             class="flex items-center gap-3 px-4 py-3 cursor-move bg-white select-none">
                            <span class="text-gray-300">⠿</span>
                            <label class="flex items-center gap-3 flex-1 min-w-0 cursor-pointer">
                                <input type="checkbox" data-checkbox value="1" @checked($punto->destacado_home)
                                       class="rounded border-gray-300 text-[#fc5648] focus:ring-[#fc5648]">
                                <span class="font-medium text-gray-800 text-sm truncate">{{ $punto->title }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ $punto->categoria?->nombre }}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <button type="submit"
                            class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                        Guardar destacados
                    </button>
                </form>
            </div>
            @endif

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
                                    <th class="px-6 py-4">Última actividad</th>
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
                                            <div class="flex items-center gap-2">
                                                @if($punto->user->imagen_logo)
                                                    <img src="{{ asset('storage/' . $punto->user->imagen_logo) }}"
                                                         class="w-7 h-7 rounded-md object-cover border border-gray-200 shrink-0">
                                                @endif
                                                <div>
                                                    <div class="font-medium text-gray-800">{{ $punto->user->name }}</div>
                                                    <div class="text-xs text-gray-400">{{ $punto->user->email }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ route('admin.clientes.activar.form', $punto) }}"
                                               class="text-xs text-pindoor-accent font-bold hover:underline">
                                                + Crear credenciales
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-500">
                                        @if($punto->actividades->isNotEmpty())
                                            {{ $punto->actividades->first()->created_at->diffForHumans() }}
                                        @else
                                            <span class="text-gray-300 italic">Sin actividad</span>
                                        @endif
                                        @if($punto->user?->last_login_at)
                                            <div class="text-xs text-gray-400">Último login: {{ $punto->user->last_login_at->diffForHumans() }}</div>
                                        @elseif($punto->user)
                                            <div class="text-xs text-amber-500">Nunca ha ingresado</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ $punto->activo ? 'Activo' : 'Pausado' }}
                                        </span>
                                        @if($punto->estado_aprobacion === 'rechazado')
                                            <span class="block mt-1 px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600 w-fit">
                                                Rechazado
                                            </span>
                                        @elseif($punto->estado_aprobacion === 'pendiente')
                                            <span class="block mt-1 px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 w-fit">
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('admin.clientes.actividad', $punto) }}"
                                               class="text-xs text-blue-500 hover:text-blue-700 font-medium">
                                                Actividad
                                            </a>
                                            <a href="{{ route('admin.clientes.modulos.form', $punto) }}"
                                               class="text-xs text-indigo-500 hover:text-indigo-700 font-medium">
                                                Módulos
                                            </a>
                                            <a href="{{ route('admin.puntos.edit', $punto) }}"
                                               class="text-xs text-gray-500 hover:text-gray-800 font-medium">
                                                Editar
                                            </a>
                                            <form method="POST" action="{{ route('admin.clientes.desactivar', $punto) }}"
                                                  onsubmit="return confirm('¿Quitar este espacio como cliente?')">
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

    <script>
        // Destacados del home: arrastrar filas reordena el DOM; el checkbox marca si el
        // negocio se destaca. Al enviar, se arman orden[] (todas las filas, en su posición
        // final) y destacados[] (solo las marcadas) como inputs hidden.
        (function() {
            var grid = document.getElementById('destacados-grid');
            if (!grid) return;
            var dragged = null;

            grid.addEventListener('dragstart', function(e) {
                var tile = e.target.closest('[data-tile]');
                if (!tile) return;
                dragged = tile;
                e.dataTransfer.effectAllowed = 'move';
                setTimeout(function() { tile.classList.add('opacity-30'); }, 0);
            });

            grid.addEventListener('dragend', function() {
                if (dragged) dragged.classList.remove('opacity-30');
                dragged = null;
            });

            grid.addEventListener('dragover', function(e) {
                e.preventDefault();
            });

            grid.addEventListener('drop', function(e) {
                e.preventDefault();
                var target = e.target.closest('[data-tile]');
                if (!target || !dragged || target === dragged) return;
                var tiles = Array.prototype.slice.call(grid.children);
                var draggedIdx = tiles.indexOf(dragged);
                var targetIdx  = tiles.indexOf(target);
                if (draggedIdx < targetIdx) {
                    target.after(dragged);
                } else {
                    target.before(dragged);
                }
            });

            grid.closest('form').addEventListener('submit', function(e) {
                var form = e.target;
                Array.prototype.slice.call(grid.children).forEach(function(tile) {
                    var id = tile.dataset.id;
                    var checked = tile.querySelector('[data-checkbox]').checked;

                    var ordenInput = document.createElement('input');
                    ordenInput.type = 'hidden';
                    ordenInput.name = 'orden[]';
                    ordenInput.value = id;
                    form.appendChild(ordenInput);

                    if (checked) {
                        var destacadoInput = document.createElement('input');
                        destacadoInput.type = 'hidden';
                        destacadoInput.name = 'destacados[]';
                        destacadoInput.value = id;
                        form.appendChild(destacadoInput);
                    }
                });
            });
        })();
    </script>
</x-admin-layout>
