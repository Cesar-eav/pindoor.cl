{{-- Uso: @include('admin.partials._preview-link', ['modelo' => $revival, 'tipo' => 'revival']) --}}
<div class="w-[90vw] mx-auto bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4 flex items-center justify-between gap-4 flex-wrap">
    <div class="flex-1 min-w-64">
        <p class="text-xs font-black uppercase tracking-widest text-blue-600 mb-1">🔗 Link para compartir con el cliente</p>
        <div class="flex items-center gap-2">
            <input type="text" readonly value="{{ $modelo->preview_url }}" id="preview-link-input"
                   onclick="this.select()"
                   class="flex-1 px-3 py-2 border border-blue-200 rounded-lg text-xs bg-white text-gray-600 font-mono">
            <button type="button" onclick="copiarPreviewLink()" id="btn-copiar-preview"
                    class="px-3 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition shrink-0">Copiar</button>
        </div>
        <p class="text-[11px] text-blue-500 mt-1">
            @if($modelo->previewEstaVivo())
                Funciona mientras esté en borrador — se desactiva solo apenas lo publiques, y la gente que lo tenga guardado queda viendo la versión pública en vez del borrador.
            @else
                Ya está en vivo: este link quedó inactivo y redirige a la versión pública.
            @endif
        </p>
    </div>
    {{-- Botón plano + fetch, no un <form>: este partial vive dentro del <form>
         principal del editor y un <form> anidado ahí sería HTML inválido. --}}
    <button type="button" id="btn-regenerar-preview"
            onclick="regenerarPreviewLink()"
            data-url="{{ route('admin.preview-token.regenerar', ['tipo' => $tipo, 'id' => $modelo->id]) }}"
            class="px-4 py-2 border border-blue-300 text-blue-700 text-xs font-bold rounded-lg hover:bg-blue-100 transition whitespace-nowrap">
        ↻ Regenerar link
    </button>
</div>

<script>
function copiarPreviewLink() {
    var input = document.getElementById('preview-link-input');
    navigator.clipboard.writeText(input.value).then(function () {
        var btn = document.getElementById('btn-copiar-preview');
        var original = btn.textContent;
        btn.textContent = '¡Copiado!';
        setTimeout(function () { btn.textContent = original; }, 1500);
    });
}

function regenerarPreviewLink() {
    if (!confirm('¿Regenerar? El link anterior dejará de funcionar de inmediato, aunque lo tengan guardado.')) return;
    var btn = document.getElementById('btn-regenerar-preview');
    fetch(btn.dataset.url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
    })
    .then(function (res) { if (!res.ok) throw new Error(); return res; })
    .then(function () { window.location.reload(); })
    .catch(function () { alert('No se pudo regenerar el link. Intenta de nuevo.'); });
}
</script>
