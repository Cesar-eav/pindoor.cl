import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const loginsData = JSON.parse(document.getElementById('data-logins-por-dia').textContent);
    const accionesData = JSON.parse(document.getElementById('data-acciones-por-tipo').textContent);

    new Chart(document.getElementById('chart-logins'), {
        type: 'bar',
        data: {
            labels: loginsData.map((d) => d.fecha.slice(5)),
            datasets: [{
                label: 'Logins',
                data: loginsData.map((d) => d.total),
                backgroundColor: '#3b82f6',
                borderRadius: 4,
            }],
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });

    new Chart(document.getElementById('chart-acciones'), {
        type: 'bar',
        data: {
            labels: accionesData.map((d) => d.tipo.replace(/_/g, ' ')),
            datasets: [{
                label: 'Acciones',
                data: accionesData.map((d) => d.total),
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
