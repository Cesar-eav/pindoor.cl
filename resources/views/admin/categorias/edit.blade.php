<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categorias.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-extrabold text-xl text-gray-900">{{ $categoria->nombre }}</h2>
                <p class="text-sm text-gray-400 mt-0.5">ID {{ $categoria->id }} · {{ $categoria->puntos_interes_count }} {{ $categoria->puntos_interes_count === 1 ? 'punto asignado' : 'puntos asignados' }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto px-4 sm:px-6" style="max-width:90%">
            <form action="{{ route('admin.categorias.update', $categoria) }}" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Columna principal --}}
                    <div class="lg:col-span-2 space-y-5">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Datos básicos</h3>
                            @include('admin.categorias._form')
                        </div>
                    </div>

                    {{-- Columna lateral --}}
                    <div class="space-y-5">

                        {{-- Preview --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Vista previa</h3>
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                                <div class="w-10 h-10 rounded-xl bg-[#fff0ef] flex items-center justify-center shrink-0">
                                    <i id="preview-icon" class="{{ $categoria->icono ? 'fa-solid fa-'.$categoria->icono : 'fa-solid fa-tag text-gray-300' }} text-[#fc5648]"></i>
                                </div>
                                <div>
                                    <p id="preview-nombre" class="font-bold text-gray-800 text-sm">{{ $categoria->nombre }}</p>
                                    <p id="preview-tipo" class="text-[11px] text-gray-400">{{ $categoria->tipo }}</p>
                                </div>
                            </div>
                            <div id="preview-modulos" class="flex flex-wrap gap-1 mt-3">
                                @foreach($categoria->modulos_defecto ?? [] as $mod)
                                    @if(isset($catalogo[$mod]))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-semibold border border-emerald-100">
                                            {{ $catalogo[$mod]['emoji'] }} {{ $catalogo[$mod]['label'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Datos actuales</h3>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Slug</dt>
                                    <dd class="font-mono text-xs text-gray-700 bg-gray-100 px-2 py-0.5 rounded">{{ $categoria->slug }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Puntos asignados</dt>
                                    <dd class="font-bold text-gray-900">{{ $categoria->puntos_interes_count }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Módulos defecto</dt>
                                    <dd class="font-bold text-gray-900">{{ count($categoria->modulos_defecto ?? []) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Creada</dt>
                                    <dd class="text-gray-700">{{ $categoria->created_at->format('d/m/Y') }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Acciones --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-2">
                            <button type="submit"
                                    class="w-full bg-[#fc5648] text-white py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                                Guardar cambios
                            </button>
                            <a href="{{ route('admin.categorias.index') }}"
                               class="block w-full text-center py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                        </div>

                        {{-- Zona peligrosa --}}
                        @if($categoria->puntos_interes_count === 0)
                        <div class="bg-red-50 rounded-2xl border border-red-100 p-5">
                            <h3 class="text-xs font-bold text-red-600 uppercase tracking-wide mb-2">Zona peligrosa</h3>
                            <p class="text-xs text-red-500 mb-3">Esta categoría no tiene puntos asignados y puede eliminarse.</p>
                            <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar «{{ $categoria->nombre }}»? Esta acción no se puede deshacer.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-full py-2 rounded-xl bg-red-500 text-white text-sm font-bold hover:bg-red-600 transition">
                                    Eliminar categoría
                                </button>
                            </form>
                        </div>
                        @else
                        <div class="bg-gray-50 rounded-2xl border border-gray-100 p-4 text-xs text-gray-400 text-center">
                            No se puede eliminar: tiene {{ $categoria->puntos_interes_count }} puntos asignados.
                        </div>
                        @endif

                    </div>
                </div>
            </form>
        </div>
    </div>

    @php $catalogo = \App\Models\PuntoInteres::catalogoModulos(); @endphp
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const MODULOS = @json(\App\Models\PuntoInteres::catalogoModulos());

        const nombre  = document.querySelector('input[name="nombre"]');
        const tipo    = document.querySelector('input[name="tipo"]');
        const iconoEl = document.getElementById('icono-input');
        const checkboxes = document.querySelectorAll('input[name="modulos_defecto[]"]');

        function updatePreview() {
            document.getElementById('preview-nombre').textContent = nombre?.value || '';
            document.getElementById('preview-tipo').textContent   = tipo?.value || '';

            const icono = iconoEl?.value;
            document.getElementById('preview-icon').className =
                icono ? `fa-solid fa-${icono} text-[#fc5648]` : 'fa-solid fa-tag text-gray-300';

            const wrap = document.getElementById('preview-modulos');
            wrap.innerHTML = '';
            checkboxes.forEach(cb => {
                if (!cb.checked) return;
                const m = MODULOS[cb.value];
                if (!m) return;
                const chip = document.createElement('span');
                chip.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-semibold border border-emerald-100';
                chip.textContent = m.emoji + ' ' + m.label;
                wrap.appendChild(chip);
            });
        }

        nombre?.addEventListener('input', updatePreview);
        tipo?.addEventListener('input', updatePreview);
        checkboxes.forEach(cb => cb.addEventListener('change', updatePreview));

        const origSelect = window.selectIcono;
        window.selectIcono = function(name, label) {
            if (origSelect) origSelect(name, label);
            setTimeout(updatePreview, 50);
        };
    });
    </script>
</x-admin-layout>
