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

        </div>
    </div>
</x-admin-layout>
