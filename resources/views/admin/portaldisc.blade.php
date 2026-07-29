<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Importar desde Portaldisc</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4"
             x-data="portaldiscImport()">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <p class="text-sm text-gray-500 mb-6">
                    Trae los próximos eventos de Valparaíso desde Portaldisc y los guarda como Panoramas.
                    La consulta e importación se hacen en el servidor, puede tardar un minuto.
                </p>

                <button @click="importar()"
                        :disabled="estado === 'importando'"
                        class="w-full bg-[#fc5648] text-white font-bold py-3 px-6 rounded-xl
                               hover:bg-[#d94439] transition disabled:opacity-50 disabled:cursor-not-allowed
                               flex items-center justify-center gap-2">
                    <svg x-show="estado === 'importando'"
                         class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-show="estado === 'idle' || estado === 'listo'">Importar eventos</span>
                    <span x-show="estado === 'importando'">Importando desde Portaldisc… (<span x-text="segundos"></span>s)</span>
                    <span x-show="estado === 'error'">Reintentar</span>
                </button>

                <p x-show="estado === 'importando'" x-cloak class="mt-3 text-xs text-gray-400 text-center">
                    Puede tardar 1-2 minutos: entra al detalle de cada evento para sacar precio y descripción. No cierres esta pestaña.
                </p>

                <div x-show="mensaje" x-cloak class="mt-4 text-sm rounded-xl px-4 py-3"
                     :class="estado === 'error' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700'">
                    <p x-text="mensaje"></p>
                </div>
            </div>

            {{-- Resumen numérico --}}
            <div x-show="resumen" x-cloak class="grid grid-cols-3 gap-3 text-center">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-4">
                    <div class="text-3xl font-extrabold text-[#fc5648]" x-text="resumen?.creados?.length"></div>
                    <div class="text-xs text-gray-500 mt-1">Creados</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-4">
                    <div class="text-3xl font-extrabold text-gray-700" x-text="resumen?.actualizados?.length"></div>
                    <div class="text-xs text-gray-500 mt-1">Actualizados</div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-4">
                    <div class="text-3xl font-extrabold text-gray-300" x-text="resumen?.omitidos?.length"></div>
                    <div class="text-xs text-gray-500 mt-1">Omitidos</div>
                </div>
            </div>

            {{-- Listados --}}
            <template x-if="resumen">
                <div class="space-y-4">

                    <template x-if="resumen.creados.length > 0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#fc5648] inline-block"></span>
                                <span class="text-sm font-bold text-gray-700">Creados</span>
                            </div>
                            <ul class="divide-y divide-gray-50">
                                <template x-for="ev in resumen.creados" :key="ev.nombre">
                                    <li class="px-5 py-3 flex items-center justify-between gap-4">
                                        <span class="text-sm text-gray-800 font-medium flex-1" x-text="ev.nombre"></span>
                                        <span class="text-xs text-gray-400 whitespace-nowrap" x-text="ev.fecha + (ev.hora ? ' ' + ev.hora : '')"></span>
                                        <span x-show="guardado === ev.slug" x-cloak
                                              class="text-xs text-green-600 font-medium whitespace-nowrap">Guardado</span>
                                        <select x-model="ev.categoria"
                                                @change="actualizarCategoria(ev)"
                                                class="text-xs border border-gray-200 rounded-lg px-2 py-1 text-gray-600 focus:ring-1 focus:ring-[#fc5648] outline-none">
                                            <template x-for="[key, label] in Object.entries(CATEGORIAS)" :key="key">
                                                <option :value="key" :selected="ev.categoria === key" x-text="label"></option>
                                            </template>
                                        </select>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <template x-if="resumen.actualizados.length > 0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                                <span class="text-sm font-bold text-gray-700">Actualizados</span>
                            </div>
                            <ul class="divide-y divide-gray-50">
                                <template x-for="ev in resumen.actualizados" :key="ev.nombre">
                                    <li class="px-5 py-3 flex items-center justify-between gap-4">
                                        <span class="text-sm text-gray-800 font-medium flex-1" x-text="ev.nombre"></span>
                                        <span class="text-xs text-gray-400 whitespace-nowrap" x-text="ev.fecha + (ev.hora ? ' ' + ev.hora : '')"></span>
                                        <span x-show="guardado === ev.slug" x-cloak
                                              class="text-xs text-green-600 font-medium whitespace-nowrap">Guardado</span>
                                        <select x-model="ev.categoria"
                                                @change="actualizarCategoria(ev)"
                                                class="text-xs border border-gray-200 rounded-lg px-2 py-1 text-gray-600 focus:ring-1 focus:ring-[#fc5648] outline-none">
                                            <template x-for="[key, label] in Object.entries(CATEGORIAS)" :key="key">
                                                <option :value="key" :selected="ev.categoria === key" x-text="label"></option>
                                            </template>
                                        </select>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>

                    <template x-if="resumen.omitidos.length > 0">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-gray-200 inline-block"></span>
                                <span class="text-sm font-bold text-gray-500">Omitidos (ya pasaron o sin fecha)</span>
                            </div>
                            <ul class="divide-y divide-gray-50">
                                <template x-for="ev in resumen.omitidos" :key="ev.nombre">
                                    <li class="px-5 py-3 flex items-start justify-between gap-4">
                                        <span class="text-sm text-gray-400" x-text="ev.nombre"></span>
                                        <span class="text-xs text-gray-300 whitespace-nowrap" x-text="ev.fecha"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>

                </div>
            </template>

        </div>
    </div>

    <script>
    const CATEGORIAS = @json(array_map(fn($v) => $v['label'], \App\Models\CategoriaEvento::catalogo()));
    const CSRF = '{{ csrf_token() }}';

    const TIMEOUT_MS = 5 * 60 * 1000; // 5 min: margen sobre lo que tarda el scraping (~1-2 min típico)

    function portaldiscImport() {
        return {
            estado:    'idle',
            mensaje:   '',
            resumen:   null,
            guardado:  null,
            segundos:  0,
            _timer:    null,

            async importar() {
                this.estado   = 'importando';
                this.mensaje  = '';
                this.resumen  = null;
                this.segundos = 0;

                const inicio = Date.now();
                this._timer  = setInterval(() => {
                    this.segundos = Math.round((Date.now() - inicio) / 1000);
                }, 1000);

                console.log('[Portaldisc] POST /admin/portaldisc — inicio', new Date().toISOString());

                const controller = new AbortController();
                const timeoutId  = setTimeout(() => controller.abort(), TIMEOUT_MS);

                try {
                    const res = await fetch('{{ route('admin.portaldisc.importar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                        },
                        signal: controller.signal,
                    });
                    console.log('[Portaldisc] respuesta recibida', res.status, `+${Math.round((Date.now() - inicio) / 1000)}s`);
                    if (!res.ok) throw new Error(`El servidor respondió ${res.status}`);

                    const data = await res.json();
                    console.log('[Portaldisc] JSON parseado', {
                        creados: data.creados?.length,
                        actualizados: data.actualizados?.length,
                        omitidos: data.omitidos?.length,
                    });

                    this.estado  = 'listo';
                    this.resumen = data;
                    this.mensaje = 'Importación completada.';
                } catch (e) {
                    console.error('[Portaldisc] error en el fetch', e);
                    this.estado = 'error';
                    this.mensaje = e.name === 'AbortError'
                        ? `No llegó respuesta del servidor tras ${Math.round(TIMEOUT_MS / 60000)} min. Es probable que la importación haya terminado igual en el servidor — revisa el listado de panoramas antes de reintentar.`
                        : 'Error al importar: ' + e.message;
                } finally {
                    clearTimeout(timeoutId);
                    clearInterval(this._timer);
                    console.log('[Portaldisc] fin', `+${Math.round((Date.now() - inicio) / 1000)}s`, 'estado:', this.estado);
                }
            },

            async actualizarCategoria(ev) {
                try {
                    const res = await fetch(`/admin/panoramas/${ev.slug}/categoria`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                        },
                        body: JSON.stringify({ categoria: ev.categoria }),
                    });
                    if (res.ok) {
                        this.guardado = ev.slug;
                        setTimeout(() => { this.guardado = null; }, 2000);
                    }
                } catch (e) {
                    console.error('Error al actualizar categoría:', e);
                }
            }
        }
    }
    </script>
</x-app-layout>
