<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 Estadísticas — {{ $punto->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('admin.clientes') }}" class="text-sm text-gray-500 hover:text-gray-800 font-medium">
                ← Volver a clientes
            </a>

            @include('partials._estadisticas_resumen', ['punto' => $punto, 'totales' => $totales, 'porCanal' => $porCanal, 'visitasHoy' => $visitasHoy, 'visitasSemana' => $visitasSemana, 'visitasMes' => $visitasMes, 'dias' => $dias])

        </div>
    </div>
</x-admin-layout>
