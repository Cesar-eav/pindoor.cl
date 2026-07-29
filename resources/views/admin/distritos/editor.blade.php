<x-admin-layout>
<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editor de Distritos</h2>
        <a href="{{ route('admin.stats') }}" class="text-sm text-gray-500 hover:text-gray-700 font-semibold">← Volver</a>
    </div>
</x-slot>

@push('head')
    @vite('resources/js/distrito-editor.js')
@endpush

<div class="flex h-[calc(100vh-64px)] overflow-hidden">

    {{-- ── Panel lateral ──────────────────────────────────────────────────── --}}
    <div class="w-80 shrink-0 bg-white border-r border-gray-200 flex flex-col overflow-hidden">

        {{-- Header panel --}}
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-3">Distritos</p>

            {{-- Crear nuevo --}}
            <div class="space-y-2" id="form-nuevo">
                <input type="text" id="nuevo-nombre" placeholder="Nombre del distrito"
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#fc5648] outline-none">
                <div class="flex items-center gap-2">
                    <input type="color" id="nuevo-color" value="#10b981"
                        class="w-9 h-9 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                    <span class="text-xs text-gray-400 flex-1">Color del distrito</span>
                </div>
                <button onclick="crearDistrito()"
                    class="w-full bg-gray-900 text-white text-sm font-bold py-2.5 rounded-xl hover:bg-black transition">
                    + Crear y dibujar
                </button>
            </div>
        </div>

        {{-- Lista de distritos --}}
        <div class="flex-1 overflow-y-auto px-3 py-3 space-y-2" id="lista-distritos">
            {{-- Se rellena dinámicamente --}}
        </div>

        {{-- Instrucciones --}}
        <div class="px-5 py-4 border-t border-gray-100 text-[11px] text-gray-400 leading-snug space-y-1">
            <p><strong class="text-gray-600">Dibujar:</strong> Clic para añadir vértice · Doble clic para cerrar · Botón ← para borrar el último</p>
            <p><strong class="text-gray-600">Editar:</strong> Arrastra vértices · <strong class="text-red-400">Clic derecho</strong> sobre un vértice para eliminarlo</p>
            <p><strong class="text-gray-600">Guardar:</strong> Pulsa 💾 cuando termines</p>
        </div>
    </div>

    {{-- ── Mapa ────────────────────────────────────────────────────────────── --}}
    <div class="flex-1 relative">
        <div id="mapa-editor" class="w-full h-full"></div>

        {{-- Botón flotante deshacer vértice (solo visible al dibujar) --}}
        <button id="btn-deshacer" onclick="deshacerVertice()"
            style="display:none"
            class="absolute bottom-20 left-1/2 -translate-x-1/2 z-500
                   bg-white border border-gray-200 shadow-lg rounded-2xl
                   px-5 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50
                   items-center gap-2 transition">
            ← Borrar último vértice
        </button>

        {{-- Toast feedback --}}
        <div id="toast" class="hidden absolute bottom-6 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-sm font-semibold px-5 py-2.5 rounded-2xl shadow-lg z-500 transition-opacity"></div>
    </div>

</div>

<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const BASE   = '/admin/distritos';

let mapa, distritos = {}, activoId = null;

// ── Init mapa ─────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    mapa = L.map('mapa-editor', {
        center: [-33.0457, -71.6197],
        zoom: 14,
        zoomControl: true,
        attributionControl: false,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, subdomains: 'abcd',
    }).addTo(mapa);

    mapa.pm.setLang('es');
    mapa.pm.setGlobalOptions({ snappable: true, snapDistance: 10 });

    // Al terminar de dibujar un polígono nuevo
    mapa.on('pm:create', e => {
        document.getElementById('btn-deshacer').style.display = 'none';
        if (!activoId) { e.layer.remove(); return; }
        const d = distritos[activoId];
        if (d.layer) d.layer.remove();
        d.layer = e.layer;
        d.layer.setStyle(estiloDistrito(d.color));
        d.layer.on('pm:edit', () => {});
        mapa.pm.disableDraw();
        toast('Polígono dibujado — guarda los cambios');
    });

    cargarTodos();
});

// ── Helpers ───────────────────────────────────────────────────────────────
function estiloDistrito(color) {
    return { color, weight: 2, opacity: 0.8, fillColor: color, fillOpacity: 0.15, dashArray: '5,4' };
}

function toast(msg, dur = 2500) {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.classList.remove('hidden');
    clearTimeout(toast._t);
    toast._t = setTimeout(() => el.classList.add('hidden'), dur);
}

