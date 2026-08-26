<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Guías</h2>
            <a href="{{ route('admin.blog.create') }}"
               class="bg-[#fc5648] text-white text-sm font-bold px-4 py-2 rounded-xl hover:bg-[#d94439] transition">
                + Nueva guía
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
        @endif

        @if($posts->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 px-6 py-16 text-center text-gray-400">
            <div class="text-5xl mb-4">✍️</div>
            <p class="font-semibold text-lg">Aún no hay guías.</p>
            <p class="text-sm mt-1">Crea el primero con el botón de arriba.</p>
        </div>
        @else
        <div class="flex justify-end mb-3">
            <button type="button" id="btn-guardar-orden-guias" class="text-xs font-bold text-[#fc5648] hover:underline">Guardar orden</button>
        </div>
        <div id="guias-grid" class="bg-white rounded-2xl border border-gray-100 overflow-hidden divide-y divide-gray-50">
            @foreach($posts as $post)
            <div class="flex items-center gap-4 px-6 py-4" draggable="true" data-tile data-id="{{ $post->id }}">

                <span class="text-gray-300 cursor-move shrink-0">⠿</span>

                {{-- Portada miniatura --}}
                <div class="shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-gray-100 border border-gray-100">
                    @if($post->imagen_portada_url)
                        <img src="{{ $post->imagen_portada_url }}" alt="{{ $post->titulo }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-2xl text-gray-300">📝</div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 truncate">{{ $post->titulo }}</p>
                    <div class="flex items-center gap-3 mt-0.5">
                        @if($post->publicado)
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-green-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Publicado · {{ $post->publicado_en?->translatedFormat('d M Y') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                Borrador
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-3 shrink-0">
                    @if($post->publicado)
                    <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                       class="text-xs font-bold text-gray-400 hover:text-gray-700 transition">Ver</a>
                    @endif
                    <a href="{{ route('admin.blog.edit', $post) }}"
                       class="text-xs font-bold text-blue-600 hover:underline">Editar</a>
                    <form action="{{ route('admin.blog.destroy', $post) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar «{{ addslashes($post->titulo) }}»?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-red-400 hover:underline">Eliminar</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <script>
        (function() {
            var grid = document.getElementById('guias-grid');
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

            document.getElementById('btn-guardar-orden-guias')?.addEventListener('click', function() {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.blog.reordenar') }}';
                form.innerHTML = '@csrf';
                Array.prototype.slice.call(grid.children).forEach(function(tile) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'orden[]';
                    input.value = tile.dataset.id;
                    form.appendChild(input);
                });
                document.body.appendChild(form);
                form.submit();
            });
        })();
    </script>
</x-admin-layout>
