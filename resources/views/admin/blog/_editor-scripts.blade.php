@vite('resources/js/quill-editor.js')

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Quill editor ────────────────────────────────────────────────
    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'header': 2 }, { 'header': 3 }],
        ['blockquote'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        ['link', 'image'],
        [{ 'align': [] }],
        ['clean'],
    ];

    const quill = new Quill('#blog-editor', {
        theme: 'snow',
        placeholder: 'Escribe aquí tu artículo...',
        modules: {
            toolbar: {
                container: toolbarOptions,
                handlers: { image: imageHandler },
            },
        },
    });

    // Cargar contenido existente (edición)
    const hidden = document.getElementById('contenido-hidden');
    if (hidden.value.trim()) {
        quill.clipboard.dangerouslyPasteHTML(hidden.value);
    }

    // Sincronizar al enviar
    document.getElementById('blog-form').addEventListener('submit', function () {
        hidden.value = quill.root.innerHTML;
    });

    // ── Subida de imagen al servidor ────────────────────────────────
    function imageHandler() {
        const input = document.createElement('input');
        input.type    = 'file';
        input.accept  = 'image/*';
        input.click();

        input.onchange = async () => {
            const file = input.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('imagen', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const res  = await fetch('{{ route('admin.blog.imagen') }}', {
                    method: 'POST',
                    body: formData,
                });
                const data = await res.json();
                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', data.url);
                quill.setSelection(range.index + 1);
            } catch (e) {
                alert('Error al subir la imagen. Inténtalo de nuevo.');
            }
        };
    }

    // ── Slug automático desde título ────────────────────────────────
    const tituloInput = document.getElementById('titulo');
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

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('galeriaManager', (imagenesExistentes) => ({

        existentes: (imagenesExistentes || []).map((img, idx) => ({
            idx,
            url: '{{ asset('storage') }}/' + (typeof img === 'string' ? img : img.ruta),
            posicion: (typeof img === 'object' && img.posicion) ? String(img.posicion) : '',
            eliminar: false,
        })),

        previews: {},   // slot (1-5) → data:URL

        get activasExistentes() {
            return this.existentes.filter(i => !i.eliminar).length;
        },

        get totalUsados() {
            return this.activasExistentes + Object.keys(this.previews).length;
        },

        get slotsNuevos() {
            const disponibles = 5 - this.activasExistentes;
            return Array.from({ length: disponibles }, (_, i) => i + 1);
        },

        marcarEliminar(idx) {
            this.existentes[idx].eliminar = !this.existentes[idx].eliminar;
        },

        // Carga múltiple: distribuye archivos en slots disponibles usando DataTransfer
        cargarMultiples(event) {
            const files  = Array.from(event.target.files);
            const slots  = this.slotsNuevos.filter(s => !this.previews[s]);
            const pares  = files.slice(0, slots.length);

            pares.forEach((file, i) => {
                const slot  = slots[i];
                const input = document.getElementById('slot-input-' + slot);
                if (input) {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                }
                const reader = new FileReader();
                reader.onload = e => { this.previews = { ...this.previews, [slot]: e.target.result }; };
                reader.readAsDataURL(file);
            });

            event.target.value = '';
        },

        // Carga individual desde el slot
        previewUno(event, slot) {
            const file = event.target.files[0];
            if (!file) {
                const p = { ...this.previews };
                delete p[slot];
                this.previews = p;
                return;
            }
            const reader = new FileReader();
            reader.onload = e => { this.previews = { ...this.previews, [slot]: e.target.result }; };
            reader.readAsDataURL(file);
        },

        // Vaciar un slot
        limpiarSlot(slot) {
            const input = document.getElementById('slot-input-' + slot);
            if (input) { input.value = ''; try { input.files = new DataTransfer().files; } catch(_) {} }
            const posInput = document.getElementById('posicion-nueva-' + slot);
            if (posInput) posInput.value = '';
            const p = { ...this.previews };
            delete p[slot];
            this.previews = p;
        },
    }));
});
</script>
