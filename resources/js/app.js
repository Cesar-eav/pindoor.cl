import './bootstrap';
import { createApp } from 'vue';
import PindoorTest from './components/PindoorTest.vue';
import SelectorMapa from './components/SelectorMapa.vue';
import GaleriaSubida from './components/GaleriaSubida.vue'
import Alpine from 'alpinejs';
import axios from 'axios';

window.Alpine = Alpine;
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Alpine.start();


const app = createApp({});

// Primero registras...
app.component('pindoor-test', PindoorTest);
app.component('selector-mapa', SelectorMapa);
app.component('galeria-subida', GaleriaSubida);

// ...y AL FINAL montas.
app.mount('#app');
