@if(isset($recomendaciones) && $recomendaciones->isNotEmpty())
<div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
        <span class="w-1 h-5 rounded-full bg-[#fc5648] shrink-0"></span>
        <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">Pindoor Recomienda</h2>
        <span class="flex-1 h-px bg-gray-200"></span>
    </div>
    <div class="relative"
         x-data="{
             dragging: false, moved: false,
             dragStartX: 0, dragScrollLeft: 0,
             canScrollRight: false, canScrollLeft: false,
             check() {
                 this.canScrollRight = this.$refs.scroller.scrollLeft + this.$refs.scroller.clientWidth < this.$refs.scroller.scrollWidth - 4;
                 this.canScrollLeft = this.$refs.scroller.scrollLeft > 4;
             },
             init() { this.check(); }
         }">
        <div x-ref="scroller"
             class="overflow-x-auto border border-[#fc5648]/20 shadow-sm"
             style="scrollbar-width:none;cursor:grab"
             @scroll="check()"
             @mouseleave="dragging = false; $el.style.cursor = 'grab'"
             @mousedown.prevent="dragging = true; moved = false; dragStartX = $event.pageX - $el.offsetLeft; dragScrollLeft = $el.scrollLeft; $el.style.cursor = 'grabbing'"
             @mouseup="dragging = false; $el.style.cursor = 'grab'"
             @mousemove="if (dragging) { const dx = $event.pageX - $el.offsetLeft - dragStartX; if (Math.abs(dx) > 5) moved = true; $el.scrollLeft = dragScrollLeft - dx * 1.5 }"
             @click.capture="if (moved) { $event.preventDefault(); $event.stopPropagation(); moved = false }">
            <div class="flex">
                @foreach($recomendaciones as $r)
                <a href="{{ route('recomienda.show', $r->slug) }}"
                   class="bg-[#fff0ef] hover:bg-[#ffe0dd] transition group shrink-0 w-72 border-r border-[#fc5648]/20">
                    <div class="relative">
                        @if($r->imagen_portada)
                            <img src="{{ asset('storage/' . $r->imagen_portada) }}" alt="{{ $r->titulo }}"
                                 class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-32 bg-[#ffe0dd] flex items-center justify-center text-3xl">{{ $r->plan_info['emoji'] }}</div>
                        @endif
                        <span class="absolute top-2 right-2 text-lg leading-none">{{ $r->plan_info['emoji'] }}</span>
                    </div>
                    <div class="p-3">
                        <p class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mt-0.5">{{ $r->titulo }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Flechas: indican que hay más recomendaciones para ver a los lados --}}
        <button type="button"
                x-show="canScrollLeft" x-cloak
                @click.stop.prevent="$refs.scroller.scrollBy({ left: -420, behavior: 'smooth' })"
                class="absolute z-10 left-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button type="button"
                x-show="canScrollRight" x-cloak
                @click.stop.prevent="$refs.scroller.scrollBy({ left: 420, behavior: 'smooth' })"
                class="absolute z-10 right-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>
</div>
@endif
