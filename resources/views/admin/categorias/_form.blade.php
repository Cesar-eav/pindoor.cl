<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
    <input type="text" name="nombre" value="{{ old('nombre', $categoria->nombre ?? '') }}" required
           class="w-full px-4 py-2.5 border rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none {{ $errors->has('nombre') ? 'border-red-400' : 'border-gray-200' }}">
    @error('nombre') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
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
    <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo</label>
    <input type="text" name="tipo" value="{{ old('tipo', $categoria->tipo ?? '') }}"
           placeholder="gastronomia, cultura, naturaleza…"
           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Descripción</label>
    <textarea name="descripcion" rows="3"
              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none resize-none">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
</div>

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
