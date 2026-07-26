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

    const quill = new Quill('#recomendacion-editor', {
        theme: 'snow',
        placeholder: 'Redacta aquí el artículo: historia del local, qué pedir, ambiente, datos útiles…',
        modules: {
            toolbar: {
                container: toolbarOptions,
                handlers: { image: imageHandler },
            },
        },
    });

    const hiddenContenido = document.getElementById('contenido');
    if (hiddenContenido.value.trim()) {
        quill.clipboard.dangerouslyPasteHTML(hiddenContenido.value);
    }

    document.getElementById('recomendacion-form').addEventListener('submit', function () {
        hiddenContenido.value = quill.root.innerHTML;
    });

    // ── Video de YouTube: solo para Plan Cobertura Premium ───────────
    const campoVideoYoutube = document.getElementById('campo-video-youtube');
    const radiosPlan = document.querySelectorAll('input[name="plan"]');

    function actualizarCampoYoutube() {
        const seleccionado = document.querySelector('input[name="plan"]:checked')?.value;
        campoVideoYoutube.classList.toggle('hidden', seleccionado !== 'premium');
    }
    radiosPlan.forEach(r => r.addEventListener('change', actualizarCampoYoutube));
    actualizarCampoYoutube();

    // ── Subida de imagen al servidor (imagen incrustada en el editor) ──
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
                const res  = await fetch('{{ route('admin.recomendaciones.imagen') }}', {
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

    // ── Galería: eliminar existentes (marcar/desmarcar) ──────────────
    window.toggleEliminar = function(id) {
        var overlay  = document.getElementById('overlay-eliminar-' + id);
        var btn      = document.getElementById('btn-eliminar-' + id);
        var hidden   = document.getElementById('hidden-eliminar-' + id);
        var posDiv   = document.getElementById('pos-existente-' + id);
        var labelDiv = document.getElementById('label-eliminar-' + id);
        var marcado  = hidden && !hidden.disabled;
        if (marcado) {
            overlay.style.display = 'none';
            btn.style.display = '';
            hidden.disabled = true;
            posDiv.style.display = '';
            labelDiv.style.display = 'none';
        } else {
            overlay.style.display = 'flex';
            btn.style.display = 'none';
            hidden.disabled = false;
            posDiv.style.display = 'none';
            labelDiv.style.display = 'block';
        }
        actualizarPreview();
    };

    // ── Galería: preview de slots nuevos ─────────────────────────────
    window.previewSlot = function(slot, input) {
        var file = input.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-' + slot).src = e.target.result;
            document.getElementById('preview-' + slot).classList.remove('hidden');
            document.getElementById('placeholder-' + slot).classList.add('hidden');
            document.getElementById('btn-limpiar-' + slot).classList.remove('hidden');
            document.getElementById('btn-limpiar-' + slot).classList.add('flex');
            document.getElementById('pos-nueva-' + slot).classList.remove('hidden');
            actualizarPreview();
        };
        reader.readAsDataURL(file);
    };

    window.limpiarSlot = function(event, slot) {
        event.preventDefault();
        var input = document.getElementById('slot-input-' + slot);
        input.value = '';
        try { input.files = new DataTransfer().files; } catch(e) {}
        document.getElementById('preview-' + slot).classList.add('hidden');
        document.getElementById('placeholder-' + slot).classList.remove('hidden');
        document.getElementById('btn-limpiar-' + slot).classList.add('hidden');
        document.getElementById('btn-limpiar-' + slot).classList.remove('flex');
        document.getElementById('pos-nueva-' + slot).classList.add('hidden');
        document.getElementById('pos-nueva-' + slot).querySelector('input').value = '';
        actualizarPreview();
    };

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
        document.querySelectorAll('[id^="existente-"]').forEach(el => {
            const idx = el.id.replace('existente-', '');
            const hidden = document.getElementById('hidden-eliminar-' + idx);
            if (hidden && !hidden.disabled) return; // marcada para borrar
            const thumb = el.querySelector('img');
            const posInput = document.getElementById('pos-existente-' + idx)?.querySelector('input[type="number"]');
            imgs.push({ src: thumb?.src || null, pos: parseInt(posInput?.value) || 0 });
        });
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

        const porBloque = {};
        const automaticas = [];
        imagenes.forEach(img => {
            if (img.pos > 0) {
                if (!porBloque[img.pos]) porBloque[img.pos] = [];
                porBloque[img.pos].push({ ...img, _isAuto: false });
            } else {
                automaticas.push(img);
            }
        });

        if (automaticas.length > 0) {
            const n = automaticas.length;
            const numBloques = bloques.length;
            const interval = Math.max(1, Math.floor(numBloques / (n + 1)));
            let autoIdx = 0;
            for (let p = interval; p <= numBloques + 1 && autoIdx < n; p++) {
                if (!porBloque[p]) {
                    porBloque[p] = [{ ...automaticas[autoIdx++], _isAuto: true }];
                }
            }
            while (autoIdx < n) {
                const last = numBloques + 1;
                if (!porBloque[last]) porBloque[last] = [];
                porBloque[last].push({ ...automaticas[autoIdx++], _isAuto: true });
            }
        }

        function thumbHtml(img) {
            const cls = 'w-full h-14 object-cover rounded-lg border border-gray-200' + (img._isAuto ? ' opacity-70' : '');
            const label = img._isAuto ? '<p class="text-[9px] text-gray-400 text-right mt-0.5">auto</p>' : '';
            return img.src
                ? `<div><img src="${img.src}" class="${cls}">${label}</div>`
                : `<div><div class="w-full h-10 bg-gray-200 rounded-lg"></div>${label}</div>`;
        }

        let out = '';
        bloques.forEach((texto, i) => {
            const n = i + 1;
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

        const alFinal = [];
        Object.entries(porBloque).forEach(([p, imgs]) => {
            if (parseInt(p) > bloques.length) alFinal.push(...imgs);
        });
        if (alFinal.length > 0) {
            out += `<div class="flex items-center gap-2 py-1.5 border-t border-gray-100 mt-1">
                <span class="shrink-0 text-[10px] font-black text-gray-300 w-5 text-center">↓</span>
                <span class="text-[11px] text-gray-400 italic">Al final del artículo</span>
            </div>`;
            alFinal.forEach(img => {
                out += `<div class="pl-7 py-1">${thumbHtml(img)}</div>`;
            });
        }

        container.innerHTML = out;
    }

    quill.on('text-change', actualizarPreview);
    document.getElementById('galeria-grid')?.addEventListener('input', e => {
        if (e.target.type === 'number') actualizarPreview();
    });
    actualizarPreview();
});
</script>