function coordsDeLayer(layer) {
    return layer.getLatLngs()[0].map(ll => [ll.lat, ll.lng]);
}

// ── Cargar todos desde DB ─────────────────────────────────────────────────
function cargarTodos() {
    fetch('/api/distritos?todos=1')
        .then(r => r.json())
        .then(data => {
            // Incluir inactivos para el editor — usamos endpoint admin
            fetch(`${BASE}?json=1`, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF } })
                .then(r => r.json())
                .then(lista => renderLista(lista))
                .catch(() => renderLista(data));
        })
        .catch(() => {});

    // Simplificado: cargar desde endpoint público y renderizar
    fetch('/api/distritos')
        .then(r => r.json())
        .then(lista => renderLista(lista));
}

function renderLista(lista) {
    document.getElementById('lista-distritos').innerHTML = '';
    distritos = {};

    lista.forEach(d => {
        distritos[d.id] = { ...d, layer: null };

        if (d.coordenadas && d.coordenadas.length > 2) {
            const layer = L.polygon(d.coordenadas, estiloDistrito(d.color)).addTo(mapa);
            layer.on('pm:edit', () => {});
            distritos[d.id].layer = layer;
        }

        renderTarjeta(d);
    });
}

function renderTarjeta(d) {
    const lista = document.getElementById('lista-distritos');
    const tarjeta = document.createElement('div');
    tarjeta.id = `tarjeta-${d.id}`;
    tarjeta.className = 'bg-gray-50 rounded-2xl p-3 border border-gray-100';
    tarjeta.innerHTML = `
        <div class="flex items-center gap-2 mb-2">
            <span class="w-3 h-3 rounded-full shrink-0" style="background:${d.color}"></span>
            <input type="text" value="${d.nombre}" id="nombre-${d.id}"
                class="flex-1 text-sm font-bold bg-transparent border-0 outline-none focus:bg-white focus:border focus:border-gray-200 focus:rounded-lg focus:px-2 py-0.5 transition-all">
            <input type="color" value="${d.color}" id="color-${d.id}"
                onchange="cambiarColor(${d.id}, this.value)"
                class="w-7 h-7 rounded-lg border border-gray-200 cursor-pointer p-0.5 shrink-0">
        </div>
        <div class="flex gap-1.5">
            <button onclick="activarDibujo(${d.id})" title="Dibujar polígono"
                class="flex-1 py-1.5 rounded-xl border border-gray-200 text-gray-600 hover:border-[#fc5648] hover:text-[#fc5648] transition flex items-center justify-center" id="btn-dibujar-${d.id}">
                <i class="fa fa-pen text-xs"></i>
            </button>
            <button onclick="activarEdicion(${d.id})" title="Mover vértices"
                class="flex-1 py-1.5 rounded-xl border border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-500 transition flex items-center justify-center">
                <i class="fa fa-arrows-up-down-left-right text-xs"></i>
            </button>
            <button onclick="guardarDistrito(${d.id})" title="Guardar" id="btn-guardar-${d.id}"
                class="flex-1 py-1.5 rounded-xl bg-gray-900 text-white hover:bg-black transition flex items-center justify-center">
                <i class="fa fa-floppy-disk text-xs" id="ico-guardar-${d.id}"></i>
                <svg id="spin-guardar-${d.id}" class="hidden animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
            </button>
            <button onclick="eliminarDistrito(${d.id})" title="Eliminar"
                class="py-1.5 px-2.5 rounded-xl border border-red-100 text-red-400 hover:bg-red-50 transition flex items-center justify-center">
                <i class="fa fa-trash text-xs"></i>
            </button>
        </div>
        <p class="text-[10px] text-gray-400 mt-1.5" id="vertices-${d.id}">${d.coordenadas ? d.coordenadas.length + ' vértices' : 'Sin polígono'}</p>
    `;
    lista.appendChild(tarjeta);
}

