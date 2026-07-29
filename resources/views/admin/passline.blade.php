<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Importar desde Passline</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4"
             x-data="passlineImport()">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <p class="text-sm text-gray-500 mb-6">
                    Trae los próximos eventos de Valparaíso desde Passline y los guarda como Panoramas.
                    La consulta se hace desde tu browser, sin configuración adicional.
                </p>

                <button @click="importar()"
                        :disabled="estado !== 'idle' && estado !== 'listo'"
                        class="w-full bg-[#fc5648] text-white font-bold py-3 px-6 rounded-xl
                               hover:bg-[#d94439] transition disabled:opacity-50 disabled:cursor-not-allowed
                               flex items-center justify-center gap-2">
                    <svg x-show="estado === 'consultando' || estado === 'guardando'"
                         class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-show="estado === 'idle' || estado === 'listo'">Importar eventos</span>
                    <span x-show="estado === 'consultando'">Consultando Passline…</span>
                    <span x-show="estado === 'guardando'" x-text="progreso"></span>
                    <span x-show="estado === 'error'">Reintentar</span>
                </button>

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
                                <span class="text-sm font-bold text-gray-500">Omitidos (ya pasaron)</span>
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

    function passlineImport() {
        return {
            estado:   'idle',
            mensaje:  '',
            progreso: '',
            resumen:  null,
            guardado: null,

            async importar() {
                this.estado   = 'consultando';
                this.mensaje  = '';
                this.resumen  = null;

                let eventos;
                try {
                    const res = await fetch('https://api.passline.com/v1/event/GetBillboardByFilters', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({
                            country: 'chile', region: null, commune: '', communeNum: null,
                            type: 0, start_date: '', end_date: '', text: 'valparaiso',
                            tag_id: null, tag: null, upper_category_id: null,
                            limit: '0,300', offset: '1',
                        }),
                    });
                    if (!res.ok) throw new Error(`Passline respondió ${res.status}`);
                    eventos = await res.json();
                } catch (e) {
                    this.estado  = 'error';
                    this.mensaje = 'No se pudo consultar Passline: ' + e.message;
                    return;
                }

                this.estado   = 'guardando';
                this.progreso = `Guardando ${eventos.length} eventos…`;

                try {
                    const res = await fetch('{{ route('admin.passline.importar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ eventos }),
                    });
                    const data   = await res.json();
                    this.estado  = 'listo';
                    this.resumen = data;
                    this.mensaje = 'Importación completada.';
                } catch (e) {
                    this.estado  = 'error';
                    this.mensaje = 'Error al guardar: ' + e.message;
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
