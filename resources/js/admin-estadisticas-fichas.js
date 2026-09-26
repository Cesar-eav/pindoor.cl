import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const diasData = JSON.parse(document.getElementById('data-tendencia-fichas').textContent);
    const categoriaData = JSON.parse(document.getElementById('data-por-categoria').textContent);

    new Chart(document.getElementById('grafico-tendencia-fichas'), {
        type: 'line',
        data: {
            labels: diasData.map((d) => d.fecha),
            datasets: [
                { label: 'Visitas',     data: diasData.map((d) => d.visitas),     borderColor: '#fc5648', backgroundColor: 'transparent', tension: 0.3 },
                { label: 'Cómo llegar', data: diasData.map((d) => d.como_llegar), borderColor: '#2a78d6', backgroundColor: 'transparent', tension: 0.3 },
                { label: 'WhatsApp',    data: diasData.map((d) => d.whatsapp),    borderColor: '#25D366', backgroundColor: 'transparent', tension: 0.3 },
                { label: 'Compartidos', data: diasData.map((d) => d.compartidos), borderColor: '#a855f7', backgroundColor: 'transparent', tension: 0.3 },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });

    new Chart(document.getElementById('grafico-por-categoria'), {
        type: 'bar',
        data: {
            labels: categoriaData.map((d) => d.nombre),
            datasets: [{
                label: 'Visitas',
                data: categoriaData.map((d) => d.total),
                backgroundColor: '#fc5648',
                borderRadius: 4,
            }],
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
});
