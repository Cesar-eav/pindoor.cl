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

    // ── Contador Resumen ─────────────────────────────────────────────
    const RESUMEN_MAX = 600;
    const resumenInput  = document.getElementById('resumen-input');
    const resumenChars  = document.getElementById('resumen-chars');
    const resumenPalab  = document.getElementById('resumen-palabras');

    function contarResumen() {
        const txt    = resumenInput.value;
        const chars  = txt.length;
        const words  = txt.trim() === '' ? 0 : txt.trim().split(/\s+/).length;
        const quedan = RESUMEN_MAX - chars;
        resumenPalab.textContent = words + (words === 1 ? ' palabra' : ' palabras');
        resumenChars.textContent = chars + ' / ' + RESUMEN_MAX;
        resumenChars.classList.toggle('text-red-500', quedan <= 50);
        resumenChars.classList.toggle('text-gray-400', quedan > 50);
    }
    resumenInput.addEventListener('input', contarResumen);
    contarResumen();

    // ── Contador Contenido (Quill) ───────────────────────────────────
    const CONTENIDO_MAX  = 10000;
    const contenidoChars = document.getElementById('contenido-chars');
    const contenidoPalab = document.getElementById('contenido-palabras');
    const contenidoAviso = document.getElementById('contenido-limite-aviso');

    function contarContenido() {
        const txt    = quill.getText().replace(/\n$/, '');
        const chars  = txt.length;
        const words  = txt.trim() === '' ? 0 : txt.trim().split(/\s+/).length;
        const quedan = CONTENIDO_MAX - chars;
        contenidoPalab.textContent = words + (words === 1 ? ' palabra' : ' palabras');
        contenidoChars.textContent = chars.toLocaleString('es') + ' / ' + CONTENIDO_MAX.toLocaleString('es');
        contenidoChars.classList.toggle('text-red-500', quedan <= 200);
        contenidoChars.classList.toggle('text-gray-400', quedan > 200);
        contenidoAviso.classList.toggle('hidden', chars < CONTENIDO_MAX);
        if (chars > CONTENIDO_MAX) {
            quill.deleteText(CONTENIDO_MAX, chars - CONTENIDO_MAX);
        }
    }
    quill.on('text-change', contarContenido);
    contarContenido();

    // ── Vista previa de posiciones de imágenes ───────────────────────
    const TAGS_BLOQUE = ['</p>','</h2>','</h3>','</h4>','</blockquote>','</ul>','</ol>'];
    const NUMS = ['①','②','③','④','⑤','⑥','⑦','⑧','⑨','⑩',
                  '⑪','⑫','⑬','⑭','⑮','⑯','⑰','⑱','⑲','⑳'];

    function parsearBloques(html) {
        let marcado = html;
        TAGS_BLOQUE.forEach(t => { marcado = marcado.split(t).join(t + '¶'); });
        return marcado.split('¶')
            .map(b => b.replace(/<[^>]*>/g, '').trim())
            .filter(b => b !== '');
    }

    function leerImagenesGaleria() {
        const imgs = [];
        // Existentes
        document.querySelectorAll('[id^="existente-"]').forEach(el => {
            const idx = el.id.replace('existente-', '');
            const hidden = document.getElementById('hidden-eliminar-' + idx);
            if (hidden && !hidden.disabled) return; // marcada para borrar
            const thumb = el.querySelector('img');
            const posInput = document.getElementById('pos-existente-' + idx)?.querySelector('input[type="number"]');
            imgs.push({ src: thumb?.src || null, pos: parseInt(posInput?.value) || 0 });
        });
        // Slots nuevos
        for (let s = 1; s <= 20; s++) {
            const preview = document.getElementById('preview-' + s);
            if (!preview || preview.classList.contains('hidden')) continue;
            const posInput = document.getElementById('pos-nueva-' + s)?.querySelector('input[type="number"]');
            imgs.push({ src: preview.src, pos: parseInt(posInput?.value) || 0 });
        }
        return imgs;
    }

    function actualizarPreview() {
        const container = document.getElementById('preview-posiciones');
        const bloqueCount = document.getElementById('preview-bloque-count');
        if (!container) return;

        const bloques = parsearBloques(quill.root.innerHTML);
        const imagenes = leerImagenesGaleria();

        if (bloqueCount) bloqueCount.textContent = bloques.length + (bloques.length === 1 ? ' bloque' : ' bloques');

        if (bloques.length === 0) {
            container.innerHTML = '<p class="text-[11px] text-gray-300 italic">Escribe contenido para ver la vista previa…</p>';
            return;
        }

        // Agrupar imágenes posicionadas por bloque
        const porBloque = {};
        const automaticas = [];
        imagenes.forEach(img => {
            if (img.pos > 0) {
                if (!porBloque[img.pos]) porBloque[img.pos] = [];
                porBloque[img.pos].push(img);
            } else {
                automaticas.push(img);
            }
        });

        function thumbHtml(img) {
            return img.src
                ? `<img src="${img.src}" class="w-full h-14 object-cover rounded-lg border border-gray-200">`
                : `<div class="w-full h-10 bg-gray-200 rounded-lg"></div>`;
        }

        let out = '';
        bloques.forEach((texto, i) => {
            const n = i + 1;
            const tag = quill.root.innerHTML.match(/<(h[2-4])/)?.[1];
            const isHead = quill.root.querySelectorAll('h2,h3,h4')[0] && texto.length < 80;
            out += `<div class="flex items-start gap-2 py-1.5 border-b border-gray-100 last:border-0">
                <span class="shrink-0 text-[10px] font-black text-[#fc5648] mt-0.5 w-5 text-center">${NUMS[i] || n}</span>
                <span class="text-[11px] text-gray-600 leading-tight">${texto.slice(0, 80)}${texto.length > 80 ? '…' : ''}</span>
            </div>`;
            if (porBloque[n]) {
                porBloque[n].forEach(img => {
                    out += `<div class="pl-7 py-1">${thumbHtml(img)}</div>`;
                });
            }
        });

        if (automaticas.length > 0) {
            out += `<div class="pt-2 pb-1 text-[9px] font-black uppercase tracking-widest text-gray-400">Auto-distribuidas</div>`;
            automaticas.forEach(img => {
                out += `<div class="py-1 opacity-60">${thumbHtml(img)}</div>`;
            });
        }

        container.innerHTML = out;
    }

    quill.on('text-change', actualizarPreview);
    document.getElementById('galeria-grid')?.addEventListener('input', e => {
        if (e.target.type === 'number') actualizarPreview();
    });
    // Actualizar preview cuando se selecciona una imagen nueva en la galería
    document.getElementById('galeria-grid')?.addEventListener('change', e => {
        if (e.target.type === 'file') setTimeout(actualizarPreview, 200);
    });
    actualizarPreview();
});
</script>