// ── Crear ─────────────────────────────────────────────────────────────────
function crearDistrito() {
    const nombre = document.getElementById('nuevo-nombre').value.trim();
    const color  = document.getElementById('nuevo-color').value;
    if (!nombre) { document.getElementById('nuevo-nombre').focus(); return; }

    fetch(`${BASE}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ nombre, color, activo: true }),
    })
    .then(r => r.json())
    .then(d => {
        distritos[d.id] = { ...d, layer: null };
        renderTarjeta(d);
        document.getElementById('nuevo-nombre').value = '';
        activarDibujo(d.id);
        toast(`Distrito "${d.nombre}" creado — dibuja el polígono en el mapa`);
    });
}

// ── Activar dibujo ────────────────────────────────────────────────────────
function activarDibujo(id) {
    mapa.pm.disableDraw();
    mapa.pm.disableGlobalEditMode();
    activoId = id;

    // Highlight tarjeta activa
    document.querySelectorAll('[id^="tarjeta-"]').forEach(el => el.classList.remove('ring-2','ring-[#fc5648]'));
    document.getElementById(`tarjeta-${id}`)?.classList.add('ring-2','ring-[#fc5648]');

    mapa.pm.enableDraw('Polygon', {
        snappable: true,
        allowSelfIntersection: false,
        finishOn: 'dblclick',
        templineStyle: { color: distritos[id]?.color || '#fc5648', weight: 2 },
        hintlineStyle: { color: distritos[id]?.color || '#fc5648', weight: 1, dashArray: '4,4' },
        pathOptions: estiloDistrito(distritos[id]?.color || '#fc5648'),
    });

    document.getElementById('btn-deshacer').style.display = 'flex';
    toast('Haz clic para añadir vértices · Doble clic para cerrar');
}

// ── Activar edición (mover vértices) ─────────────────────────────────────
function activarEdicion(id) {
    mapa.pm.disableDraw();
    mapa.pm.disableGlobalEditMode();
    activoId = id;

    const d = distritos[id];
    if (!d?.layer) { toast('Primero dibuja el polígono'); return; }

    // Quitar y re-agregar la capa para asegurar que pm la reconoce
    d.layer.remove();
    d.layer.addTo(mapa);
    d.layer.pm.enable({
        allowSelfIntersection: false,
        draggable: true,
        addVertexOn: 'click',
        removeVertexOn: 'contextmenu',
    });

    document.querySelectorAll('[id^="tarjeta-"]').forEach(el => el.classList.remove('ring-2','ring-blue-400'));
    document.getElementById(`tarjeta-${id}`)?.classList.add('ring-2','ring-blue-400');

    toast('Arrastra vértices · Clic en borde = nuevo vértice · Clic derecho = borrar');
}

// ── Guardar ───────────────────────────────────────────────────────────────
function guardarDistrito(id) {
    const d      = distritos[id];
    const nombre = document.getElementById(`nombre-${id}`)?.value.trim() || d.nombre;
    const color  = document.getElementById(`color-${id}`)?.value || d.color;
    const coords = d.layer ? coordsDeLayer(d.layer) : d.coordenadas;

    if (d.layer) d.layer.pm.disable();
    mapa.pm.disableDraw();

    const ico  = document.getElementById(`ico-guardar-${id}`);
    const spin = document.getElementById(`spin-guardar-${id}`);
    if (ico)  ico.classList.add('hidden');
    if (spin) spin.classList.remove('hidden');

    fetch(`${BASE}/${id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ nombre, color, coordenadas: coords }),
    })
    .then(r => r.json())
    .then(updated => {
        distritos[id] = { ...distritos[id], ...updated };
        const el = document.getElementById(`vertices-${id}`);
        if (el) el.textContent = coords ? coords.length + ' vértices guardados ✓' : 'Sin polígono';
        document.querySelectorAll('[id^="tarjeta-"]').forEach(el => el.classList.remove('ring-2','ring-[#fc5648]','ring-blue-400'));
        toast('Guardado ✓');
    })
    .finally(() => {
        if (ico)  ico.classList.remove('hidden');
        if (spin) spin.classList.add('hidden');
    });
}

// ── Cambiar color ─────────────────────────────────────────────────────────
function cambiarColor(id, color) {
    distritos[id].color = color;
    const dot = document.querySelector(`#tarjeta-${id} span.rounded-full`);
    if (dot) dot.style.background = color;
    if (distritos[id].layer) distritos[id].layer.setStyle(estiloDistrito(color));
}

// ── Deshacer último vértice al dibujar ────────────────────────────────────
function deshacerVertice() {
    const draw = mapa.pm.Draw.Polygon;
    if (draw && draw._enabled) {
        draw._removeLastVertex();
    }
}

// ── Eliminar ──────────────────────────────────────────────────────────────
function eliminarDistrito(id) {
    if (!confirm('¿Eliminar este distrito?')) return;
    fetch(`${BASE}/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF },
    })
    .then(() => {
        if (distritos[id]?.layer) distritos[id].layer.remove();
        document.getElementById(`tarjeta-${id}`)?.remove();
        delete distritos[id];
        toast('Distrito eliminado');
    });
}
</script>

</x-admin-layout>
