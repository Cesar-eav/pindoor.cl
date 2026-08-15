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

    const quill = new Quill('#revival-editor', {
        theme: 'snow',
        placeholder: 'Cuenta cómo estuvo el evento...',
        modules: {
            toolbar: {
                container: toolbarOptions,
                handlers: { image: imageHandler },
            },
        },
    });

    // Cargar contenido ES por defecto
    const hiddenEs = document.getElementById('contenido_es');
    if (hiddenEs.value.trim()) {
        quill.clipboard.dangerouslyPasteHTML(hiddenEs.value);
    }

    // Sincronizar al enviar + spinner
    document.getElementById('revival-form').addEventListener('submit', function (e) {
        const activoId = 'contenido_' + currentLang;
        document.getElementById(activoId).value = quill.root.innerHTML;

        const submitter = e.submitter;
        if (submitter && submitter.name === 'accion') {
            let accionInput = document.getElementById('accion-input');
            if (!accionInput) {
                accionInput = document.createElement('input');
                accionInput.type = 'hidden';
                accionInput.name = 'accion';
                accionInput.id = 'accion-input';
                this.appendChild(accionInput);
            }
            accionInput.value = submitter.value;
        }

        const btn        = document.getElementById('guardar-btn');
        const btnSeguir  = document.getElementById('guardar-seguir-btn');
        const spinner    = document.getElementById('guardar-spinner');
        [btn, btnSeguir].forEach(b => {
            b.disabled = true;
            b.classList.add('opacity-50', 'cursor-not-allowed');
        });
        spinner.style.display = 'flex';
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
                const res  = await fetch('{{ route('admin.revival.imagen') }}', {
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

    // ── Cambio de idioma ────────────────────────────────────────────
    let currentLang = 'es';

    const TAB_ESTILOS = {
        es: { activo: 'border-[#fc5648] bg-[#fc5648] text-white', color: '#fc5648' },
        en: { activo: 'border-blue-500 bg-blue-500 text-white',   color: '#3b82f6' },
        fr: { activo: 'border-indigo-500 bg-indigo-500 text-white', color: '#6366f1' },
    };
    const TAB_INACTIVO = 'border-gray-200 bg-white text-gray-500 hover:border-gray-400';

    window.setLang = function(lang) {
        // Guardar contenido del editor en el textarea del idioma actual
        document.getElementById('contenido_' + currentLang).value = quill.root.innerHTML;

        // Mostrar/ocultar campos
        document.querySelectorAll('[data-lang-field]').forEach(el => {
            el.style.display = el.dataset.langField === lang ? '' : 'none';
        });

        // Cargar contenido del nuevo idioma en el editor
        const nuevoContenido = document.getElementById('contenido_' + lang).value;
        quill.setContents([]);
        if (nuevoContenido.trim()) {
            quill.clipboard.dangerouslyPasteHTML(nuevoContenido);
        }

        // Actualizar label del editor
        const label = document.getElementById('editor-lang-label');
        if (label) {
            label.textContent = lang.toUpperCase();
            label.style.color = TAB_ESTILOS[lang].color;
        }

        // Estilos de los tabs
        Object.keys(TAB_ESTILOS).forEach(l => {
            const tab = document.getElementById('tab-' + l);
            if (!tab) return;
            tab.className = 'flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border transition '
                + (l === lang ? TAB_ESTILOS[l].activo : TAB_INACTIVO);
        });

        currentLang = lang;
    };

    // ── Auto-traducir ES → EN / FR ───────────────────────────────────
    window.autoTraducir = async function(destino) {
        const btn    = document.getElementById('btn-autotraducir-' + destino);
        const estado = document.getElementById('traducir-estado');

        // Guardar contenido ES actual
        document.getElementById('contenido_es').value = quill.root.innerHTML;

        const tituloEs = document.getElementById('titulo-es').value.trim();
        const textoEs  = quill.getText().trim();

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
                estado.textContent = 'Traduciendo título…';
                document.getElementById('titulo-' + destino).value = await traducirTexto(tituloEs);
            }
            if (textoEs) {
                const traducido = await traducirTexto(textoEs);
                const oraciones = traducido.match(/[^.!?]+[.!?]+/g) || [traducido];
                let htmlDestino = '';
                let buf = '';
                oraciones.forEach((o, i) => {
                    buf += o + ' ';
                    if (buf.length > 350 || i === oraciones.length - 1) {
                        htmlDestino += '<p>' + buf.trim() + '</p>';
                        buf = '';
                    }
                });
                document.getElementById('contenido_' + destino).value = htmlDestino;
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

    // ── Slug automático desde título ────────────────────────────────
    const tituloInput = document.getElementById('titulo-es');
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

        // Agrupar posicionadas manualmente
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

        // Calcular posiciones de auto-distribuidas (mismo algoritmo que PHP)
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

        // Imágenes con posición más allá del último bloque → van al final
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
    // Actualizar preview cuando se selecciona una imagen nueva en la galería
    document.getElementById('galeria-grid')?.addEventListener('change', e => {
        if (e.target.type === 'file') setTimeout(actualizarPreview, 200);
    });
    actualizarPreview();
});
</script>
