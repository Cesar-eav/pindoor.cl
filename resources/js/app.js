import './bootstrap';
import { createApp } from 'vue';
import PindoorTest from './components/PindoorTest.vue';
import SelectorMapa from './components/SelectorMapa.vue';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const app = createApp({});

// Primero registras...
app.component('pindoor-test', PindoorTest);
app.component('selector-mapa', SelectorMapa);

// ...y AL FINAL montas.
app.mount('#app');
