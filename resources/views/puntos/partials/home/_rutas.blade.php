{{-- Rutas Pindoor --}}
@if(isset($ultimasRutas) && $ultimasRutas->isNotEmpty() && !request()->filled('category') && !request()->filled('search') && !request()->filled('lat'))
<div id="rutas-mobile-section" class="mt-5">
    <div class="flex items-center gap-2 mb-3 px-3">
        <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
        <h2 class="text-sm font-bold text-gray-900 tracking-tight">{{ __('ui.home.rutas_titulo') }}</h2>
        <span class="flex-1 h-px bg-gray-200"></span>
        <a href="{{ route('rutas.index') }}" class="text-[11px] font-semibold text-[#fc5648] shrink-0">{{ __('ui.home.ver_todos') }}</a>
    </div>

    <div class="flex gap-3 overflow-x-auto pb-2 px-3" style="-ms-overflow-style:none;scrollbar-width:none;"
         x-data="{ touchStartX: 0, touchMoved: false }"
         @touchstart="touchStartX = $event.touches[0].clientX; touchMoved = false"
         @touchmove="if (Math.abs($event.touches[0].clientX - touchStartX) > 10) touchMoved = true"
         @click.capture="if (touchMoved) { $event.preventDefault(); $event.stopPropagation(); touchMoved = false }">
        @foreach($ultimasRutas as $ruta)
        <a href="{{ route('rutas.show', $ruta->slug) }}"
           class="relative shrink-0 rounded-2xl overflow-hidden shadow-sm"
           style="width:72vw;height:11rem;">
            @if($ruta->imagen_portada)
                <img src="{{ asset('storage/' . $ruta->imagen_portada) }}"
                     alt="{{ $ruta->titulo }}"
                     class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-gray-800 flex items-center justify-center text-4xl">🗺️</div>
            @endif
            <div class="absolute inset-0 bg-linear-to-t from-black/85 via-black/25 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <p class="text-[10px] font-bold text-white/70 mb-0.5">{{ $ruta->puntos->count() }} {{ $ruta->puntos->count() === 1 ? 'parada' : 'paradas' }}</p>
                <h3 class="text-sm font-bold text-white leading-snug line-clamp-3">{{ $ruta->titulo }}</h3>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
