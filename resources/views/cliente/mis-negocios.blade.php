<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mis Negocios
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3 mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach($puntos as $punto)
                <a href="{{ route('cliente.perfil.ver', $punto) }}"
                   class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 p-6 flex items-start gap-4">

                    {{-- Logo o ícono --}}
                    @if($punto->imagen_perfil)
                        <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                             alt="Logo {{ $punto->title }}"
                             class="w-14 h-14 rounded-xl object-cover border border-gray-100 shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-2xl shrink-0">
                            🏪
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="font-bold text-gray-900 group-hover:text-pindoor-accent transition truncate">
                                {{ $punto->title }}
                            </span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold shrink-0
                                {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $punto->activo ? 'Visible' : 'Pausado' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-400 truncate">
                            {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                            &middot; 📍 {{ $punto->sector }}
                        </p>
                        @if($punto->horario)
                            <p class="text-xs text-gray-400 mt-1">🕐 {{ $punto->horario }}</p>
                        @endif
                    </div>

                    <svg class="w-4 h-4 text-gray-300 group-hover:text-pindoor-accent shrink-0 mt-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
