<template>
  <div class="mb-4">
    <div id="map" class="h-64 w-full rounded-xl border-2 border-gray-200 shadow-inner mb-4"></div>
    
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase">Latitud</label>
        <input type="text" name="lat" v-model="lat" readonly
          class="block mt-1 w-full bg-gray-50 border-gray-300 rounded-md text-sm">
      </div>
      <div>
        <label class="block text-xs font-bold text-gray-500 uppercase">Longitud</label>
        <input type="text" name="lng" v-model="lng" readonly
          class="block mt-1 w-full bg-gray-50 border-gray-300 rounded-md text-sm">
      </div>
    </div>
    <p class="text-[10px] text-gray-400 mt-1 italic">* Haz clic en el mapa para ajustar la ubicación exacta en el cerro.</p>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const lat = ref(-33.0472); // Coordenada central de Valparaíso
const lng = ref(-71.6297);
let map = null;
let marker = null;

onMounted(() => {
  // Inicializar mapa
  map = L.map('map').setView([lat.value, lng.value], 14);

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
    updatePosition(newLat, newLng);
  });

  // Evento al arrastrar el marcador
  marker.on('dragend', () => {
    const position = marker.getLatLng();
    updatePosition(position.lat, position.lng);
  });
});

function updatePosition(newLat, newLng) {
  lat.value = newLat.toFixed(6);
  lng.value = newLng.toFixed(6);
  marker.setLatLng([newLat, newLng]);
}
</script>

<style>
/* Ajuste para que el mapa no se vea gris al cargar */
.leaflet-container {
  z-index: 1;
}
</style>