<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Puntos Públicos') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ view: 'listado' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex mb-6 bg-gray-200 p-1 rounded-xl w-fit">
                <button
                    @click="view = 'crear'; $nextTick(() => window.dispatchEvent(new Event('mapa-visible')))"
                    :class="view === 'crear' ? 'bg-white shadow-sm text-pindoor-accent' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-2 rounded-lg font-bold text-sm transition-all">
                    + Crear Nuevo
                </button>
                <button
                    @click="view = 'listado'"
                    :class="view === 'listado' ? 'bg-white shadow-sm text-pindoor-accent' : 'text-gray-500 hover:text-gray-700'"
                    class="px-6 py-2 rounded-lg font-bold text-sm transition-all">
                    Ver Listado
                </button>
            </div>

            {{-- FORMULARIO CREAR --}}
            <div x-show="view === 'crear'" x-cloak x-transition>
                <div class="bg-slate-100 p-8 rounded-2xl shadow-sm border border-slate-200 max-w-4xl mx-auto">
                    <h3 class="text-lg font-bold mb-1">Información del Nuevo Punto Público</h3>
                    <p class="text-xs text-gray-400 mb-6"><span class="text-red-500">*</span> Campo obligatorio</p>

                    <form action="{{ route('admin.puntos.store') }}" id="main-form" onsubmit="return false;">
                        @csrf

                        {{-- Tabs idioma --}}
                        <div class="flex items-center gap-2 flex-wrap mb-6">
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
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition ml-2">
                                ✨ ES→EN
                            </button>
                            <button type="button" id="btn-autotraducir-fr" onclick="autoTraducir('fr')"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition">
                                ✨ ES→FR
                            </button>
                            <span id="traducir-estado" class="text-xs text-gray-400 hidden"></span>
                        </div>

                        {{-- Fila 1: Nombre + Categoría --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <div data-lang-field="es">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre del Punto <span class="text-[#fc5648]">ES</span> <span class="text-red-500">*</span>
                                    </label>
                                    <x-text-input id="title-es" name="title_es" class="block mt-1 w-full" required />
                                </div>
                                <div data-lang-field="en" style="display:none">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-blue-500">EN</span></label>
                                    <x-text-input id="title-en" name="title_en" class="block mt-1 w-full" />
                                </div>
                                <div data-lang-field="fr" style="display:none">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-indigo-500">FR</span></label>
                                    <x-text-input id="title-fr" name="title_fr" class="block mt-1 w-full" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Categoría <span class="text-red-500">*</span>
                                </label>
                                <select name="categoria_id" required class="block mt-1 w-full bg-white border border-slate-200 text-gray-900 rounded-lg focus:border-pindoor-accent focus:ring-pindoor-accent transition">
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Fila 2: Autor + Tags --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="autor" value="Autor / Artista / Institución" />
                                <x-text-input id="autor" name="autor" class="block mt-1 w-full"  />
                            </div>
                            <div>
                                <x-input-label for="tags" value="Etiquetas (separadas por coma)" />
                                <x-text-input id="tags" name="tags" class="block mt-1 w-full" />
                            </div>
                        </div>

                        {{-- Fila 3: Sector + Dirección --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="sector" value="Sector / Cerro" />
                                @include('admin.partials._sector-select')
                            </div>
                            <div>
                                <x-input-label for="direccion" value="Dirección" />
                                <x-text-input id="direccion" name="direccion" class="block mt-1 w-full" />
                            </div>
                        </div>

                        {{-- Fila 4: Horario + Enlace --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="horario" value="Horario de visita" />
                                <x-text-input id="horario" name="horario" class="block mt-1 w-full" />
                            </div>
                            <div>
                                <x-input-label for="enlace" value="Sitio web o Instagram" />
                                <x-text-input id="enlace" name="enlace" type="url" class="block mt-1 w-full"  />
                            </div>
                        </div>

                        {{-- Video YouTube --}}
                        <div class="mb-6">
                            <x-input-label for="video_url" value="Video de YouTube" />
                            <x-text-input id="video_url" name="video_url" type="url" class="block mt-1 w-full" />
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Reseña o descripción — <span id="editor-lang-label" class="text-[#fc5648]">ES</span> <span class="text-red-500">*</span>
                            </label>
                            <div id="description-editor"
                                 class="bg-white border border-slate-200 rounded-lg"
                                 style="min-height: 180px;"></div>
                            <textarea id="description_es" name="description_es" class="hidden"></textarea>
                            <textarea id="description_en" name="description_en" class="hidden"></textarea>
                            <textarea id="description_fr" name="description_fr" class="hidden"></textarea>
                        </div>

                        {{-- Perfil de búsqueda (admin) --}}
                        <div class="mb-6 p-4 rounded-xl border border-amber-200 bg-amber-50">
                            <label class="block text-sm font-semibold text-amber-900 mb-1">🔍 Palabras clave de búsqueda (uso interno)</label>
                            <p class="text-xs text-amber-700 mb-2">
                                Invisible para los turistas, pero se usa para que este punto aparezca en el buscador.
                                Es el equivalente al "Perfil de búsqueda" que rellena el cliente desde su panel, pero editable solo desde acá
                                — útil para puntos sin dueño real (básicos) o atractivos que administra Pindoor directamente.
                            </p>
                            <textarea name="descripcion_busqueda_admin" rows="4"
                                      class="block w-full border-amber-200 bg-white rounded-lg shadow-sm text-sm focus:ring-amber-400 resize-none"
                                      placeholder="Sinónimos, palabras que la gente buscaría, tipo de lugar, características…">{{ old('descripcion_busqueda_admin') }}</textarea>
                        </div>

                        {{-- Mapa + Galería (Vue) --}}
                        <div id="app" class="mb-6">
                            <x-input-label value="Ubicación en el mapa" />
                            <p class="text-xs text-gray-400 mb-2">Escribe la dirección y el pin se ubica solo. Si no queda exacto, haz clic en el mapa o arrastra el marcador.</p>
                            <selector-mapa
                                geocode-url="{{ route('admin.geocodificar') }}"
                                reverse-geocode-url="{{ route('admin.geocodificar-inverso') }}"
                            ></selector-mapa>
                            <div class="mt-6">
                                <x-input-label value="Fotografías" />
                                <p class="text-xs text-gray-400 mb-2">Sube al menos una foto. Puedes reordenarlas arrastrando.</p>
                                <galeria-subida></galeria-subida>
                            </div>
                        </div>

                        {{-- Perfil básico (sin dueño) --}}
                        <div class="mb-6 p-4 rounded-xl border border-gray-200 bg-gray-50">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="publicar_como_basico" value="1"
                                       class="mt-1 rounded border-gray-300 text-pindoor-accent focus:ring-pindoor-accent">
                                <div>
                                    <span class="text-sm font-semibold text-gray-800">Publicar como perfil básico (sin dueño asignado)</span>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        El negocio aparece en Pindoor con información básica y podrá activar su perfil más adelante para desbloquear galería, menú, eventos y promociones.
                                    </p>
                                </div>
                            </label>
                        </div>

                        {{-- Errores de validación --}}
                        <div id="form-errors" class="hidden mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-5 py-4">
                            <p class="text-sm font-bold mb-1">Por favor completa los campos obligatorios:</p>
                            <ul id="form-errors-list" class="text-sm list-disc list-inside space-y-0.5"></ul>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="button"
                                onclick="validarYEnviar()"
                                class="bg-pindoor-accent text-white px-8 py-3 rounded-2xl font-bold shadow-lg hover:bg-red-600 transition">
                                Publicar Punto Público
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- LISTADO --}}
            <div x-show="view === 'listado'" x-cloak x-transition>

                {{-- Buscador y filtros --}}
                <form method="GET" action="{{ route('admin.puntos.create') }}"
                      class="mb-4 flex flex-col sm:flex-row gap-3 items-start sm:items-center">

                    <div class="relative flex-1 min-w-0">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar por nombre, sector o autor…"
                            class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-pindoor-accent focus:border-transparent transition"
                        />
                    </div>

                    <select
                        name="categoria"
                        onchange="this.form.submit()"
                        class="py-2 pl-3 pr-8 rounded-xl border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-pindoor-accent transition">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="px-5 py-2 bg-pindoor-accent text-white rounded-xl text-sm font-bold hover:bg-red-600 transition whitespace-nowrap">
                        Buscar
                    </button>

                    @if(request()->filled('search') || request()->filled('categoria'))
                        <a href="{{ route('admin.puntos.create') }}"
                           class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-semibold hover:bg-gray-200 transition whitespace-nowrap">
                            Limpiar
                        </a>
                    @endif
                </form>

                @if(request()->filled('search') || request()->filled('categoria'))
                    <p class="text-xs text-gray-400 mb-3">
                        {{ $puntos->total() }} resultado{{ $puntos->total() !== 1 ? 's' : '' }} encontrado{{ $puntos->total() !== 1 ? 's' : '' }}
                    </p>
                @endif

                <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-4">Id</th>
                                <th class="px-6 py-4">Punto</th>
                                <th class="px-6 py-4">Sector</th>
                                <th class="px-6 py-4">Categoría</th>
                                <th class="px-6 py-4 text-center">Estado</th>
                                <th class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach($puntos as $punto)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $punto->id }}</td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $punto->title }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $punto->sector }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 px-2 py-1 rounded text-[10px]">{{ $punto->categoria->nombre ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $punto->activo ? 'ACTIVO' : 'OCULTO' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.puntos.edit', $punto) }}" class="text-blue-600 font-bold hover:underline">Editar</a>
                                    <form action="{{ route('admin.puntos.toggle', $punto) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="font-bold {{ $punto->activo ? 'text-orange-600' : 'text-green-600' }} hover:underline">
                                            {{ $punto->activo ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($puntos->isEmpty())
                        <div class="p-10 text-center text-gray-400 italic">No has creado puntos públicos todavía.</div>
                    @endif
                </div>
                @if($puntos->hasPages())
                    <div class="mt-4">{{ $puntos->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>

@vite('resources/js/quill-editor.js')

<style>
    [x-cloak] { display: none !important; }
    #description-editor .ql-container { font-size: 14px; border-radius: 0 0 0.5rem 0.5rem; border-color: #e2e8f0; }
    #description-editor .ql-toolbar { border-radius: 0.5rem 0.5rem 0 0; border-color: #e2e8f0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const quill = new Quill('#description-editor', {
        theme: 'snow',
        placeholder: 'Escribe una descripción del lugar…',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['clean']
            ]
        }
    });

    let currentLang = 'es';

    function syncQuillActual() {
        document.getElementById('description_' + currentLang).value = quill.root.innerHTML;
    }

    document.getElementById('main-form').addEventListener('submit', syncQuillActual);
    window.addEventListener('trigger-pindoor-submit', syncQuillActual, true);

    const TAB_ESTILOS = {
        es: 'border-[#fc5648] bg-[#fc5648] text-white',
        en: 'border-blue-500 bg-blue-500 text-white',
        fr: 'border-indigo-500 bg-indigo-500 text-white',
    };
    const TAB_INACTIVO = 'border-gray-200 bg-white text-gray-500 hover:border-gray-400';
    const EDITOR_COLOR = { es: '#fc5648', en: '#3b82f6', fr: '#6366f1' };

    window.setLang = function(lang) {
        syncQuillActual();

        document.querySelectorAll('[data-lang-field]').forEach(el => {
            el.style.display = el.dataset.langField === lang ? '' : 'none';
        });

        quill.setContents([]);
        const nuevoContenido = document.getElementById('description_' + lang).value;
        if (nuevoContenido.trim()) {
            quill.clipboard.dangerouslyPasteHTML(nuevoContenido);
        }

        const label = document.getElementById('editor-lang-label');
        if (label) {
            label.textContent = lang.toUpperCase();
            label.style.color = EDITOR_COLOR[lang];
        }

        Object.keys(TAB_ESTILOS).forEach(l => {
            const tab = document.getElementById('tab-' + l);
            if (!tab) return;
            tab.className = 'flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border transition '
                + (l === lang ? TAB_ESTILOS[l] : TAB_INACTIVO);
        });

        currentLang = lang;
    };

    window.autoTraducir = async function(destino) {
        const btn    = document.getElementById('btn-autotraducir-' + destino);
        const estado = document.getElementById('traducir-estado');

        syncQuillActual();

        const tituloEs = document.getElementById('title-es').value.trim();
        const textoEs  = document.getElementById('description_es').value
            ? new DOMParser().parseFromString(document.getElementById('description_es').value, 'text/html').body.textContent.trim()
            : '';

        if (!tituloEs && !textoEs) {
            alert('Escribe primero el contenido en Español.');
            return;
        }

        btn.disabled  = true;
        btn.className = btn.className.replace('text-gray-500', 'text-blue-400');
        estado.classList.remove('hidden');

        async function traducirTexto(txt) {
            if (!txt) return '';
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
                estado.textContent = 'Traduciendo nombre…';
                document.getElementById('title-' + destino).value = await traducirTexto(tituloEs);
            }
            if (textoEs) {
                estado.textContent = 'Traduciendo descripción…';
                const traducido = await traducirTexto(textoEs);
                const oraciones = traducido.match(/[^.!?]+[.!?]+/g) || [traducido];
                let html = '';
                let buf = '';
                oraciones.forEach((o, i) => {
                    buf += o + ' ';
                    if (buf.length > 350 || i === oraciones.length - 1) {
                        html += '<p>' + buf.trim() + '</p>';
                        buf = '';
                    }
                });
                document.getElementById('description_' + destino).value = html;
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

    window.validarYEnviar = function () {
        syncQuillActual();

        const campos = [
            { id: 'title-es',      label: 'Nombre del Punto' },
            { name: 'categoria_id', label: 'Categoría' },
            { name: 'sector',     label: 'Sector / Cerro' },
        ];

        const errores = [];

        // Limpiar estado previo
        document.querySelectorAll('.campo-error').forEach(el => {
            el.classList.remove('campo-error', 'border-red-400', 'ring-1', 'ring-red-400');
        });

        campos.forEach(({ id, name, label }) => {
            const el = id
                ? document.getElementById(id)
                : document.querySelector(`[name="${name}"]`);
            if (!el || !el.value.trim()) {
                errores.push(label);
                if (el) {
                    el.classList.add('campo-error', 'border-red-400', 'ring-1', 'ring-red-400');
                }
            }
        });

        // Validar descripción ES (Quill)
        const htmlDesc  = document.getElementById('description_es').value;
        const textoDesc = htmlDesc ? new DOMParser().parseFromString(htmlDesc, 'text/html').body.textContent.trim() : '';
        if (!textoDesc) {
            errores.push('Reseña o descripción');
            document.getElementById('description-editor').classList.add('campo-error', 'border-red-400', 'ring-1', 'ring-red-400');
        }

        const banner = document.getElementById('form-errors');
        const lista  = document.getElementById('form-errors-list');

        if (errores.length > 0) {
            lista.innerHTML = errores.map(e => `<li>${e}</li>`).join('');
            banner.classList.remove('hidden');
            banner.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        banner.classList.add('hidden');
        window.dispatchEvent(new CustomEvent('trigger-pindoor-submit'));
    };
});
</script>
