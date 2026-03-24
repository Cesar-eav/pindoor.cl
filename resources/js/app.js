import './bootstrap';
import { createApp } from 'vue';
import PindoorTest from './components/PindoorTest.vue';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const app = createApp({});

// Primero registras...
app.component('pindoor-test', PindoorTest);

// ...y AL FINAL montas.
app.mount('#app');
