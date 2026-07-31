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

                        {{-- Fila 1: Nombre + Categoría --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre del Punto <span class="text-red-500">*</span>
                                </label>
                                <x-text-input id="title" name="title" class="block mt-1 w-full" required />
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
                                Reseña o descripción <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description-input" name="description" class="hidden"></textarea>
                            <div id="description-editor"
                                 class="bg-white border border-slate-200 rounded-lg"
                                 style="min-height: 180px;"></div>
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

    const hidden = document.getElementById('description-input');
    if (hidden.value) quill.root.innerHTML = hidden.value;

    document.getElementById('main-form').addEventListener('submit', () => {
        hidden.value = quill.root.innerHTML;
    });

    window.addEventListener('trigger-pindoor-submit', () => {
        hidden.value = quill.root.innerHTML;
    }, true);

    window.validarYEnviar = function () {
        hidden.value = quill.root.innerHTML;

        const campos = [
            { id: 'title',        label: 'Nombre del Punto' },
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

        // Validar descripción (Quill)
        const textoDesc = quill.getText().trim();
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
