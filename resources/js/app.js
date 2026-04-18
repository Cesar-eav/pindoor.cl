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

// Dropdown handler for data-dropdown components
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-dropdown]').forEach(dropdown => {
        const trigger = dropdown.querySelector('[data-dropdown-trigger]');
        const content = dropdown.querySelector('[data-dropdown-content]');
        if (!trigger || !content) return;

        trigger.addEventListener('click', e => {
            e.stopPropagation();
            const isOpen = content.style.display !== 'none';
            document.querySelectorAll('[data-dropdown-content]').forEach(c => c.style.display = 'none');
            content.style.display = isOpen ? 'none' : 'block';
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('[data-dropdown-content]').forEach(c => c.style.display = 'none');
    });
});


const app = createApp({});

// Primero registras...
app.component('pindoor-test', PindoorTest);
app.component('selector-mapa', SelectorMapa);
app.component('galeria-subida', GaleriaSubida);

// ...y AL FINAL montas.
app.mount('#app');
