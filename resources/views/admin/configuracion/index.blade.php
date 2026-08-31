<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-gray-900">Configuración general</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto px-4 sm:px-6" style="max-width:720px">

            @if(session('success'))
                <div class="mb-5 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.configuracion.actualizar') }}" method="POST">
                @csrf
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="aprobacion_negocios_activa" value="1"
                               @checked($aprobacionActiva)
                               class="mt-1 rounded border-gray-300 text-[#fc5648] focus:ring-[#fc5648]">
                        <div>
                            <p class="font-bold text-gray-900">Requerir aprobación de admin para negocios nuevos</p>
                            <p class="text-xs text-gray-500 mt-1">
                                Con esto activo, el onboarding le pide al negocio un WhatsApp de contacto y su ficha
                                queda oculta hasta que un admin la revise y apruebe desde
                                <a href="{{ route('admin.clientes') }}" class="text-[#fc5648] underline">Clientes</a>.
                                Los negocios que ya existen no se ven afectados.
                            </p>
                        </div>
                    </label>
                </div>

                <button type="submit"
                        class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                    Guardar
                </button>
            </form>

            {{-- Atractivos por categoría en la home --}}
            <form action="{{ route('admin.configuracion.home-por-categoria') }}" method="POST" class="mt-6">
                @csrf
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <label for="home_puntos_por_categoria" class="font-bold text-gray-900 block">
                        Atractivos por categoría en la home
                    </label>
                    <p class="text-xs text-gray-500 mt-1 mb-3">
                        Cuántos puntos se muestran, como máximo, en cada carrusel de categoría de la página de inicio.
                        Para elegir qué categorías aparecen y en qué orden, entra a
                        <a href="{{ route('admin.categorias.index') }}" class="text-[#fc5648] underline">Categorías</a>.
                    </p>
                    <input type="number" id="home_puntos_por_categoria" name="home_puntos_por_categoria"
                           min="1" max="100" value="{{ old('home_puntos_por_categoria', $homePorCategoria) }}"
                           class="w-32 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                    @error('home_puntos_por_categoria') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                    Guardar
                </button>
            </form>

            {{-- Orden de las secciones del home --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                <p class="font-bold text-gray-900">Orden de las secciones del home</p>
                <p class="text-xs text-gray-500 mt-1 mb-4">
                    Arrastra para cambiar el orden en que aparecen los bloques en la portada.
                </p>

                <form action="{{ route('admin.configuracion.orden-secciones') }}" method="POST">
                    @csrf
                    <div id="orden-secciones-grid" class="divide-y divide-gray-50 border border-gray-100 rounded-xl">
                        @foreach($ordenSecciones as $clave)
                        <div draggable="true" data-tile data-clave="{{ $clave }}"
                             class="flex items-center gap-3 px-4 py-3 cursor-move bg-white select-none">
                            <span class="text-gray-300">⠿</span>
                            <span class="font-medium text-gray-800 text-sm">{{ $etiquetasSecciones[$clave] ?? $clave }}</span>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="orden" id="orden-secciones-input">

                    <button type="submit"
                            class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                        Guardar orden
                    </button>
                </form>
            </div>

            {{-- Puntos de ejemplo / demo --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                <p class="font-bold text-gray-900">Puntos de ejemplo (no son negocios reales)</p>
                <p class="text-xs text-gray-500 mt-1 mb-4">
                    Se crearon para mostrarle a un cliente prospecto cómo quedaría su local antes de registrarse.
                    Quedan afuera de todo lo público (mapa, sitemap, listados, búsqueda, ficha directa) — solo se
                    siguen mostrando a propósito en <span class="font-semibold">/registro</span> y
                    <span class="font-semibold">/contacto</span> como muestra.
                </p>

                <form action="{{ route('admin.configuracion.excluidos') }}" method="POST"
                      x-data="{
                          todos: {{ Illuminate\Support\Js::from($puntosData) }},
                          categorias: {{ Illuminate\Support\Js::from($categoriasDisponibles) }},
                          seleccionados: {{ Illuminate\Support\Js::from($idsExcluidos) }},
                          busqueda: '',
                          categoria: '',
                          get filtrados() {
                              const q = this.busqueda.trim().toLowerCase();
                              return this.todos.filter(p =>
                                  !this.seleccionados.includes(p.id) &&
                                  (this.categoria === '' || p.categoria === this.categoria) &&
                                  (q === '' || (p.title + ' ' + (p.sector || '')).toLowerCase().includes(q))
                              );
                          },
                          agregar(id) { if (!this.seleccionados.includes(id)) this.seleccionados.push(id); },
                          quitar(id) { this.seleccionados = this.seleccionados.filter(x => x !== id); },
                          info(id) { return this.todos.find(p => p.id === id) || {}; },
                      }">
                    @csrf

                    {{-- Seleccionados --}}
                    <div class="flex flex-wrap gap-2 mb-3" x-show="seleccionados.length">
                        <template x-for="id in seleccionados" :key="id">
                            <span class="inline-flex items-center gap-1.5 bg-[#fff0ef] text-[#fc5648] text-xs font-bold pl-3 pr-1.5 py-1.5 rounded-full">
                                <span x-text="info(id).title"></span>
                                <button type="button" @click="quitar(id)"
                                        class="hover:bg-[#fc5648]/20 rounded-full w-4 h-4 flex items-center justify-center leading-none">×</button>
                            </span>
                        </template>
                    </div>
                    <p x-show="!seleccionados.length" class="text-xs text-gray-300 italic mb-3">Ningún punto marcado como ejemplo todavía.</p>

                    {{-- Filtro por categoría --}}
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        <button type="button" @click="categoria = ''"
                                :class="categoria === '' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                class="px-3 py-1 rounded-full text-xs font-bold transition">Todas</button>
                        <template x-for="cat in categorias" :key="cat">
                            <button type="button" @click="categoria = cat"
                                    :class="categoria === cat ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                    class="px-3 py-1 rounded-full text-xs font-bold transition" x-text="cat"></button>
                        </template>
                    </div>

                    {{-- Buscador --}}
                    <input type="text" x-model="busqueda" placeholder="Busca por nombre o sector…"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none mb-2">

                    {{-- Resultados --}}
                    <div class="border border-gray-100 rounded-xl divide-y divide-gray-50 max-h-56 overflow-y-auto">
                        <template x-for="p in filtrados.slice(0, 50)" :key="p.id">
                            <button type="button" @click="agregar(p.id)"
                                    class="w-full text-left px-4 py-2.5 hover:bg-gray-50 flex items-center justify-between gap-2 text-sm transition">
                                <span>
                                    <span class="font-medium text-gray-800" x-text="p.title"></span>
                                    <span class="text-gray-400" x-text="p.sector ? ' · ' + p.sector : ''"></span>
                                </span>
                                <span class="text-[10px] text-gray-400 shrink-0" x-text="p.categoria ? (p.emoji ?? '') + ' ' + p.categoria : ''"></span>
                            </button>
                        </template>
                        <p x-show="!filtrados.length" class="px-4 py-3 text-xs text-gray-300 italic">Sin resultados.</p>
                    </div>
                    <p x-show="filtrados.length > 50" class="text-[11px] text-gray-400 mt-1">Mostrando los primeros 50 — refina la búsqueda para ver más.</p>

                    <template x-for="id in seleccionados" :key="'input-' + id">
                        <input type="hidden" name="puntos[]" :value="id">
                    </template>

                    <button type="submit"
                            class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                        Guardar
                    </button>
                </form>
            </div>

            {{-- Correos y Telegram de notificaciones --}}
            <form action="{{ route('admin.configuracion.notificaciones') }}" method="POST" class="mt-6">
                @csrf
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="font-bold text-gray-900">Notificaciones de registro y actividad</p>
                    <p class="text-xs text-gray-500 mt-1 mb-4">
                        A quién le llegan los avisos de nuevos registros (cliente, artista, operador), negocios
                        pendientes de aprobación, solicitudes de activación de perfil, mensajes de contacto y
                        experiencias propuestas.
                    </p>

                    <label for="notificaciones_emails" class="text-sm font-semibold text-gray-700 block mb-1">
                        Correos (separados por coma)
                    </label>
                    <textarea id="notificaciones_emails" name="notificaciones_emails" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none"
                    >{{ old('notificaciones_emails', $notificacionesEmails) }}</textarea>
                    @error('notificaciones_emails') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                    <label for="notificaciones_telegram_chat_id" class="text-sm font-semibold text-gray-700 block mt-4 mb-1">
                        Chat ID de Telegram
                    </label>
                    <input type="text" id="notificaciones_telegram_chat_id" name="notificaciones_telegram_chat_id"
                           value="{{ old('notificaciones_telegram_chat_id', $notificacionesTelegramChatId) }}"
                           placeholder="Ej: 8800864722"
                           class="w-full sm:w-64 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                    <p class="text-xs text-gray-400 mt-1">
                        El chat (o grupo) de Telegram al que llegan los mismos avisos. Déjalo vacío para usar el
                        configurado por defecto en el servidor.
                    </p>
                    @error('notificaciones_telegram_chat_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                    Guardar
                </button>
            </form>

            {{-- Credenciales de pago Flow --}}
            <form action="{{ route('admin.configuracion.flow') }}" method="POST" class="mt-6"
                  x-data="{ modo: '{{ old('flow_modo', $flowModo) }}' }">
                @csrf
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <p class="font-bold text-gray-900">Credenciales de pago Flow</p>
                    <p class="text-xs text-gray-500 mt-1 mb-4">
                        Modo activo y llaves de API/Secret para Sandbox (pruebas) y Producción (pagos reales).
                        Deja una llave Secret en blanco para no modificarla.
                    </p>

                    <label class="text-sm font-semibold text-gray-700 block mb-1">Modo activo</label>
                    <div class="flex gap-2 mb-5">
                        <label class="flex-1 flex items-center gap-2 border border-gray-200 rounded-xl px-4 py-2.5 cursor-pointer"
                               :class="modo === 'sandbox' ? 'border-[#fc5648] bg-[#fff0ef]' : ''">
                            <input type="radio" name="flow_modo" value="sandbox" x-model="modo"
                                   class="text-[#fc5648] focus:ring-[#fc5648]">
                            <span class="text-sm font-medium">Sandbox (pruebas)</span>
                        </label>
                        <label class="flex-1 flex items-center gap-2 border border-gray-200 rounded-xl px-4 py-2.5 cursor-pointer"
                               :class="modo === 'produccion' ? 'border-[#fc5648] bg-[#fff0ef]' : ''">
                            <input type="radio" name="flow_modo" value="produccion" x-model="modo"
                                   class="text-[#fc5648] focus:ring-[#fc5648]">
                            <span class="text-sm font-medium">Producción (real)</span>
                        </label>
                    </div>
                    @error('flow_modo') <p class="text-xs text-red-500 -mt-3 mb-4">{{ $message }}</p> @enderror

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Sandbox</p>
                            <label for="flow_sandbox_api_key" class="text-sm font-semibold text-gray-700 block mb-1">API Key</label>
                            <input type="text" id="flow_sandbox_api_key" name="flow_sandbox_api_key"
                                   value="{{ old('flow_sandbox_api_key', $flowSandboxApiKey) }}"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                            @error('flow_sandbox_api_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                            <label for="flow_sandbox_secret_key" class="text-sm font-semibold text-gray-700 block mt-3 mb-1">Secret Key</label>
                            <input type="password" id="flow_sandbox_secret_key" name="flow_sandbox_secret_key"
                                   placeholder="{{ $flowSandboxSecretConfigurado ? '•••••••••••••• (configurada — deja en blanco para no cambiarla)' : 'No configurada' }}"
                                   autocomplete="new-password"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                            @error('flow_sandbox_secret_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Producción</p>
                            <label for="flow_produccion_api_key" class="text-sm font-semibold text-gray-700 block mb-1">API Key</label>
                            <input type="text" id="flow_produccion_api_key" name="flow_produccion_api_key"
                                   value="{{ old('flow_produccion_api_key', $flowProduccionApiKey) }}"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                            @error('flow_produccion_api_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                            <label for="flow_produccion_secret_key" class="text-sm font-semibold text-gray-700 block mt-3 mb-1">Secret Key</label>
                            <input type="password" id="flow_produccion_secret_key" name="flow_produccion_secret_key"
                                   placeholder="{{ $flowProduccionSecretConfigurado ? '•••••••••••••• (configurada — deja en blanco para no cambiarla)' : 'No configurada' }}"
                                   autocomplete="new-password"
                                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                            @error('flow_produccion_secret_key') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <button type="submit"
                        class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                    Guardar
                </button>
            </form>

            {{-- Ascensores fuera de servicio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                <p class="font-bold text-gray-900">Ascensores fuera de servicio</p>
                <p class="text-xs text-gray-500 mt-1 mb-4">
                    Los ascensores de Valparaíso a veces cierran por mantención o fallas. Marca los que
                    están fuera de servicio para que se muestre un aviso en su ficha y en la imagen de
                    portada que ve el turista.
                </p>

                <form action="{{ route('admin.configuracion.ascensores') }}" method="POST">
                    @csrf

                    <div class="divide-y divide-gray-50 border border-gray-100 rounded-xl">
                        @forelse($ascensores as $ascensor)
                        <div class="p-4" x-data="{ fuera: {{ $ascensor->fuera_de_servicio ? 'true' : 'false' }} }">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="fuera_de_servicio[{{ $ascensor->id }}]" value="1"
                                       x-model="fuera"
                                       class="mt-1 rounded border-gray-300 text-[#fc5648] focus:ring-[#fc5648]">
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm">{{ $ascensor->title }}</p>
                                    @if($ascensor->sector)
                                    <p class="text-xs text-gray-400">{{ $ascensor->sector }}</p>
                                    @endif
                                </div>
                            </label>
                            <div x-show="fuera" class="mt-3 ml-7">
                                <textarea name="motivo[{{ $ascensor->id }}]" rows="2"
                                          placeholder="Describe el problema (ej: en mantención hasta nuevo aviso)"
                                          class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ $ascensor->fuera_de_servicio_motivo }}</textarea>
                            </div>
                        </div>
                        @empty
                        <p class="px-4 py-3 text-xs text-gray-300 italic">No hay ascensores registrados.</p>
                        @endforelse
                    </div>

                    <button type="submit"
                            class="mt-5 bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                        Guardar
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Orden de las secciones del home: arrastrar filas reordena el DOM; al enviar,
        // se arma el CSV final leyendo data-clave en el orden resultante.
        (function() {
            var grid = document.getElementById('orden-secciones-grid');
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

            grid.closest('form').addEventListener('submit', function() {
                var orden = Array.prototype.slice.call(grid.children)
                    .map(function(tile) { return tile.dataset.clave; });
                document.getElementById('orden-secciones-input').value = orden.join(',');
            });
        })();
    </script>
</x-admin-layout>
