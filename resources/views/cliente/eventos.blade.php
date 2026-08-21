<x-app-layout>

<div class="flex bg-white" style="min-height: calc(100vh - 3.5rem)">

    @include('cliente.partials._sidebar', ['punto' => $punto])

    <main class="flex-1 min-w-0" style="background: #f8fafc">
        <div class="max-w-3xl mx-auto px-4 lg:px-8 py-8 space-y-6">

            <div class="flex items-start justify-between gap-4">
                <div>
                    <a href="{{ route('cliente.perfil.ver', $punto) }}" class="text-xs text-gray-400 hover:text-gray-600">← Volver al perfil</a>
                    <h1 class="text-xl font-extrabold text-gray-900 mt-1">📅 Agenda cultural</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Obras de teatro, proyecciones, conciertos, talleres y más para {{ $punto->title }}.</p>
                </div>
                <a href="{{ route('cliente.eventos.reel', $punto) }}"
                   class="shrink-0 flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                    🎬 Grabar reel
                </a>
            </div>

            <livewire:cliente-eventos :punto="$punto" />

        </div>
    </main>

</div>

</x-app-layout>
