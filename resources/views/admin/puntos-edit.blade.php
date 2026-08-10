<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar: {{ $punto->title }}
            </h2>
            <a href="{{ route('admin.puntos.create') }}" class="text-sm text-gray-500 hover:text-gray-800 transition">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-1">Información del Punto Público</h3>
                <p class="text-xs text-gray-400 mb-6"><span class="text-red-500">*</span> Campo obligatorio</p>

                <form id="main-form" onsubmit="return false;">
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
                                <x-text-input id="title-es" name="title_es" class="block mt-1 w-full" required value="{{ $punto->getTranslation('title','es',false) }}" />
                            </div>
                            <div data-lang-field="en" style="display:none">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-blue-500">EN</span></label>
                                <x-text-input id="title-en" name="title_en" class="block mt-1 w-full" value="{{ $punto->getTranslation('title','en',false) }}" />
                            </div>
                            <div data-lang-field="fr" style="display:none">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom <span class="text-indigo-500">FR</span></label>
                                <x-text-input id="title-fr" name="title_fr" class="block mt-1 w-full" value="{{ $punto->getTranslation('title','fr',false) }}" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Categoría <span class="text-red-500">*</span>
                            </label>
                            <select name="categoria_id" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-pindoor-accent">
                                <option value="">Selecciona una categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" @selected($punto->categoria_id == $cat->id)>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Fila 2: Autor + Tags --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="autor" value="Autor / Artista / Institución" />
                            <x-text-input id="autor" name="autor" class="block mt-1 w-full"
                                placeholder="Ej: Municipalidad de Valparaíso" value="{{ $punto->autor }}" />
                        </div>
                        <div>
                            <x-input-label for="tags" value="Etiquetas (separadas por coma)" />
                            <x-text-input id="tags" name="tags" class="block mt-1 w-full"
                                placeholder="vista, historia, arte"
                                value="{{ is_array($punto->tags) ? implode(', ', $punto->tags) : $punto->tags }}" />
                        </div>
                    </div>

                    {{-- Fila 3: Sector + Dirección --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="sector" value="Sector / Cerro" />
                            @include('admin.partials._sector-select', ['selected' => $punto->sector])
                        </div>
                        <div>
                            <x-input-label for="direccion" value="Dirección" />
                            <x-text-input id="direccion" name="direccion" class="block mt-1 w-full"
                                placeholder="Ej: Pasaje Gálvez 214, Cerro Alegre" value="{{ $punto->direccion }}" />
                        </div>
                    </div>

                    {{-- Fila 4: Horario + Enlace --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <x-input-label for="horario" value="Horario de visita" />
                            <x-text-input id="horario" name="horario" class="block mt-1 w-full"
                                placeholder="Ej: Lun–Vie 09:00–18:00" value="{{ $punto->horario }}" />
                        </div>
                        <div>
                            <x-input-label for="enlace" value="Sitio web o Instagram" />
                            <x-text-input id="enlace" name="enlace" type="url" class="block mt-1 w-full"
                                placeholder="https://..." value="{{ $punto->enlace }}" />
                        </div>
                    </div>

                    {{-- Video YouTube --}}
                    <div class="mb-6">
                        <x-input-label for="video_url" value="Video de YouTube" />
                        <x-text-input id="video_url" name="video_url" type="url" class="block mt-1 w-full"
                            placeholder="https://www.youtube.com/watch?v=..." value="{{ $punto->video_url }}" />
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Reseña o descripción — <span id="editor-lang-label" class="text-[#fc5648]">ES</span> <span class="text-red-500">*</span>
                        </label>
                        <div id="description-editor"
                             class="bg-white border border-gray-300 rounded-lg"
                             style="min-height: 180px;"></div>
                        <textarea id="description_es" name="description_es" class="hidden">{{ $punto->getTranslation('description','es',false) }}</textarea>
                        <textarea id="description_en" name="description_en" class="hidden">{{ $punto->getTranslation('description','en',false) }}</textarea>
                        <textarea id="description_fr" name="description_fr" class="hidden">{{ $punto->getTranslation('description','fr',false) }}</textarea>
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
                                  placeholder="Sinónimos, palabras que la gente buscaría, tipo de lugar, características…">{{ old('descripcion_busqueda_admin', $punto->descripcion_busqueda_admin) }}</textarea>
                    </div>

                    {{-- Mapa + Galería (Vue) --}}
                    @php
                        $imagenesJson = json_encode($punto->imagenes->map(function($img) {
                            return [
                                'id'           => $img->id,
                                'url'          => asset('storage/' . $img->ruta),
                                'es_principal' => $img->es_principal,
                                'orden'        => $img->orden,
                            ];
                        })->values()->all());
                    @endphp

                    <div id="app" class="mb-6">
                        <x-input-label value="Ubicación en el mapa" />
                        <p class="text-xs text-gray-400 mb-2">Escribe la dirección y el pin se ubica solo. Si no queda exacto, haz clic en el mapa o arrastra el marcador.</p>
                        <selector-mapa
                            :initial-lat="{{ $punto->lat ?? -33.0472 }}"
                            :initial-lng="{{ $punto->lng ?? -71.6297 }}"
                            geocode-url="{{ route('admin.geocodificar') }}"
                            reverse-geocode-url="{{ route('admin.geocodificar-inverso') }}"
                        ></selector-mapa>

                        <div class="mt-6">
                            <x-input-label value="Fotografías" />
                            <p class="text-xs text-gray-400 mb-2">Puedes reordenar arrastrando. La imagen marcada como Principal es la portada.</p>
                            <galeria-subida
                                :punto-id="{{ $punto->id }}"
                                :initial-images="{{ $imagenesJson }}"
                                endpoint="{{ route('admin.puntos.update', $punto) }}"
                            ></galeria-subida>
                        </div>
                    </div>

                    {{-- Perfil básico (sin dueño) --}}
                    <div class="mb-6 p-4 rounded-xl border border-gray-200 bg-gray-50">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="publicar_como_basico" value="1"
                                   {{ $punto->esBasico() ? 'checked' : '' }}
                                   class="mt-1 rounded border-gray-300 text-pindoor-accent focus:ring-pindoor-accent">
                            <div>
                                <span class="text-sm font-semibold text-gray-800">Publicar como perfil básico (sin dueño asignado)</span>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    El negocio aparece en Pindoor con información básica y podrá activar su perfil más adelante para desbloquear galería, menú, eventos y promociones.
                                    Si ya tiene un dueño real asignado, desmarcar esta opción no le quita su cuenta.
                                </p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.puntos.create') }}"
                           class="px-6 py-3 rounded-2xl font-bold text-gray-600 hover:text-gray-900 transition">
                            Cancelar
                        </a>
                        <button
                            type="button"
                            onclick="window.dispatchEvent(new CustomEvent('trigger-pindoor-submit'))"
                            class="bg-pindoor-accent text-white px-8 py-3 rounded-2xl font-bold shadow-lg hover:bg-red-600 transition">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>

@vite('resources/js/quill-editor.js')

<style>
    [x-cloak] { display: none !important; }
    #description-editor .ql-container { font-size: 14px; border-radius: 0 0 0.5rem 0.5rem; border-color: #d1d5db; }
    #description-editor .ql-toolbar { border-radius: 0.5rem 0.5rem 0 0; border-color: #d1d5db; }
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
    const hiddenEs = document.getElementById('description_es');
    if (hiddenEs.value) quill.root.innerHTML = hiddenEs.value;

    function syncQuillActual() {
        document.getElementById('description_' + currentLang).value = quill.root.innerHTML;
    }

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
});
</script>
