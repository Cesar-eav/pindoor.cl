<template>
  <div class="mb-4">
    <label class="block text-sm font-bold text-gray-800 mb-1.5">🔍 Busca tu dirección</label>
    <div class="relative">
      <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-[#fc5648]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      <input type="text" v-model="direccion" @keydown.enter.prevent="buscarDireccion" @blur="buscarDireccion"
        placeholder="Ej: Condell 1234, Cerro Alegre" autocomplete="off" autocorrect="off" spellcheck="false"
        class="block bg-white w-full pl-12 pr-12 py-3.5 border-2 border-[#fc5648]/40 rounded-xl text-base shadow-sm focus:ring-2 focus:ring-[#fc5648] focus:border-[#fc5648] outline-none transition">
      <div v-if="buscando" class="absolute right-4 top-1/2 -translate-y-1/2">
        <svg class="animate-spin h-5 w-5 text-[#fc5648]" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
      </div>
    </div>
    <p v-if="mensaje" class="text-xs mt-1.5 font-medium" :class="encontrada ? 'text-green-600' : 'text-amber-600'">{{ mensaje }}</p>

    <div id="map" class="h-64 w-full rounded-xl border-2 border-gray-200 shadow-inner mt-3"></div>
    <p class="text-[11px] text-gray-400 mt-1.5">📍 Si el pin no quedó exacto, arrástralo dentro del mapa para ajustarlo.</p>

    <input type="hidden" name="lat" :value="lat">
    <input type="hidden" name="lng" :value="lng">
    <input type="hidden" name="direccion" :value="direccion">
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
  initialLat: { type: Number, default: -33.0472 },
  initialLng: { type: Number, default: -71.6297 },
  geocodeUrl: { type: String, required: true },
  reverseGeocodeUrl: { type: String, default: '' },
});

const ZOOM_CERCA = 17;

const lat = ref(props.initialLat);
const lng = ref(props.initialLng);
const direccion = ref('');
const buscando = ref(false);
const encontrada = ref(false);
const mensaje = ref('');
let map = null;
let marker = null;
let ultimaBusqueda = '';

onMounted(() => {
  map = L.map('map').setView([lat.value, lng.value], 14);
  // Corrige el render cuando el contenedor estaba oculto (x-show, tabs, etc.)
  setTimeout(() => map.invalidateSize(), 300);
  // Escucha el evento que dispara Alpine al mostrar el panel de creación
  window.addEventListener('mapa-visible', () => map.invalidateSize());

  // Capa de OpenStreetMap (Gratuita)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
  }).addTo(map);

  // Icono por defecto (Fix para Leaflet con Vite)
  const defaultIcon = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41]
  });

  // Marcador inicial
  marker = L.marker([lat.value, lng.value], { icon: defaultIcon, draggable: true }).addTo(map);

  // Evento al hacer clic en el mapa
  map.on('click', (e) => {
    const { lat: newLat, lng: newLng } = e.latlng;
    updatePosition(newLat, newLng, { reverseGeocode: true });
  });

  // Evento al arrastrar el marcador
  marker.on('dragend', () => {
    const position = marker.getLatLng();
    updatePosition(position.lat, position.lng, { reverseGeocode: true });
  });
});

function updatePosition(newLat, newLng, { zoom = null, reverseGeocode = false } = {}) {
  lat.value = newLat.toFixed(6);
  lng.value = newLng.toFixed(6);
  marker.setLatLng([newLat, newLng]);
  if (zoom) {
    map.setView([newLat, newLng], zoom);
  } else {
    map.panTo([newLat, newLng]);
  }
  if (reverseGeocode) {
    buscarDireccionInversa(newLat, newLng);
  }
}

async function buscarDireccionInversa(latVal, lngVal) {
  if (!props.reverseGeocodeUrl) return;

  buscando.value = true;
  mensaje.value = '';

  try {
    const res = await fetch(props.reverseGeocodeUrl + `?lat=${latVal}&lng=${lngVal}`);
    const data = await res.json();

    if (data.encontrado) {
      direccion.value = data.direccion;
      ultimaBusqueda = data.direccion;
      encontrada.value = true;
      mensaje.value = '✓ ' + data.direccion;
    } else {
      encontrada.value = false;
      mensaje.value = 'No pudimos identificar la dirección de este punto. Puedes escribirla manualmente.';
    }
  } catch (e) {
    encontrada.value = false;
    mensaje.value = 'No pudimos identificar la dirección de este punto. Puedes escribirla manualmente.';
  } finally {
    buscando.value = false;
  }
}

async function buscarDireccion() {
  const q = direccion.value.trim();
  if (!q || q === ultimaBusqueda) return;
  ultimaBusqueda = q;

  buscando.value = true;
  mensaje.value = '';

  try {
    const res = await fetch(props.geocodeUrl + '?q=' + encodeURIComponent(q));
    const data = await res.json();

    if (data.encontrado) {
      updatePosition(parseFloat(data.lat), parseFloat(data.lng), { zoom: ZOOM_CERCA });
      encontrada.value = true;
      mensaje.value = '✓ Encontramos: ' + data.direccion;
    } else {
      encontrada.value = false;
      mensaje.value = 'No encontramos esa dirección. Ubica el pin manualmente en el mapa.';
    }
  } catch (e) {
    encontrada.value = false;
    mensaje.value = 'No pudimos buscar la dirección. Ubica el pin manualmente en el mapa.';
  } finally {
    buscando.value = false;
  }
}
</script>

<style>
/* Ajuste para que el mapa no se vea gris al cargar */
.leaflet-container {
  z-index: 1;
}
</style>
