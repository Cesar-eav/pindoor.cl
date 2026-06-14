<script>
const PUNTOS_DATA = @json($puntosMapData);
const GPS_LAT     = {{ request('lat') ? (float) request('lat') : 'null' }};
const GPS_LNG     = {{ request('lng') ? (float) request('lng') : 'null' }};

// ── Vista Listado / Mapa ─────────────────────────────────────────────────
let mapaIniciado  = false;
let mapaLeaflet   = null;
let markerGroup   = null;

function setView(vista) {
    const mobile = window.innerWidth < 768;

    const elListado  = document.getElementById('vista-listado');
    const elMapa     = document.getElementById('vista-mapa');
    const elListadoM = document.getElementById('vista-listado-mobile');
    const elMapaM    = document.getElementById('vista-mapa-mobile');
    const btnL = document.getElementById('btn-listado');
    const btnM = document.getElementById('btn-mapa');

    const navInicio = document.getElementById('nav-inicio');
    const navMapa   = document.getElementById('nav-explorar-mapa');
    const activeNav   = ['bg-[#fff0ef]', 'text-[#fc5648]'];
    const inactiveNav = ['text-gray-600', 'hover:bg-gray-50', 'hover:text-gray-900'];
    if (vista === 'mapa') {
        navInicio?.classList.remove(...activeNav);
        navInicio?.classList.add(...inactiveNav);
        navMapa?.classList.add(...activeNav);
        navMapa?.classList.remove(...inactiveNav);
    } else {
        navMapa?.classList.remove(...activeNav);
        navMapa?.classList.add(...inactiveNav);
        navInicio?.classList.add(...activeNav);
        navInicio?.classList.remove(...inactiveNav);
    }

    const elPanoramasM     = document.getElementById('panoramas-mobile-section');
    const elBlogM         = document.getElementById('blog-mobile-section');
    const elExperienciasM = document.getElementById('experiencias-mobile-section');
    const elFiltroM       = document.getElementById('filtro-activo-mobile');

    if (vista === 'mapa') {
        if (mobile) {
            elListadoM?.classList.add('hidden');
            elMapaM?.classList.remove('hidden');
            elMapaM?.classList.add('flex');
            elPanoramasM?.classList.add('hidden');
            elBlogM?.classList.add('hidden');
            elExperienciasM?.classList.add('hidden');
            elFiltroM?.classList.add('hidden');
        } else {
            elListado?.classList.add('hidden');
            elMapa?.classList.remove('hidden');
            btnM?.classList.add('bg-white', 'shadow', 'text-[#fc5648]');
            btnM?.classList.remove('text-gray-500', 'hover:text-gray-700');
            btnL?.classList.remove('bg-white', 'shadow', 'text-[#fc5648]');
            btnL?.classList.add('text-gray-500', 'hover:text-gray-700');
        }
        const containerId = mobile ? 'mapa-mobile' : 'mapa-principal';
        if (!mapaIniciado) {
            mapaIniciado = true;
            void document.getElementById(containerId)?.offsetHeight;
            iniciarMapa(containerId);
        }
        setTimeout(() => {
            mapaLeaflet?.invalidateSize();
            if (GPS_LAT && GPS_LNG) {
                mapaLeaflet.flyTo([GPS_LAT, GPS_LNG], 18, { duration: 1 });
            }
        }, 150);
    } else {
        if (mobile) {
            elMapaM?.classList.add('hidden');
            elMapaM?.classList.remove('flex');
            elListadoM?.classList.remove('hidden');
            elPanoramasM?.classList.remove('hidden');
            elBlogM?.classList.remove('hidden');
            elExperienciasM?.classList.remove('hidden');
            elFiltroM?.classList.remove('hidden');
        } else {
            elMapa?.classList.add('hidden');
            elListado?.classList.remove('hidden');
            btnL?.classList.add('bg-white', 'shadow', 'text-[#fc5648]');
            btnL?.classList.remove('text-gray-500', 'hover:text-gray-700');
            btnM?.classList.remove('bg-white', 'shadow', 'text-[#fc5648]');
            btnM?.classList.add('text-gray-500', 'hover:text-gray-700');
        }
    }
}

