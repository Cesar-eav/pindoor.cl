{{-- Tarjetas de totales --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="bg-white shadow-sm rounded-2xl p-6 border-l-4 border-[#fc5648]">
        <div class="text-sm font-medium text-gray-500 uppercase">Visitas a tu ficha</div>
        <div class="flex items-end gap-4 mt-1">
            <div>
                <div class="text-3xl font-bold text-gray-900">{{ $totales['visitas'] }}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold">Total</div>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-700">{{ $visitasHoy }}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold">Hoy</div>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-700">{{ $visitasSemana }}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold">Semana</div>
            </div>
            <div>
                <div class="text-xl font-bold text-gray-700">{{ $visitasMes }}</div>
                <div class="text-[10px] text-gray-400 uppercase font-bold">Mes</div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-2xl p-6 border-l-4 border-blue-500">
        <div class="text-sm font-medium text-gray-500 uppercase">Clics en "Cómo llegar"</div>
        <div class="text-3xl font-bold text-gray-900 mt-1">{{ $totales['como_llegar'] }}</div>
        <div class="text-xs text-gray-400 mt-1">Personas que pidieron indicaciones en el mapa</div>
    </div>

    <div class="bg-white shadow-sm rounded-2xl p-6 border-l-4 border-[#25D366]">
        <div class="text-sm font-medium text-gray-500 uppercase">Clics en WhatsApp</div>
        <div class="text-3xl font-bold text-gray-900 mt-1">{{ $totales['whatsapp'] }}</div>
        <div class="text-xs text-gray-400 mt-1">
            @if($punto->whatsapp_publico)
                Contactos directos desde tu ficha
            @else
                Configura tu WhatsApp público para empezar a recibir contactos
            @endif
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-2xl p-6 border-l-4 border-purple-400">
        <div class="text-sm font-medium text-gray-500 uppercase">Compartidos</div>
        <div class="text-3xl font-bold text-gray-900 mt-1">{{ $totales['compartidos'] }}</div>
        @if($porCanal->isNotEmpty())
        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2">
            @foreach($porCanal as $canal => $total)
            <span class="text-xs text-gray-500">
                {{ \App\Http\Controllers\Admin\CompartidosController::CANALES[$canal]['emoji'] ?? '🔗' }}
                {{ \App\Http\Controllers\Admin\CompartidosController::CANALES[$canal]['label'] ?? ucfirst($canal) }}: <strong class="text-gray-700">{{ $total }}</strong>
            </span>
            @endforeach
        </div>
        @endif
    </div>

</div>

{{-- Gráfico de tendencia --}}
<div class="bg-white shadow-sm rounded-2xl p-6">
    <h3 class="font-bold text-gray-700 mb-4">Últimos 30 días</h3>
    <canvas id="grafico-tendencia" height="90"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('grafico-tendencia'), {
    type: 'line',
    data: {
        labels: @json($dias->pluck('fecha')),
        datasets: [
            { label: 'Visitas',      data: @json($dias->pluck('visitas')),     borderColor: '#fc5648', backgroundColor: 'transparent', tension: 0.3 },
            { label: 'Cómo llegar',  data: @json($dias->pluck('como_llegar')), borderColor: '#2a78d6', backgroundColor: 'transparent', tension: 0.3 },
            { label: 'WhatsApp',     data: @json($dias->pluck('whatsapp')),    borderColor: '#25D366', backgroundColor: 'transparent', tension: 0.3 },
            { label: 'Compartidos',  data: @json($dias->pluck('compartidos')),borderColor: '#a855f7', backgroundColor: 'transparent', tension: 0.3 },
        ],
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
    },
});
</script>
