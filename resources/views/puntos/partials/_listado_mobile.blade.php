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
    @else
        <div class="text-center py-16">
            <div class="text-5xl mb-3">🕵️‍♂️</div>
            <p class="font-bold text-gray-700 mb-1">Sin resultados</p>
            <p class="text-sm text-gray-400 mb-4">Prueba con otra búsqueda</p>
            <a href="{{ route('puntos.index') }}"
               class="text-sm font-bold text-[#fc5648] underline">Ver todos</a>
        </div>
    @endif
</div>