// ── Leaflet ──────────────────────────────────────────────────────────────
const EMOJI_CAT = {
    1: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M10 19H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-6"/><path d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M12 19v3M9 22h6"/></svg>`,
    2: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#854d0e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><path d="M6 2v2M10 2v2M14 2v2"/></svg>`,
    3: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#d946ef" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M12 2v3m-3-3h6M9 9h6v11a2 2 0 0 1-2 2h-2a2 2 0 0 1-2-2V9Z"/><path d="M9 13h6M17 5a2 2 0 0 1 2 2M18 10a1 1 0 0 1 1 1"/></svg>`,
    4: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><circle cx="12" cy="12" r="4"/></svg>`,
    5: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><path d="M9 9h.01M15 9h.01"/></svg>`,
    6: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M12 22V12m0 0a5 5 0 1 0-5-5M12 12a5 5 0 1 1 5-5"/><path d="M12 12a5 5 0 0 1-5 5M12 12a5 5 0 0 0 5 5"/></svg>`,
    7: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0f766e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="m3 9 9-7 9 7"/><path d="M5 22V11M9 22V11M15 22V11M19 22V11"/><path d="M2 22h20"/></svg>`,
    8: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a6 6 0 0 0 1.2 3.6l.6.8A6 6 0 0 1 17 13v8a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-8a6 6 0 0 1 1.2-3.6l.6-.8A6 6 0 0 0 10 5z"/><path d="M17 13h-4a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h4"/></svg>`,
    9: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M2 22h20"/><path d="M12 2v2"/><path d="m12 4-4 14"/><path d="m12 4 4 14"/><path d="M9 14h6"/></svg>`,
    10: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#be123c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M8 22h8M12 15v7M12 15a5 5 0 0 0 5-5V2H7v8a5 5 0 0 0 5 5Z"/><path d="M7 8h10"/></svg>`,
    11: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v3a2 2 0 0 0 2 2h3"/></svg>`,
    12: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#14b8a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M7 21a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2"/><path d="M10 17v4h4v-4"/><path d="M8 8.5V11a4 4 0 0 0 4 4 4 4 0 0 0 4-4V8.5Z"/><path d="M18 10V5a2 2 0 0 0-2-2h-2"/><path d="M12 2l-1 2h2z" fill="currentColor"/><path d="M10 7a2 2 0 1 1 4 0"/></svg>`,
    13: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;display:block;"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10Z"/><path d="m12 14-3 3.5 1-4.5-4-1.5 4.5-.5L12 7l1.5 4 4.5.5-4 1.5 1 4.5Z"/></svg>`,
};

function crearIcono(p) {
    const emoji  = EMOJI_CAT[p.categoria_id] || (p.es_cliente ? '🏪' : '📍');
    const border = p.es_cliente ? '#fc5648' : '#9ca3af';
    return L.divIcon({
        className: '',
        html: `<div style="display:flex;flex-direction:column;align-items:center;">
            <div style="background:white;border:2.5px solid ${border};border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-size:17px;box-shadow:0 2px 10px rgba(0,0,0,.22);line-height:1;">${emoji}</div>
            <div style="width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid ${border};margin-top:-1px;"></div>
        </div>`,
        iconSize:    [34, 41],
        iconAnchor:  [17, 41],
        popupAnchor: [0, -44],
    });
}

function crearPopup(p) {
    const catBadge    = p.categoria ? `<span style="background:#fc5648;color:white;font-size:9px;font-weight:700;text-transform:uppercase;padding:2px 7px;border-radius:999px;">${p.categoria}</span>` : '';
    const clienteBadge= p.es_cliente ? `<span style="background:#fef3c7;color:#92400e;font-size:9px;font-weight:700;padding:2px 7px;border-radius:999px;margin-left:4px;">Negocio</span>` : '';
    const catEmoji    = EMOJI_CAT[p.categoria_id] || (p.es_cliente ? '🏪' : '📍');
    const imgHtml     = p.imagen
        ? `<img src="${p.imagen}" style="width:100%;height:110px;object-fit:cover;border-radius:.5rem .5rem 0 0;">`
        : `<div style="width:100%;height:60px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-size:1.5rem;border-radius:.5rem .5rem 0 0;">${catEmoji}</div>`;
    return `<div style="font-family:sans-serif;">${imgHtml}<div style="padding:10px 12px 12px;"><div style="margin-bottom:5px;">${catBadge}${clienteBadge}</div><div style="font-weight:700;font-size:13px;line-height:1.3;margin-bottom:8px;">${p.title}</div><a href="/lugar/${p.slug}" style="background:#fc5648;color:white;font-size:11px;font-weight:700;padding:5px 12px;border-radius:8px;text-decoration:none;display:inline-block;">Ver detalle →</a></div></div>`;
}

function filtrarMapa(slug) {
    // Actualizar pills desktop
    document.querySelectorAll('.pill-mapa').forEach(p => {
        const activo = (p.dataset.slug === slug) || (!slug && p.dataset.slug === '');
        p.classList.toggle('bg-gray-900',  activo);
        p.classList.toggle('text-white',   activo);
        p.classList.toggle('border-gray-900', activo);
        p.classList.toggle('bg-white',     !activo);
        p.classList.toggle('text-gray-500',!activo);
        p.classList.toggle('border-gray-300', !activo);
    });

    if (!mapaLeaflet || !markerGroup) return;

    markerGroup.clearLayers();
    const datos = slug ? PUNTOS_DATA.filter(p => p.categoria_slug === slug) : PUNTOS_DATA;
    datos.forEach(p => {
        if (!p.lat || !p.lng) return;
        L.marker([p.lat, p.lng], { icon: crearIcono(p) })
            .bindPopup(crearPopup(p), { maxWidth: 230 })
            .addTo(markerGroup);
    });

    const contador = document.getElementById('mapa-contador');
    if (contador) contador.textContent = datos.filter(p => p.lat && p.lng).length;
}

function iniciarMapa(containerId) {
    delete L.Icon.Default.prototype._getIconUrl;

    mapaLeaflet = L.map(containerId, {
        center: [-33.039156, -71.621014],
        zoom: 14,
        minZoom: 5,
        maxZoom: 19,
        zoomControl: false,
        attributionControl: false,
    });
    mapaLeaflet.invalidateSize();

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, subdomains: 'abcd',
    }).addTo(mapaLeaflet);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19, subdomains: 'abcd', pane: 'shadowPane',
    }).addTo(mapaLeaflet);

    if (GPS_LAT && GPS_LNG) {
        mapaLeaflet.setView([GPS_LAT, GPS_LNG], 15);
        L.circleMarker([GPS_LAT, GPS_LNG], {
            radius: 8, color: '#fc5648', fillColor: '#fc5648', fillOpacity: 1, weight: 3,
        }).addTo(mapaLeaflet).bindPopup('Estás aquí');
    }

    markerGroup = L.layerGroup().addTo(mapaLeaflet);

    // Aplicar filtro activo al iniciar el mapa si hay categoría seleccionada
    const activePill = document.querySelector('.overflow-x-auto.no-scrollbar a.bg-gray-900[data-slug]');
    filtrarMapa(activePill?.dataset.slug ?? null);
}

// ── GPS ──────────────────────────────────────────────────────────────────
function geolocate(latInput, lngInput, form, btn) {
    if (!navigator.geolocation) { alert('{{ __("ui.general.sin_geolocalizacion") }}'); return; }
    btn.disabled = true;
    const orig = btn.innerHTML;
    btn.innerHTML = '{{ __("ui.general.localizando") }}';
    navigator.geolocation.getCurrentPosition(
        pos => { latInput.value = pos.coords.latitude; lngInput.value = pos.coords.longitude; form.submit(); },
        ()  => { alert('{{ __("ui.general.error_ubicacion") }}'); btn.disabled = false; btn.innerHTML = orig; },
        { enableHighAccuracy: true, timeout: 8000 }
    );
}

function geolocateMobile(btn) {
    const mapaVisible = !document.getElementById('vista-mapa-mobile')?.classList.contains('hidden');

    if (mapaVisible && mapaLeaflet) {
        // Estamos en el mapa: solo centrar, sin recargar página
        if (!navigator.geolocation) { alert('Tu navegador no soporta geolocalización.'); return; }
        btn.disabled = true;
        const orig = btn.innerHTML;
        btn.innerHTML = '⌛ Localizando…';
        navigator.geolocation.getCurrentPosition(
            pos => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                btn.disabled = false;
                btn.innerHTML = orig;
                mapaLeaflet.flyTo([lat, lng], 18, { duration: 1 });
                L.circleMarker([lat, lng], {
                    radius: 8, color: '#fc5648', fillColor: '#fc5648', fillOpacity: 1, weight: 3,
                }).addTo(mapaLeaflet).bindPopup('Estás aquí').openPopup();
            },
            () => { alert('No pudimos obtener tu ubicación.'); btn.disabled = false; btn.innerHTML = orig; },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    } else {
        // Estamos en el listado: submit normal para filtrar por cercanía
        geolocate(
            document.getElementById('lat-m'),
            document.getElementById('lng-m'),
            document.getElementById('filterForm-mobile'),
            btn
        );
    }
}

function activarVistaMapa() {
    const mobile = window.innerWidth < 768;
    document.getElementById('vista-listado-mobile')?.classList.add('hidden');
    document.getElementById('panoramas-mobile-section')?.classList.add('hidden');
    document.getElementById('blog-mobile-section')?.classList.add('hidden');
    document.getElementById('experiencias-mobile-section')?.classList.add('hidden');
    document.getElementById('filtro-activo-mobile')?.classList.add('hidden');
    const elMapaM = document.getElementById('vista-mapa-mobile');
    if (elMapaM) { elMapaM.classList.remove('hidden'); elMapaM.classList.add('flex'); }
    if (!mobile) {
        document.getElementById('vista-listado')?.classList.add('hidden');
        document.getElementById('vista-mapa')?.classList.remove('hidden');
    }
    history.replaceState(null, '', location.pathname);
    const containerId = mobile ? 'mapa-mobile' : 'mapa-principal';
    if (!mapaIniciado) { mapaIniciado = true; iniciarMapa(containerId); }
}

if (location.hash === '#mapa') {
    if (typeof L !== 'undefined') {
        activarVistaMapa();
    }
}

document.addEventListener('DOMContentLoaded', function () {

    if (location.hash === '#mapa') {
        activarVistaMapa();
    }

    // Desktop GPS — Livewire
    const btnGpsLw = document.getElementById('btn-gps-lw');
    if (btnGpsLw) {
        btnGpsLw.addEventListener('click', () => {
            if (!navigator.geolocation) { alert('Tu navegador no soporta geolocalización.'); return; }
            btnGpsLw.disabled = true;
            navigator.geolocation.getCurrentPosition(
                pos => Livewire.dispatch('gps-update', { lat: pos.coords.latitude, lng: pos.coords.longitude }),
                ()  => { btnGpsLw.disabled = false; }
            );
        });
    }

    // Mobile GPS (botón del drawer)
    const btnGpsM = document.getElementById('btn-gps-m');
    const fFormM  = document.getElementById('filterForm-mobile');
    if (btnGpsM && fFormM) {
        btnGpsM.addEventListener('click', () => geolocate(
            document.getElementById('lat-m'), document.getElementById('lng-m'), fFormM, btnGpsM
        ));
    }

    // Pills de categoría en mapa desktop
    document.querySelectorAll('.pill-mapa').forEach(pill => {
        pill.addEventListener('click', () => filtrarMapa(pill.dataset.slug || null));
    });

    // AJAX category pill filter — no page reload
    const pillsScroll = document.querySelector('.overflow-x-auto.no-scrollbar');
    if (pillsScroll) {
        pillsScroll.querySelectorAll('a').forEach(pill => {
            pill.addEventListener('click', async (e) => {
                e.preventDefault();
                const url  = pill.href;
                const slug = pill.dataset.slug ?? null;

                // Actualizar estilo activo inmediatamente
                pillsScroll.querySelectorAll('a').forEach(p => {
                    p.classList.remove('bg-gray-900', 'text-white', 'border-gray-900');
                    p.classList.add('bg-white', 'text-gray-500', 'border-gray-300', 'hover:border-gray-500');
                });
                pill.classList.remove('bg-white', 'text-gray-500', 'border-gray-300', 'hover:border-gray-500');
                pill.classList.add('bg-gray-900', 'text-white', 'border-gray-900');

                history.pushState({}, '', url);

                // Filtrar marcadores del mapa si ya está iniciado
                filtrarMapa(slug);

                // Actualizar listado mobile en background
                try {
                    const res  = await fetch(url);
                    const html = await res.text();
                    const doc  = new DOMParser().parseFromString(html, 'text/html');

                    const newListado = doc.getElementById('vista-listado-mobile');
                    const newFiltro  = doc.getElementById('filtro-activo-mobile');
                    if (newListado) {
                        const mapaVisible = !document.getElementById('vista-mapa-mobile').classList.contains('hidden');
                        if (mapaVisible) newListado.classList.add('hidden');
                        document.getElementById('vista-listado-mobile').replaceWith(newListado);
                    }
                    if (newFiltro)  document.getElementById('filtro-activo-mobile').replaceWith(newFiltro);
                } catch (_) {
                    window.location.href = url;
                }
            });
        });
    }
});
</script>
