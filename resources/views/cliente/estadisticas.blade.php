<x-app-layout :punto="$punto" :modulos="$punto->modulos_habilitados ?? []">

<div class="flex bg-white" style="min-height: calc(100vh - 3.5rem)">

    @include('cliente.partials._sidebar', ['punto' => $punto])

    <main class="flex-1 min-w-0" style="background: #f8fafc">
        <div class="max-w-5xl mx-auto px-4 lg:px-8 py-8 space-y-6">

            <div>
                <h1 class="text-xl font-extrabold text-gray-900">📊 Estadísticas</h1>
                <p class="text-sm text-gray-400 mt-0.5">Cómo le está yendo a la ficha pública de {{ $punto->title }}.</p>
            </div>

            @if(!$punto->whatsapp_publico)
            <div class="rounded-2xl p-4 flex items-center justify-between gap-4" style="background: #fff0ef; border: 1px solid rgba(252,86,72,0.25)">
                <p class="text-sm text-gray-700">
                    <strong>Aún no configuraste tu WhatsApp público.</strong> Actívalo para que los visitantes de tu ficha puedan escribirte directo.
                </p>
                <a href="{{ route('cliente.perfil.editar', $punto) }}"
                   class="shrink-0 text-sm font-bold text-[#fc5648] hover:underline">
                    Configurar →
                </a>
            </div>
            @endif

            @include('partials._estadisticas_resumen', ['punto' => $punto, 'totales' => $totales, 'porCanal' => $porCanal, 'visitasHoy' => $visitasHoy, 'visitasSemana' => $visitasSemana, 'visitasMes' => $visitasMes, 'dias' => $dias])

        </div>
    </main>

</div>

</x-app-layout>
