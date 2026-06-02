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

