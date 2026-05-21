{{-- Indicador de filtro activo --}}
<div id="filtro-activo-mobile">
@if($hayFiltros)
<div class="mx-3 mb-2 px-3 py-2 bg-[#fff0ef] rounded-xl flex items-center justify-between">
    <span class="text-xs text-[#fc5648] font-semibold">
        @if(request('search')) "{{ request('search') }}"
        @elseif(request('category')) {{ $categorias->firstWhere('slug', request('category'))?->nombre }}
        @elseif(request('lat')) Cerca de ti
        @endif
    </span>
    <a href="{{ route('puntos.index') }}" class="text-xs text-gray-400 font-bold">✕ Borrar</a>
</div>
@endif
</div>

{{-- Resultados --}}
<div id="vista-listado-mobile" class="flex-1 px-3 pt-3 pb-6">
    @if($atractivos->count())
        <div class="grid grid-cols-1 gap-4">
        @foreach($atractivos as $atractivo)
            @include('puntos.partials._card_mobile')
        @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $atractivos->links() }}
        </div>
    @elseif(empty($panoramas) || $panoramas->isEmpty())
        <div class="text-center py-16">
            <div class="text-5xl mb-3">🕵️‍♂️</div>
            <p class="font-bold text-gray-700 mb-1">Sin resultados</p>
            <p class="text-sm text-gray-400 mb-4">Prueba con otra búsqueda</p>
            <a href="{{ route('puntos.index') }}"
               class="text-sm font-bold text-[#fc5648] underline">Ver todos</a>
        </div>
    @endif

    {{-- Panoramas en búsqueda --}}
    @if(isset($panoramas) && $panoramas->isNotEmpty())
    <div class="mt-8">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-sm font-bold text-gray-700">🗓 Panoramas relacionados</h2>
            <a href="{{ route('atractivos.panoramas') }}" class="text-xs text-[#fc5648] font-semibold">Ver todos →</a>
        </div>
        <div class="flex flex-col gap-3">
            @foreach($panoramas as $panorama)
            <a href="{{ route('panoramas.show', $panorama) }}"
               class="flex gap-3 bg-white rounded-2xl border border-gray-100 shadow-sm p-3 items-center">
                @if($panorama->imagen)
                    <img src="{{ asset('storage/' . $panorama->imagen) }}"
                         alt="{{ $panorama->titulo }}"
                         class="w-16 h-16 rounded-xl object-cover shrink-0">
                @else
                    <div class="w-16 h-16 rounded-xl bg-[#fff0ef] flex items-center justify-center text-2xl shrink-0">🗓</div>
                @endif
                <div class="min-w-0">
                    <p class="font-bold text-gray-900 text-sm leading-snug line-clamp-2">{{ $panorama->titulo }}</p>
                    <p class="text-xs text-[#fc5648] font-semibold mt-0.5">
                        {{ \Carbon\Carbon::parse($panorama->fecha)->locale('es')->isoFormat('D MMM') }}
                        @if($panorama->fecha_fin && $panorama->fecha_fin !== $panorama->fecha)
                            — {{ \Carbon\Carbon::parse($panorama->fecha_fin)->locale('es')->isoFormat('D MMM') }}
                        @endif
                    </p>
                    @if($panorama->ubicacion)
                        <p class="text-xs text-gray-400 truncate">📍 {{ $panorama->ubicacion }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
