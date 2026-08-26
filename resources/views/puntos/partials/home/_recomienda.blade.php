{{-- Pindoor Recomienda --}}
@if(isset($recomendaciones) && $recomendaciones->isNotEmpty() && !request()->filled('lat') && !request()->filled('category') && !request()->filled('search'))
<div id="recomienda-mobile-section" class="px-3 pt-3">
    <div class="flex items-center gap-2 mb-2">
        <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
        <span class="text-md font-bold text-gray-900 tracking-tight">Pindoor Recomienda</span>
        <span class="flex-1 h-px bg-gray-200"></span>
    </div>
    <div class="overflow-x-auto pb-1 scrollbar-hide" style="scrollbar-width:none"
         x-data="{ touchStartX: 0, touchMoved: false }"
         @touchstart="touchStartX = $event.touches[0].clientX; touchMoved = false"
         @touchmove="if (Math.abs($event.touches[0].clientX - touchStartX) > 10) touchMoved = true"
         @click.capture="if (touchMoved) { $event.preventDefault(); $event.stopPropagation(); touchMoved = false }">
        <div class="flex gap-2">
        @foreach($recomendaciones as $r)
        <a href="{{ route('recomienda.show', $r->slug) }}"
           class="flex-none w-56 bg-[#fff0ef] border border-[#fc5648]/20 rounded-2xl shadow-sm overflow-hidden">
            <div class="relative">
                @if($r->imagen_portada)
                    <img src="{{ asset('storage/' . $r->imagen_portada) }}"
                         alt="{{ $r->titulo }}"
                         class="w-full h-36 object-cover">
                @else
                    <div class="w-full h-24 bg-[#ffe0dd] flex items-center justify-center text-3xl">{{ $r->plan_info['emoji'] }}</div>
                @endif
                <span class="absolute top-1.5 right-1.5 text-base leading-none">{{ $r->plan_info['emoji'] }}</span>
            </div>
            <div class="px-2.5 py-2">
                <p class="text-[14px] font-bold text-gray-900 leading-tight line-clamp-2 mt-0.5">{{ $r->titulo }}</p>
            </div>
        </a>
        @endforeach
        </div>
    </div>
</div>
@endif
