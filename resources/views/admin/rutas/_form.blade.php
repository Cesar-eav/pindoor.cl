@php $esEdicion = isset($ruta) && $ruta !== null; @endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      id="ruta-form" class="space-y-6">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    {{-- Publicar + Guardar --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 flex items-center justify-between gap-4">
        <label class="flex items-center gap-3 cursor-pointer select-none">
            <div class="relative">
                <input type="checkbox" name="publicado" id="publicado" value="1" class="sr-only peer"
                       {{ old('publicado', $ruta?->publicado) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 rounded-full peer
                            peer-checked:bg-[#fc5648] transition-colors duration-200"></div>
                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow
                            peer-checked:translate-x-5 transition-transform duration-200"></div>
            </div>
            <span class="text-sm font-bold text-gray-700" id="publicado-label">
                {{ old('publicado', $ruta?->publicado) ? 'Publicado' : 'Borrador' }}
            </span>
        </label>
        <div class="flex items-center gap-3">
            @if($esEdicion)
            <a href="{{ route('admin.rutas.preview', $ruta) }}" target="_blank"
               class="inline-flex items-center gap-2 border border-gray-200 text-gray-600 px-5 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Vista previa
            </a>
            @endif
            <span id="guardar-spinner" class="hidden items-center gap-2 text-sm text-gray-400">
                <svg class="animate-spin w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Guardando…
            </span>
            <button type="submit" name="accion" value="seguir" id="guardar-seguir-btn"
                    class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-50 transition">
                {{ $esEdicion ? 'Guardar y seguir editando' : 'Crear y seguir editando' }}
            </button>
            <button type="submit" name="accion" value="guardar" id="guardar-btn"
                    class="bg-gray-900 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-black transition">
                {{ $esEdicion ? 'Guardar cambios' : 'Crear ruta' }}
            </button>
        </div>
    </div>

    @if($errors->any())
    <div class="w-[90vw] mx-auto bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Tabs idioma ─────────────────────────────────────────────────── --}}
    <div class="w-[90vw] mx-auto flex items-center gap-2 flex-wrap">
        <button type="button" id="tab-es" onclick="setLang('es')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-[#fc5648] bg-[#fc5648] text-white transition">
            🇪🇸 Español
        </button>
        <button type="button" id="tab-en" onclick="setLang('en')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-gray-400 transition">
            🇬🇧 English
        </button>
        <button type="button" id="tab-fr" onclick="setLang('fr')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-gray-400 transition">
            🇫🇷 Français
        </button>
        <button type="button" id="btn-autotraducir-en" onclick="autoTraducir('en')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition ml-2">
            ✨ Auto-traducir ES→EN
        </button>
        <button type="button" id="btn-autotraducir-fr" onclick="autoTraducir('fr')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition">
            ✨ Auto-traducir ES→FR
        </button>
        <span id="traducir-estado" class="text-xs text-gray-400 hidden"></span>
        <span class="text-xs text-gray-400 ml-auto">Slug e imagen de portada son compartidos</span>
    </div>

    {{-- ── Título + Slug · Descripción · Portada ─────────────────────── --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- Título + Slug --}}
            <div class="space-y-4">
                <div data-lang-field="es">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Título * <span class="text-[#fc5648]">ES</span></label>
                    <input id="titulo-es" type="text" name="titulo_es" required
                           value="{{ old('titulo_es', $ruta?->getTranslation('titulo','es',false)) }}"
                           placeholder="Ej: Ruta del arte porteño"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold focus:ring-2 focus:ring-[#fc5648] outline-none">
                </div>
                <div data-lang-field="en" style="display:none">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Title <span class="text-blue-500">EN</span></label>
                    <input id="titulo-en" type="text" name="titulo_en"
                           value="{{ old('titulo_en', $ruta?->getTranslation('titulo','en',false)) }}"
                           placeholder="Eg: The porteño art route"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
                <div data-lang-field="fr" style="display:none">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Titre <span class="text-indigo-500">FR</span></label>
                    <input id="titulo-fr" type="text" name="titulo_fr"
                           value="{{ old('titulo_fr', $ruta?->getTranslation('titulo','fr',false)) }}"
                           placeholder="Ex : La route de l'art porteño"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                        Slug (URL)
                        <span class="normal-case font-normal text-gray-400 ml-1">— automático desde el título ES</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-400 shrink-0">/rutas/</span>
                        <input id="slug" type="text" name="slug"
                               value="{{ old('slug', $ruta?->slug) }}"
                               placeholder="ruta-del-arte-porteno"
                               class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                    </div>
                </div>
            </div>

            {{-- Descripción --}}
            <div>
                <div data-lang-field="es">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                        Descripción <span class="text-[#fc5648]">ES</span>
                    </label>
                    <textarea id="descripcion-es" name="descripcion_es" rows="12" maxlength="2000"
                              placeholder="Presenta la ruta: qué se recorre y por qué vale la pena..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ old('descripcion_es', $ruta?->getTranslation('descripcion','es',false)) }}</textarea>
                </div>
                <div data-lang-field="en" style="display:none">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                        Description <span class="text-blue-500">EN</span>
                    </label>
                    <textarea id="descripcion-en" name="descripcion_en" rows="12" maxlength="2000"
                              placeholder="Introduce the route: what it covers and why it's worth it..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-400 outline-none resize-none">{{ old('descripcion_en', $ruta?->getTranslation('descripcion','en',false)) }}</textarea>
                </div>
                <div data-lang-field="fr" style="display:none">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                        Description <span class="text-indigo-500">FR</span>
                    </label>
                    <textarea id="descripcion-fr" name="descripcion_fr" rows="12" maxlength="2000"
                              placeholder="Présentez l'itinéraire : ce qu'il traverse et pourquoi ça vaut le détour..."
                              class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none">{{ old('descripcion_fr', $ruta?->getTranslation('descripcion','fr',false)) }}</textarea>
                </div>
            </div>

            {{-- Duración --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Duración</label>
                <input type="text" name="duracion" maxlength="100" placeholder="Ej: 3 horas aprox."
                       value="{{ old('duracion', $ruta?->duracion) }}"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
            </div>

            {{-- Imagen portada --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Imagen de portada</label>

                @if($esEdicion && $ruta->imagen_portada_url)
                <div class="mb-3" id="portada-preview-actual">
                    <img src="{{ $ruta->imagen_portada_url }}" alt="Portada actual"
                         class="w-full rounded-xl object-cover aspect-video border border-gray-100">
                    <p class="text-xs text-gray-400 mt-1">Portada actual — sube una nueva para reemplazarla</p>
                </div>
                @endif

                <div id="portada-preview-nueva" class="mb-3 hidden">
                    <img id="portada-img-nueva" src="" alt="Nueva portada"
                         class="w-full rounded-xl object-cover aspect-video border border-gray-100">
                </div>

                <input type="file" name="imagen_portada" id="imagen_portada" accept="image/*"
                       class="block w-full text-sm text-gray-500
                              file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                              file:text-sm file:font-bold file:bg-[#fff0ef] file:text-[#fc5648]
                              hover:file:bg-[#ffe0dd] cursor-pointer">
            </div>

        </div>
    </div>

    {{-- Puntos de la ruta (con orden) --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
            Puntos de la ruta *
        </label>
        <p class="text-xs text-gray-400 mb-3">
            Selecciona los lugares y ordénalos con las flechas según el recorrido.
        </p>

        @php
            $puntosSeleccionados = $ruta?->puntos->pluck('id')->all() ?? [];
            $puntosData = $puntos->map(fn ($p) => [
                'id'        => $p->id,
                'title'     => (string) $p->title,
                'sector'    => $p->sector,
                'categoria' => $p->categoria?->nombre,
                'emoji'     => $p->categoria?->icono,
                'imagen'    => $p->imagenPrincipal ? asset('storage/' . $p->imagenPrincipal->ruta) : null,
            ])->values();
            $categoriasDisponibles = $puntosData->pluck('categoria')->filter()->unique()->sort()->values();
        @endphp

        <div x-data="{
                todos: {{ Illuminate\Support\Js::from($puntosData) }},
                categorias: {{ Illuminate\Support\Js::from($categoriasDisponibles) }},
                seleccionados: {{ Illuminate\Support\Js::from($puntosSeleccionados) }},
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
                subir(index) {
                    if (index <= 0) return;
                    const arr = this.seleccionados;
                    [arr[index - 1], arr[index]] = [arr[index], arr[index - 1]];
                },
                bajar(index) {
                    const arr = this.seleccionados;
                    if (index >= arr.length - 1) return;
                    [arr[index], arr[index + 1]] = [arr[index + 1], arr[index]];
                },
             }">

            {{-- Seleccionados, en orden --}}
            <div class="space-y-2 mb-4" x-show="seleccionados.length">
                <template x-for="(id, index) in seleccionados" :key="id">
                    <div class="flex items-center gap-3 bg-[#fff0ef] border border-[#fc5648]/20 rounded-xl p-2">
                        <span class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#fc5648] text-white text-xs font-black" x-text="index + 1"></span>
                        <div class="shrink-0 w-11 h-11 rounded-lg overflow-hidden bg-gray-100 border border-white">
                            <template x-if="info(id).imagen">
                                <img :src="info(id).imagen" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!info(id).imagen">
                                <div class="w-full h-full flex items-center justify-center text-lg text-gray-300">📍</div>
                            </template>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-gray-800 truncate" x-text="info(id).title"></p>
                            <p class="text-[11px] text-gray-400 truncate" x-text="info(id).sector ?? ''"></p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="subir(index)" :disabled="index === 0"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-500 hover:bg-white disabled:opacity-30 disabled:hover:bg-transparent transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                            </button>
                            <button type="button" @click="bajar(index)" :disabled="index === seleccionados.length - 1"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-500 hover:bg-white disabled:opacity-30 disabled:hover:bg-transparent transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <button type="button" @click="quitar(id)"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-[#fc5648] hover:bg-white transition">×</button>
                        </div>
                    </div>
                </template>
            </div>
            <p x-show="!seleccionados.length" class="text-xs text-gray-300 italic mb-3">Ningún punto seleccionado todavía.</p>

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
                            class="w-full text-left px-3 py-2 hover:bg-gray-50 flex items-center gap-3 text-sm transition">
                        <div class="shrink-0 w-9 h-9 rounded-lg overflow-hidden bg-gray-100">
                            <template x-if="p.imagen">
                                <img :src="p.imagen" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!p.imagen">
                                <div class="w-full h-full flex items-center justify-center text-sm text-gray-300">📍</div>
                            </template>
                        </div>
                        <span class="flex-1 min-w-0">
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
        </div>
    </div>

    {{-- Operadores turísticos --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
            Operadores que ofrecen esta ruta
        </label>
        <p class="text-xs text-gray-400 mb-3">
            La ruta aparecerá en el perfil público de cada operador seleccionado.
        </p>

        @php $operadoresSeleccionados = $ruta?->operadores->pluck('id')->all() ?? []; @endphp

        @if($operadores->isEmpty())
        <p class="text-xs text-gray-300 italic">No hay operadores activos todavía.</p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($operadores as $operador)
            <label class="flex items-center gap-2.5 px-4 py-2.5 border border-gray-200 rounded-xl text-sm cursor-pointer hover:bg-gray-50 has-[:checked]:border-[#fc5648] has-[:checked]:bg-[#fff0ef] transition">
                <input type="checkbox" name="operadores[]" value="{{ $operador->id }}"
                       {{ in_array($operador->id, old('operadores', $operadoresSeleccionados)) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-[#fc5648] focus:ring-[#fc5648]">
                <span class="font-medium text-gray-700 truncate">{{ $operador->nombre }}</span>
            </label>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Guías relacionadas --}}
    <div class="w-[90vw] mx-auto bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
            Guías relacionadas
        </label>
        <p class="text-xs text-gray-400 mb-3">
            Vincula guías del blog que hablan de este recorrido. Aparecerán en la ficha pública de la ruta, y esta ruta aparecerá en la ficha de cada guía.
        </p>

        @php $guiasSeleccionadas = $ruta?->guias->pluck('id')->all() ?? []; @endphp

        @if($guias->isEmpty())
        <p class="text-xs text-gray-300 italic">No hay guías publicadas todavía.</p>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
            @foreach($guias as $guia)
            <label class="flex items-center gap-2.5 px-4 py-2.5 border border-gray-200 rounded-xl text-sm cursor-pointer hover:bg-gray-50 has-[:checked]:border-[#fc5648] has-[:checked]:bg-[#fff0ef] transition">
                <input type="checkbox" name="guias[]" value="{{ $guia->id }}"
                       {{ in_array($guia->id, old('guias', $guiasSeleccionadas)) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-[#fc5648] focus:ring-[#fc5648]">
                <span class="font-medium text-gray-700 truncate">{{ $guia->titulo }}</span>
            </label>
            @endforeach
        </div>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById('ruta-form').addEventListener('submit', function (e) {
            const submitter = e.submitter;
            if (submitter && submitter.name === 'accion') {
                let accionInput = document.getElementById('accion-input');
                if (!accionInput) {
                    accionInput = document.createElement('input');
                    accionInput.type = 'hidden';
                    accionInput.name = 'accion';
                    accionInput.id = 'accion-input';
                    this.appendChild(accionInput);
                }
                accionInput.value = submitter.value;
            }

            const btn       = document.getElementById('guardar-btn');
            const btnSeguir = document.getElementById('guardar-seguir-btn');
            const spinner   = document.getElementById('guardar-spinner');
            [btn, btnSeguir].forEach(b => {
                b.disabled = true;
                b.classList.add('opacity-50', 'cursor-not-allowed');
            });
            spinner.style.display = 'flex';
        });

        // ── Cambio de idioma ────────────────────────────────────────────
        let currentLang = 'es';

        const TAB_ESTILOS = {
            es: 'border-[#fc5648] bg-[#fc5648] text-white',
            en: 'border-blue-500 bg-blue-500 text-white',
            fr: 'border-indigo-500 bg-indigo-500 text-white',
        };
        const TAB_INACTIVO = 'border-gray-200 bg-white text-gray-500 hover:border-gray-400';

        window.setLang = function(lang) {
            document.querySelectorAll('[data-lang-field]').forEach(el => {
                el.style.display = el.dataset.langField === lang ? '' : 'none';
            });

            Object.keys(TAB_ESTILOS).forEach(l => {
                const tab = document.getElementById('tab-' + l);
                if (!tab) return;
                tab.className = 'flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border transition '
                    + (l === lang ? TAB_ESTILOS[l] : TAB_INACTIVO);
            });

            currentLang = lang;
        };

        // ── Auto-traducir ES → EN / FR ───────────────────────────────────
        window.autoTraducir = async function(destino) {
            const btn    = document.getElementById('btn-autotraducir-' + destino);
            const estado = document.getElementById('traducir-estado');

            const tituloEs      = document.getElementById('titulo-es').value.trim();
            const descripcionEs = document.getElementById('descripcion-es').value.trim();

            if (!tituloEs && !descripcionEs) {
                alert('Escribe primero el contenido en Español.');
                return;
            }

            btn.disabled  = true;
            btn.className = btn.className.replace('text-gray-500', 'text-blue-400');
            estado.classList.remove('hidden');

            async function traducirTexto(txt) {
                if (!txt) return '';
                // Dividir en oraciones de ~450 chars
                const oraciones = txt.match(/[^.!?]+[.!?]+/g) || [txt];
                const chunks = [];
                let cur = '';
                for (const o of oraciones) {
                    if ((cur + o).length > 450) { if (cur) chunks.push(cur); cur = o; }
                    else cur += (cur ? ' ' : '') + o;
                }
                if (cur) chunks.push(cur);

                let resultado = '';
                for (let i = 0; i < chunks.length; i++) {
                    estado.textContent = `Traduciendo… ${i + 1}/${chunks.length}`;
                    const r = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(chunks[i])}&langpair=es|${destino}&de=cesar.eav@gmail.com`);
                    const d = await r.json();
                    resultado += (d.responseData?.translatedText || chunks[i]) + ' ';
                    await new Promise(r => setTimeout(r, 600));
                }
                return resultado.trim();
            }

            try {
                if (tituloEs) {
                    estado.textContent = 'Traduciendo título…';
                    document.getElementById('titulo-' + destino).value = await traducirTexto(tituloEs);
                }
                if (descripcionEs) {
                    estado.textContent = 'Traduciendo descripción…';
                    document.getElementById('descripcion-' + destino).value = await traducirTexto(descripcionEs);
                }

                setLang(destino);
                estado.textContent = '✓ Traducción completada';
                setTimeout(() => { estado.classList.add('hidden'); estado.textContent = ''; }, 3000);
            } catch(e) {
                estado.textContent = 'Error al traducir. Inténtalo de nuevo.';
            } finally {
                btn.disabled  = false;
                btn.className = btn.className.replace('text-blue-400', 'text-gray-500');
            }
        };

        // ── Slug automático desde título ────────────────────────────────
        const tituloInput = document.getElementById('titulo-es');
        const slugInput   = document.getElementById('slug');
        let slugEditado   = slugInput.value.trim() !== '';

        slugInput.addEventListener('input', () => { slugEditado = true; });

        tituloInput.addEventListener('input', () => {
            if (slugEditado) return;
            slugInput.value = tituloInput.value
                .toLowerCase()
                .normalize('NFD').replace(/[̀-ͯ]/g, '')
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');
        });

        // ── Preview portada ─────────────────────────────────────────────
        document.getElementById('imagen_portada').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('portada-img-nueva').src = e.target.result;
                document.getElementById('portada-preview-nueva').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });

        // ── Toggle label Publicado/Borrador ─────────────────────────────
        const toggle = document.getElementById('publicado');
        const label  = document.getElementById('publicado-label');
        toggle.addEventListener('change', () => {
            label.textContent = toggle.checked ? 'Publicado' : 'Borrador';
        });
    });
    </script>

</form>
