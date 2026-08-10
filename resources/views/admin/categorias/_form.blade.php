{{-- Tabs idioma --}}
<div class="flex items-center gap-2 flex-wrap">
    <button type="button" id="tab-es" onclick="setLang('es')"
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-[#fc5648] bg-[#fc5648] text-white transition">
        🇪🇸 Español
    </button>
    <button type="button" id="tab-en" onclick="setLang('en')"
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-gray-400 transition">
        🇬🇧 English
    </button>
    <button type="button" id="tab-fr" onclick="setLang('fr')"
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border border-gray-200 bg-white text-gray-500 hover:border-gray-400 transition">
        🇫🇷 Français
    </button>
    <button type="button" id="btn-autotraducir-en" onclick="autoTraducir('en')"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition ml-2">
        ✨ ES→EN
    </button>
    <button type="button" id="btn-autotraducir-fr" onclick="autoTraducir('fr')"
            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-bold border border-gray-200 bg-white text-gray-500 hover:border-blue-400 hover:text-blue-500 transition">
        ✨ ES→FR
    </button>
    <span id="traducir-estado" class="text-xs text-gray-400 hidden"></span>
</div>

<div data-lang-field="es">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre <span class="text-[#fc5648]">ES</span> <span class="text-red-500">*</span></label>
    <input id="nombre-es" type="text" name="nombre_es" value="{{ old('nombre_es', $categoria?->getTranslation('nombre','es',false)) }}" required
           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none {{ $errors->has('nombre_es') ? 'border-red-400' : 'border-gray-200' }}">
    @error('nombre_es') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
</div>
<div data-lang-field="en" style="display:none">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Name <span class="text-blue-500">EN</span></label>
    <input id="nombre-en" type="text" name="nombre_en" value="{{ old('nombre_en', $categoria?->getTranslation('nombre','en',false)) }}"
           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-400 outline-none">
</div>
<div data-lang-field="fr" style="display:none">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Nom <span class="text-indigo-500">FR</span></label>
    <input id="nombre-fr" type="text" name="nombre_fr" value="{{ old('nombre_fr', $categoria?->getTranslation('nombre','fr',false)) }}"
           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Icono</label>
    <input type="hidden" name="icono" id="icono-input" value="{{ old('icono', $categoria->icono ?? '') }}">

    {{-- Preview --}}
    <div class="flex items-center gap-3 mb-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
        <div id="icono-preview"
             class="w-10 h-10 rounded-lg border-2 border-[#fc5648] bg-white flex items-center justify-center text-[#fc5648] text-lg shrink-0">
            @if(old('icono', $categoria->icono ?? ''))
                <i class="fa-solid fa-{{ old('icono', $categoria->icono ?? '') }}"></i>
            @else
                <i class="fa-solid fa-tag text-gray-300"></i>
            @endif
        </div>
        <span class="text-sm text-gray-500">Seleccionado: <strong id="icono-name" class="font-mono text-gray-700">{{ old('icono', $categoria->icono ?? '—') }}</strong></span>
        <button type="button" onclick="clearIcono()"
                class="ml-auto text-xs text-gray-400 hover:text-red-500 transition px-2 py-1 rounded-lg hover:bg-red-50">✕ Quitar</button>
    </div>

    {{-- Buscador --}}
    <input type="text" id="icon-search" placeholder="🔍  Buscar icono…"
           oninput="filterIcons(this.value)"
           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm mb-2 focus:ring-2 focus:ring-[#fc5648] outline-none">

    {{-- Contador --}}
    <p class="text-xs text-gray-400 mb-2"><span id="icon-count"></span> iconos disponibles</p>

    {{-- Grid --}}
    <div id="icon-grid"
         class="grid grid-cols-6 sm:grid-cols-8 gap-1 border border-gray-100 rounded-xl p-2 bg-gray-50"
         style="max-height: 320px; overflow-y: auto;">
    </div>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Imagen de portada</label>
    <p class="text-xs text-gray-400 mb-2">Si subes una imagen, se usa en vez del ícono para mostrar esta categoría en el home. Si no subes nada, se usa el ícono de arriba.</p>

    @if($categoria->imagen_portada ?? false)
        <div class="mb-3">
            <img src="{{ asset('storage/' . $categoria->imagen_portada) }}"
                 alt="Imagen actual"
                 class="h-32 w-auto rounded-xl border border-gray-200 object-cover">
            <p class="text-xs text-gray-400 mt-1">Imagen actual. Sube una nueva para reemplazarla.</p>
        </div>
    @endif

    <input id="cat-imagen-input" type="file" name="imagen_portada" accept="image/*"
           class="block w-full text-sm text-gray-500
                  file:mr-4 file:py-2 file:px-4
                  file:rounded-xl file:border-0
                  file:text-sm file:font-bold
                  file:bg-[#fff0ef] file:text-[#fc5648]
                  hover:file:bg-[#ffe0dd] cursor-pointer">
    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WEBP — máx. 4 MB</p>

    <div id="cat-imagen-preview" class="mt-3 hidden">
        <img id="cat-imagen-preview-img" src="" alt="Preview"
             class="h-32 w-auto rounded-xl border border-[#fc5648]/40 object-cover">
    </div>
    @error('imagen_portada') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

    <div id="cat-mostrar-nombre-wrap" class="mt-3 {{ ($categoria->imagen_portada ?? false) ? '' : 'hidden' }}">
        <label class="flex items-center gap-2.5 cursor-pointer">
            <input type="hidden" name="mostrar_nombre_en_imagen" value="0">
            <input type="checkbox" name="mostrar_nombre_en_imagen" value="1" id="cat-mostrar-nombre"
                   {{ old('mostrar_nombre_en_imagen', $categoria->mostrar_nombre_en_imagen ?? true) ? 'checked' : '' }}
                   class="w-4 h-4 accent-[#fc5648] rounded">
            <span class="text-sm text-gray-700">Mostrar el nombre de la categoría sobre la imagen</span>
        </label>
    </div>
</div>

<script>
document.getElementById('cat-imagen-input').addEventListener('change', function () {
    const file = this.files[0];
    const wrap = document.getElementById('cat-imagen-preview');
    const img  = document.getElementById('cat-imagen-preview-img');
    const nombreWrap = document.getElementById('cat-mostrar-nombre-wrap');
    if (!file) { wrap.classList.add('hidden'); return; }
    img.src = URL.createObjectURL(file);
    img.onload = () => URL.revokeObjectURL(img.src);
    wrap.classList.remove('hidden');
    nombreWrap.classList.remove('hidden');
});
</script>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo</label>
    <input type="text" name="tipo" value="{{ old('tipo', $categoria->tipo ?? '') }}"
           placeholder="gastronomia, cultura, naturaleza…"
           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
</div>

<div data-lang-field="es">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción <span class="text-[#fc5648]">ES</span></label>
    <textarea id="descripcion-es" name="descripcion_es" rows="3"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ old('descripcion_es', $categoria?->getTranslation('descripcion','es',false)) }}</textarea>
</div>
<div data-lang-field="en" style="display:none">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Description <span class="text-blue-500">EN</span></label>
    <textarea id="descripcion-en" name="descripcion_en" rows="3"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-400 outline-none resize-none">{{ old('descripcion_en', $categoria?->getTranslation('descripcion','en',false)) }}</textarea>
</div>
<div data-lang-field="fr" style="display:none">
    <label class="block text-sm font-semibold text-gray-700 mb-1">Description <span class="text-indigo-500">FR</span></label>
    <textarea id="descripcion-fr" name="descripcion_fr" rows="3"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-400 outline-none resize-none">{{ old('descripcion_fr', $categoria?->getTranslation('descripcion','fr',false)) }}</textarea>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentLang = 'es';

    const TAB_ESTILOS = {
        es: 'border-[#fc5648] bg-[#fc5648] text-white',
        en: 'border-blue-500 bg-blue-500 text-white',
        fr: 'border-indigo-500 bg-indigo-500 text-white',
    };
    const TAB_INACTIVO = 'border-gray-200 bg-white text-gray-500 hover:border-gray-400';

    window.setLang = function(lang) {
        document.querySelectorAll('[data-lang-field]').forEach(el => {
            el.style.display = el.dataset.langField === lang ? '' : 'none';
        });

        Object.keys(TAB_ESTILOS).forEach(l => {
            const tab = document.getElementById('tab-' + l);
            if (!tab) return;
            tab.className = 'flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-bold border transition '
                + (l === lang ? TAB_ESTILOS[l] : TAB_INACTIVO);
        });

        currentLang = lang;
    };

    window.autoTraducir = async function(destino) {
        const btn    = document.getElementById('btn-autotraducir-' + destino);
        const estado = document.getElementById('traducir-estado');

        const nombreEs     = document.getElementById('nombre-es').value.trim();
        const descripcionEs = document.getElementById('descripcion-es').value.trim();

        if (!nombreEs && !descripcionEs) {
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
            if (nombreEs) {
                estado.textContent = 'Traduciendo nombre…';
                document.getElementById('nombre-' + destino).value = await traducirTexto(nombreEs);
            }
            if (descripcionEs) {
                estado.textContent = 'Traduciendo descripción…';
                document.getElementById('descripcion-' + destino).value = await traducirTexto(descripcionEs);
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
});
</script>

<script>
const ICONS = [
    // Gastronomía
    { n:'utensils',          l:'Restaurante' },
    { n:'hamburger',         l:'Comida rápida' },
    { n:'pizza-slice',       l:'Pizza' },
    { n:'hotdog',            l:'Hot dog' },
    { n:'bacon',             l:'Parrilla' },
    { n:'egg',               l:'Desayuno' },
    { n:'carrot',            l:'Vegetariano' },
    { n:'lemon',             l:'Jugos' },
    { n:'ice-cream',         l:'Helados' },
    { n:'cookie-bite',       l:'Pastelería' },
    { n:'cake-candles',      l:'Tortas' },
    { n:'bread-slice',       l:'Panadería' },
    { n:'cheese',            l:'Quesos' },
    { n:'fish',              l:'Mariscos' },
    { n:'shrimp',            l:'Pescados' },
    // Bebidas
    { n:'coffee',            l:'Café' },
    { n:'mug-hot',           l:'Té / Infusiones' },
    { n:'wine-glass-alt',    l:'Vino' },
    { n:'beer-mug-empty',    l:'Bar / Cerveza' },
    { n:'martini-glass',     l:'Coctelería' },
    { n:'bottle-water',      l:'Bebidas' },
    // Cultura / turismo
    { n:'camera',            l:'Mirador' },
    { n:'camera-retro',      l:'Fotografía' },
    { n:'monument',          l:'Monumento' },
    { n:'person',            l:'Estatua' },
    { n:'chess-knight',      l:'Escultura' },
    { n:'landmark',          l:'Museo' },
    { n:'landmark-dome',     l:'Patrimonio' },
    { n:'archway',           l:'Arquitectura' },
    { n:'building-columns',  l:'Edificio hist.' },
    { n:'building',          l:'Edificio' },
    { n:'city',              l:'Ciudad' },
    { n:'tower-observation', l:'Torre' },
    { n:'tower-broadcast',   l:'Torre' },
    { n:'bridge',            l:'Puente' },
    { n:'bridge-water',      l:'Puente río' },
    { n:'theater-masks',     l:'Teatro' },
    { n:'masks-theater',     l:'Artes escénicas' },
    { n:'paint-brush',       l:'Arte / Pintura' },
    { n:'palette',           l:'Galería de arte' },
    { n:'music',             l:'Música' },
    { n:'guitar',            l:'Guitarra' },
    { n:'drum',              l:'Percusión' },
    { n:'microphone',        l:'Concierto' },
    { n:'book',              l:'Biblioteca' },
    { n:'book-open',         l:'Centro cultural' },
    { n:'scroll',            l:'Historia' },
    { n:'graduation-cap',    l:'Educación' },
    { n:'university',        l:'Universidad' },
    { n:'film',              l:'Cine' },
    { n:'clapperboard',      l:'Cine / Filmación' },
    { n:'photo-film',        l:'Audiovisual' },
    { n:'chess',             l:'Juegos de mesa' },
    { n:'puzzle-piece',      l:'Entretenimiento' },
    { n:'gamepad',           l:'Videojuegos' },
    { n:'dice',              l:'Casino' },
    { n:'masks-theater',     l:'Espectáculo' },
    // Religión / patrimonio
    { n:'church',            l:'Iglesia' },
    { n:'place-of-worship',  l:'Lugar de culto' },
    { n:'cross',             l:'Capilla' },
    { n:'star-of-david',     l:'Sinagoga' },
    { n:'moon',              l:'Mezquita' },
    // Naturaleza / outdoor
    { n:'leaf',              l:'Naturaleza' },
    { n:'tree',              l:'Parque / Bosque' },
    { n:'seedling',          l:'Jardín' },
    { n:'mountain',          l:'Montaña' },
    { n:'mountain-sun',      l:'Cerro' },
    { n:'volcano',           l:'Volcán' },
    { n:'water',             l:'Lago / Río' },
    { n:'droplet',           l:'Cascada' },
    { n:'umbrella-beach',    l:'Playa' },
    { n:'sun',               l:'Sol / Verano' },
    { n:'snowflake',         l:'Nieve / Invierno' },
    { n:'cloud',             l:'Clima' },
    { n:'wind',              l:'Viento' },
    { n:'fire',              l:'Tendencia / Calor' },
    // Deporte / aventura
    { n:'hiking',            l:'Senderismo' },
    { n:'person-hiking',     l:'Trekking' },
    { n:'bicycle',           l:'Ciclismo' },
    { n:'person-biking',     l:'Bicicleta' },
    { n:'football',          l:'Fútbol' },
    { n:'basketball',        l:'Básquetbol' },
    { n:'volleyball',        l:'Vóleibol' },
    { n:'tennis-ball',       l:'Tenis' },
    { n:'golf-ball-tee',     l:'Golf' },
    { n:'person-swimming',   l:'Natación' },
    { n:'person-running',    l:'Atletismo' },
    { n:'horse',             l:'Equitación' },
    { n:'dumbbell',          l:'Gimnasio' },
    { n:'person-skiing',     l:'Ski' },
    { n:'kiwi-bird',         l:'Observación aves' },
    // Alojamiento
    { n:'bed',               l:'Alojamiento' },
    { n:'hotel',             l:'Hotel' },
    { n:'campground',        l:'Camping' },
    { n:'house',             l:'Hospedaje' },
    { n:'house-chimney',     l:'Cabaña' },
    // Transporte
    { n:'car',               l:'Auto' },
    { n:'bus',               l:'Bus / Micro' },
    { n:'taxi',              l:'Taxi' },
    { n:'train',             l:'Tren' },
    { n:'ship',              l:'Barco / Ferry' },
    { n:'plane',             l:'Avión' },
    { n:'helicopter',        l:'Helicóptero' },
    { n:'bicycle',           l:'Bicicleta' },
    { n:'gas-pump',          l:'Gasolinera' },
    { n:'road',              l:'Ruta' },
    // Servicios / salud
    { n:'hospital',          l:'Hospital' },
    { n:'stethoscope',       l:'Clínica' },
    { n:'pills',             l:'Farmacia' },
    { n:'tooth',             l:'Dentista' },
    { n:'spa',               l:'Spa / Bienestar' },
    { n:'scissors',          l:'Peluquería' },
    // Compras
    { n:'shopping-bag',      l:'Tienda / Boutique' },
    { n:'shopping-cart',     l:'Mercado' },
    { n:'store',             l:'Local comercial' },
    { n:'gift',              l:'Souvenirs / Regalos' },
    { n:'gem',               l:'Joyería' },
    // Misceláneos
    { n:'map-marker-alt',    l:'Punto de interés' },
    { n:'map',               l:'Mapa' },
    { n:'compass',           l:'Explorar' },
    { n:'globe',             l:'Internacional' },
    { n:'flag',              l:'Bandera / Hito' },
    { n:'star',              l:'Destacado' },
    { n:'heart',             l:'Favorito' },
    { n:'tag',               l:'Etiqueta' },
    { n:'info-circle',       l:'Información' },
    { n:'child',             l:'Para niños' },
    { n:'dog',               l:'Pet-friendly' },
    { n:'wifi',              l:'Internet / WiFi' },
    { n:'parking',           l:'Estacionamiento' },
    { n:'toilet',            l:'Baños públicos' },
    { n:'wheelchair',        l:'Accesible' },
    { n:'clock',             l:'Horarios' },
    { n:'ticket',            l:'Entradas' },
    { n:'money-bill',        l:'Economía / Precios' },
    { n:'percent',           l:'Descuentos' },
    { n:'calendar',          l:'Agenda / Eventos' },
    { n:'calendar-days',     l:'Temporada' },
];

let allIcons = [...ICONS];
let currentValue = document.getElementById('icono-input').value;

function buildGrid(list) {
    const grid = document.getElementById('icon-grid');
    document.getElementById('icon-count').textContent = list.length;
    grid.innerHTML = '';
    list.forEach(icon => {
        const selected = icon.n === currentValue;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.title = icon.l;
        btn.dataset.name = icon.n;
        btn.className = [
            'icon-btn flex flex-col items-center justify-center gap-1 p-2 rounded-lg border transition-all cursor-pointer',
            selected
                ? 'border-[#fc5648] bg-[#fff5f4] text-[#fc5648] shadow-sm'
                : 'border-transparent bg-white hover:border-gray-200 text-gray-500 hover:text-gray-700'
        ].join(' ');
        btn.innerHTML = `<i class="fa-solid fa-${icon.n} text-base leading-none"></i><span class="text-[9px] leading-tight text-center truncate w-full">${icon.l}</span>`;
        btn.onclick = () => selectIcono(icon.n, icon.l);
        grid.appendChild(btn);
    });
}

function selectIcono(name) {
    currentValue = name;
    document.getElementById('icono-input').value = name;
    document.getElementById('icono-name').textContent = name;
    document.getElementById('icono-preview').innerHTML = `<i class="fa-solid fa-${name}"></i>`;
    document.querySelectorAll('.icon-btn').forEach(b => {
        const active = b.dataset.name === name;
        b.className = [
            'icon-btn flex flex-col items-center justify-center gap-1 p-2 rounded-lg border transition-all cursor-pointer',
            active
                ? 'border-[#fc5648] bg-[#fff5f4] text-[#fc5648] shadow-sm'
                : 'border-transparent bg-white hover:border-gray-200 text-gray-500 hover:text-gray-700'
        ].join(' ');
    });
}

function clearIcono() {
    currentValue = '';
    document.getElementById('icono-input').value = '';
    document.getElementById('icono-name').textContent = '—';
    document.getElementById('icono-preview').innerHTML = '<i class="fa-solid fa-tag text-gray-300"></i>';
    document.querySelectorAll('.icon-btn').forEach(b => {
        b.className = 'icon-btn flex flex-col items-center justify-center gap-1 p-2 rounded-lg border transition-all cursor-pointer border-transparent bg-white hover:border-gray-200 text-gray-500 hover:text-gray-700';
    });
}

function filterIcons(query) {
    const q = query.toLowerCase().trim();
    buildGrid(q ? allIcons.filter(i => i.n.includes(q) || i.l.toLowerCase().includes(q)) : allIcons);
}

document.addEventListener('DOMContentLoaded', () => buildGrid(allIcons));
</script>

@php
    $catalogo = \App\Models\PuntoInteres::catalogoModulos();
    // groupBy no preserva claves string — se añade '_key' a cada item antes de agrupar
    $grupos   = collect($catalogo)->map(fn($mod, $key) => array_merge($mod, ['_key' => $key]))->groupBy('grupo');
    $actuales = old('modulos_defecto', $categoria->modulos_defecto ?? []);
@endphp

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Uso de la categoría</label>
    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition
                  {{ old('es_cliente', $categoria->es_cliente ?? false) ? 'border-[#fc5648] bg-[#fff5f4]' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
        <input type="checkbox" name="es_cliente" value="1"
               class="shrink-0 accent-[#fc5648]"
               {{ old('es_cliente', $categoria->es_cliente ?? false) ? 'checked' : '' }}>
        <div>
            <span class="text-sm font-semibold text-gray-800">Disponible para negocios (clientes)</span>
            <p class="text-xs text-gray-400 mt-0.5">Los negocios registrados en Pindoor podrán elegir esta categoría.</p>
        </div>
    </label>
</div>

<div>
    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition
                  {{ old('disponible_para_operadores', $categoria->disponible_para_operadores ?? false) ? 'border-teal-500 bg-teal-50' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
        <input type="checkbox" name="disponible_para_operadores" value="1"
               class="shrink-0 accent-teal-600"
               {{ old('disponible_para_operadores', $categoria->disponible_para_operadores ?? false) ? 'checked' : '' }}>
        <div>
            <span class="text-sm font-semibold text-gray-800">Disponible para operadores turísticos</span>
            <p class="text-xs text-gray-400 mt-0.5">Los operadores turísticos podrán asociarse a puntos de esta categoría (miradores, museos, etc.).</p>
        </div>
    </label>
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">Módulos activos por defecto</label>
    <p class="text-xs text-gray-400 mb-3">Se asignan automáticamente cuando se activa un negocio en esta categoría.</p>

    <div class="space-y-4">
        @foreach($grupos as $grupo => $modulos)
        <div>
            <p class="text-[11px] font-bold uppercase tracking-wide text-gray-400 mb-1.5">{{ $grupo }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($modulos as $mod)
                @php $modKey = $mod['_key']; @endphp
                <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition
                              {{ in_array($modKey, $actuales) ? 'border-[#fc5648] bg-[#fff5f4]' : 'border-gray-200 bg-gray-50 hover:border-gray-300' }}">
                    <input type="checkbox" name="modulos_defecto[]" value="{{ $modKey }}"
                           class="mt-0.5 shrink-0 accent-[#fc5648]"
                           {{ in_array($modKey, $actuales) ? 'checked' : '' }}>
                    <div>
                        <span class="text-sm font-semibold text-gray-800">{{ $mod['emoji'] }} {{ $mod['label'] }}</span>
                        <p class="text-xs text-gray-400 leading-tight mt-0.5">{{ $mod['desc'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
