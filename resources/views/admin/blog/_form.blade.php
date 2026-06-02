@php $esEdicion = isset($post) && $post !== null; @endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data"
      id="blog-form" class="space-y-6">
    @csrf
    @if($method !== 'POST') @method($method) @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Título + Slug --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Título *</label>
            <input id="titulo" type="text" name="titulo" required
                   value="{{ old('titulo', $post?->titulo) }}"
                   placeholder="Ej: Los mejores rincones del cerro Alegre"
                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-base font-semibold focus:ring-2 focus:ring-[#fc5648] outline-none">
        </div>
        <div>
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
                Slug (URL)
                <span class="normal-case font-normal text-gray-400 ml-1">— se genera automático desde el título</span>
            </label>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-400 shrink-0">/blog/</span>
                <input id="slug" type="text" name="slug"
                       value="{{ old('slug', $post?->slug) }}"
                       placeholder="mi-primer-post"
                       class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
            </div>
        </div>
    </div>

    {{-- Resumen --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-1">
            Resumen
            <span class="normal-case font-normal text-gray-400 ml-1">— aparece en las tarjetas del blog</span>
        </label>
        <textarea name="resumen" rows="3" maxlength="600"
                  placeholder="Una breve introducción que invite a leer el post completo..."
                  class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ old('resumen', $post?->resumen) }}</textarea>
    </div>

    {{-- Imagen portada --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Imagen de portada</label>

        @if($esEdicion && $post->imagen_portada_url)
        <div class="mb-4" id="portada-preview-actual">
            <img src="{{ $post->imagen_portada_url }}" alt="Portada actual"
                 class="w-full max-w-sm rounded-2xl object-cover aspect-video border border-gray-100">
            <p class="text-xs text-gray-400 mt-1">Portada actual — sube una nueva para reemplazarla</p>
        </div>
        @endif

        <div id="portada-preview-nueva" class="mb-4 hidden">
            <img id="portada-img-nueva" src="" alt="Nueva portada"
                 class="w-full max-w-sm rounded-2xl object-cover aspect-video border border-gray-100">
        </div>

        <input type="file" name="imagen_portada" id="imagen_portada" accept="image/*"
               class="block w-full text-sm text-gray-500
                      file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0
                      file:text-sm file:font-bold file:bg-[#fff0ef] file:text-[#fc5648]
                      hover:file:bg-[#ffe0dd] cursor-pointer">
    </div>

    {{-- Componente Alpine de galería --}}
    <script>
    window.__storageUrl = {{ json_encode(asset('storage')) }};
    window.galeriaManager = function(imagenesExistentes) {
        return {
            existentes: (imagenesExistentes || []).map(function(img, idx) {
                return {
                    idx: idx,
                    url: window.__storageUrl + '/' + (typeof img === 'string' ? img : img.ruta),
                    posicion: (typeof img === 'object' && img.posicion) ? String(img.posicion) : '',
                    eliminar: false,
                };
            }),
            previews: {},
            activasExistentes: 0,
            totalUsados: 0,
            slotsNuevos: [],

            init: function() {
                var self = this;
                self._recompute();
                self.$watch('existentes', function() { self._recompute(); });
                self.$watch('previews',   function() { self._recompute(); });
            },

            _recompute: function() {
                this.activasExistentes = this.existentes.filter(function(i){ return !i.eliminar; }).length;
                this.totalUsados       = this.activasExistentes + Object.keys(this.previews).length;
                var n = 5 - this.activasExistentes;
                this.slotsNuevos = Array.from({ length: n < 0 ? 0 : n }, function(_, i){ return i + 1; });
            },

            marcarEliminar: function(idx) {
                this.existentes[idx].eliminar = !this.existentes[idx].eliminar;
                this._recompute();
            },

            cargarMultiples: function(event) {
                var self = this;
                var files = Array.from(event.target.files);
                var slots = self.slotsNuevos.filter(function(s){ return !self.previews[s]; });
                files.slice(0, slots.length).forEach(function(file, i) {
                    var slot  = slots[i];
                    var input = document.getElementById('slot-input-' + slot);
                    if (input) { var dt = new DataTransfer(); dt.items.add(file); input.files = dt.files; }
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        self.previews = Object.assign({}, self.previews, { [slot]: e.target.result });
                    };
                    reader.readAsDataURL(file);
                });
                event.target.value = '';
            },

            previewUno: function(event, slot) {
                var self = this;
                var file = event.target.files[0];
                if (!file) {
                    var p = Object.assign({}, self.previews);
                    delete p[slot];
                    self.previews = p;
                    return;
                }
                var reader = new FileReader();
                reader.onload = function(e) {
                    self.previews = Object.assign({}, self.previews, { [slot]: e.target.result });
                };
                reader.readAsDataURL(file);
            },

            limpiarSlot: function(slot) {
                var input = document.getElementById('slot-input-' + slot);
                if (input) { input.value = ''; try { input.files = new DataTransfer().files; } catch(e) {} }
                var posInput = document.getElementById('posicion-nueva-' + slot);
                if (posInput) posInput.value = '';
                var p = Object.assign({}, this.previews);
                delete p[slot];
                this.previews = p;
            },
        };
    };
    </script>

    {{-- Galería de imágenes (hasta 5) --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
         x-data="galeriaManager(@json($post?->imagenes ?? []))">

        {{-- Cabecera --}}
        <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-black uppercase tracking-widest text-gray-400">
                Imágenes del artículo
            </label>
            <span class="text-xs font-bold text-gray-400" x-text="totalUsados + '/5'"></span>
        </div>
        <p class="text-xs text-gray-400 mb-4">
            Sube hasta 5 fotos. Indica el número de párrafo tras el que debe aparecer, o déjalo vacío para distribución automática.
        </p>

        {{-- Aviso cuando todos los slots están ocupados --}}
        <div x-show="totalUsados >= 5 && activasExistentes >= 5" class="mb-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl px-4 py-2.5 text-xs font-medium">
            Ya hay 5 imágenes. Para añadir una nueva, marca con ✕ la que quieras reemplazar y se liberará un slot.
        </div>

        {{-- Botón carga múltiple --}}
        <div class="mb-5" x-show="totalUsados < 5">
            <input type="file" x-ref="cargaMultiple" multiple accept="image/*"
                   class="hidden" @change="cargarMultiples($event)">
            <button type="button" @click="$refs.cargaMultiple.click()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#fff0ef] text-[#fc5648]
                           rounded-xl text-sm font-bold hover:bg-[#ffe0dd] transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Subir varias fotos a la vez
            </button>
        </div>

        {{-- Grid de imágenes --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">

            {{-- ── Imágenes existentes ── --}}
            <template x-for="(img, idx) in existentes" :key="'e'+idx">
                <div>
                    <div class="relative aspect-square rounded-2xl overflow-hidden border-2 bg-gray-50"
                         :class="img.eliminar ? 'border-red-300' : 'border-gray-100'">
                        <img :src="img.url" alt="" class="w-full h-full object-cover">

                        {{-- Botón ✕ siempre visible en esquina --}}
                        <button type="button" @click="marcarEliminar(idx)"
                                x-show="!img.eliminar"
                                class="absolute top-1.5 right-1.5 bg-red-500 hover:bg-red-600 text-white
                                       rounded-full w-7 h-7 flex items-center justify-center
                                       text-xs font-bold shadow-md transition z-10">
                            ✕
                        </button>

                        {{-- Tachado si marcada para borrar --}}
                        <div x-show="img.eliminar"
                             class="absolute inset-0 bg-red-500/70 flex flex-col items-center justify-center gap-1 cursor-pointer"
                             @click="marcarEliminar(idx)">
                            <span class="text-white font-black text-xs">BORRAR</span>
                            <span class="text-white/80 text-[10px]">clic para deshacer</span>
                        </div>

                        <template x-if="img.eliminar">
                            <input type="hidden" name="eliminar_imagen[]" :value="idx">
                        </template>
                    </div>

                    {{-- Posición para imagen existente --}}
                    <div x-show="!img.eliminar" class="mt-1.5">
                        <p class="text-[10px] text-gray-400 font-semibold text-center mb-0.5">Tras párrafo nº</p>
                        <input type="number" min="1" max="99"
                               :name="'posicion_existente_' + idx"
                               x-model="img.posicion"
                               placeholder="auto"
                               class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg
                                      focus:ring-1 focus:ring-[#fc5648] outline-none text-center">
                    </div>
                    <div x-show="img.eliminar" class="mt-1.5 text-center">
                        <span class="text-[10px] text-red-400 font-semibold">Se eliminará al guardar</span>
                    </div>
                </div>
            </template>

            {{-- ── Slots para nuevas imágenes ── --}}
            <template x-for="slot in slotsNuevos" :key="'n'+slot">
                <div>
                    <label class="relative aspect-square rounded-2xl border-2 border-dashed border-gray-200
                                  bg-gray-50 hover:border-[#fc5648] hover:bg-[#fff8f7]
                                  transition cursor-pointer overflow-hidden block">

                        <template x-if="previews[slot]">
                            <img :src="previews[slot]" alt=""
                                 class="w-full h-full object-cover absolute inset-0">
                        </template>

                        <template x-if="!previews[slot]">
                            <div class="w-full h-full flex flex-col items-center justify-center gap-1">
                                <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="text-[10px] text-gray-400 font-bold">Foto</span>
                            </div>
                        </template>

                        {{-- Botón limpiar slot (si tiene preview) --}}
                        <template x-if="previews[slot]">
                            <button type="button"
                                    @click.prevent="limpiarSlot(slot)"
                                    class="absolute top-1.5 right-1.5 bg-red-500 text-white
                                           rounded-full w-6 h-6 flex items-center justify-center
                                           text-xs font-bold shadow z-10">
                                ✕
                            </button>
                        </template>

                        {{-- Input oculto, nombrado por slot --}}
                        <input type="file"
                               :id="'slot-input-' + slot"
                               :name="'imagen_nueva_' + slot"
                               accept="image/*"
                               class="absolute inset-0 opacity-0 w-full h-full cursor-pointer"
                               @change="previewUno($event, slot)">
                    </label>

                    {{-- Posición para imagen nueva --}}
                    <div x-show="previews[slot]" class="mt-1.5">
                        <input type="number" min="1" max="99"
                               :id="'posicion-nueva-' + slot"
                               :name="'posicion_nueva_' + slot"
                               placeholder="Párrafo (auto)"
                               class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg
                                      focus:ring-1 focus:ring-[#fc5648] outline-none text-center">
                    </div>
                </div>
            </template>

        </div>
    </div>

    {{-- Contenido Quill --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-3">Contenido</label>

        {{-- Editor Quill --}}
        <div id="blog-editor" class="min-h-[400px]"></div>

        {{-- Campo oculto que recibe el HTML --}}
        <textarea id="contenido-hidden" name="contenido" class="hidden">{{ old('contenido', $post?->contenido) }}</textarea>
    </div>

    {{-- Publicar + Guardar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex items-center justify-between gap-4">
        <label class="flex items-center gap-3 cursor-pointer select-none">
            <div class="relative">
                <input type="checkbox" name="publicado" id="publicado" value="1" class="sr-only peer"
                       {{ old('publicado', $post?->publicado) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-200 rounded-full peer
                            peer-checked:bg-[#fc5648] transition-colors duration-200"></div>
                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow
                            peer-checked:translate-x-5 transition-transform duration-200"></div>
            </div>
            <span class="text-sm font-bold text-gray-700" id="publicado-label">
                {{ old('publicado', $post?->publicado) ? 'Publicado' : 'Borrador' }}
            </span>
        </label>

        <button type="submit"
                class="bg-gray-900 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-black transition">
            {{ $esEdicion ? 'Guardar cambios' : 'Crear post' }}
        </button>
    </div>
</form>
