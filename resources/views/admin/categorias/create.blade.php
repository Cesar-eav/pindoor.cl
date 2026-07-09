<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.categorias.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="font-extrabold text-xl text-gray-900">Nueva categoría</h2>
                <p class="text-sm text-gray-400 mt-0.5">Configura nombre, icono y módulos por defecto</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto px-4 sm:px-6" style="max-width:90%">
            <form action="{{ route('admin.categorias.store') }}" method="POST">
                @csrf
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
                                <div id="preview-icon-box"
                                     class="w-10 h-10 rounded-xl bg-[#fff0ef] flex items-center justify-center shrink-0">
                                    <i id="preview-icon" class="fa-solid fa-tag text-[#fc5648]"></i>
                                </div>
                                <div>
                                    <p id="preview-nombre" class="font-bold text-gray-800 text-sm">Nueva categoría</p>
                                    <p id="preview-tipo" class="text-[11px] text-gray-400"></p>
                                </div>
                            </div>
                            <div id="preview-modulos" class="flex flex-wrap gap-1 mt-3 empty:hidden"></div>
                        </div>

                        {{-- Acciones --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-2">
                            <button type="submit"
                                    class="w-full bg-[#fc5648] text-white py-2.5 rounded-xl font-bold text-sm hover:bg-[#d94439] transition">
                                Crear categoría
                            </button>
                            <a href="{{ route('admin.categorias.index') }}"
                               class="block w-full text-center py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                                Cancelar
                            </a>
                        </div>

                        {{-- Info --}}
                        <div class="bg-blue-50 rounded-2xl border border-blue-100 p-4 text-xs text-blue-700 space-y-1.5">
                            <p class="font-bold">¿Para qué sirven los módulos por defecto?</p>
                            <p>Al activar un negocio en esta categoría, estos módulos quedarán habilitados automáticamente. El admin puede ajustarlos después desde el panel de módulos del cliente.</p>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const MODULOS = @json(\App\Models\PuntoInteres::catalogoModulos());

        const nombre  = document.querySelector('input[name="nombre"]');
        const tipo    = document.querySelector('input[name="tipo"]');
        const iconoEl = document.getElementById('icono-input');
        const checkboxes = document.querySelectorAll('input[name="modulos_defecto[]"]');

        function updatePreview() {
            document.getElementById('preview-nombre').textContent = nombre?.value || 'Nueva categoría';
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
        iconoEl?.addEventListener('change', updatePreview);
        checkboxes.forEach(cb => cb.addEventListener('change', updatePreview));

        // Observer para cuando el icono cambia vía selectIcono()
        if (iconoEl) {
            new MutationObserver(updatePreview).observe(iconoEl, { attributes: true, attributeFilter: ['value'] });
            iconoEl.addEventListener('input', updatePreview);
        }

        // Patch selectIcono to trigger update
        const origSelect = window.selectIcono;
        window.selectIcono = function(name, label) {
            if (origSelect) origSelect(name, label);
            setTimeout(updatePreview, 50);
        };
    });
    </script>
</x-app-layout>
