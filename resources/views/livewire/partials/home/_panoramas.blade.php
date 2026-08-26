@if(isset($proximosPanoramas) && $proximosPanoramas->isNotEmpty())
<div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
        <span class="w-1 h-5 rounded-full bg-[#fc5648] shrink-0"></span>
        <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ __('ui.home.panoramas_titulo') }}</h2>
        <span class="flex-1 h-px bg-gray-200"></span>
        <a href="{{ route('atractivos.panoramas') }}" class="text-sm font-semibold text-[#fc5648] hover:underline shrink-0">{{ __('ui.home.ver_todos') }}</a>
    </div>
    <div class="overflow-x-auto border border-gray-100 shadow-sm"
         style="scrollbar-width:none;cursor:grab"
         x-data="{
             stopped: false, dragging: false,
             dragStartX: 0, dragScrollLeft: 0, last: null,
             init() {
                 const el = this.$el;
                 const step = (ts) => {
                     if (this.last !== null && !this.stopped && !this.dragging)
                         el.scrollLeft += 30 * (ts - this.last) / 1000;
                     this.last = ts;
                     requestAnimationFrame(step);
                 };
                 requestAnimationFrame(step);
             }
         }"
         @mouseenter="stopped = true"
         @mouseleave="stopped = false; dragging = false; last = null; $el.style.cursor = 'grab'"
         @mousedown.prevent="dragging = true; dragStartX = $event.pageX - $el.offsetLeft; dragScrollLeft = $el.scrollLeft; $el.style.cursor = 'grabbing'"
         @mouseup="dragging = false; $el.style.cursor = 'grab'"
         @mousemove="if (dragging) { $el.scrollLeft = dragScrollLeft - ($event.pageX - $el.offsetLeft - dragStartX) * 1.5 }">
        <div class="flex">
            @foreach($proximosPanoramas->take(30) as $p)
            @php $hrefPanorama = $p->slug
                ? route('panoramas.show', $p)
                : ($p->punto_slug
                    ? route('puntos.evento', ['slug' => $p->punto_slug, 'item' => $p->modulo_item_id])
                    : route('artista.evento', ['slug' => $p->artista_slug, 'item' => $p->modulo_item_id])); @endphp
            <a href="{{ $hrefPanorama }}"
               class="bg-white hover:bg-gray-50 transition group shrink-0 w-44 border-r border-gray-100">
                @if($p->imagen)
                    <img src="{{ asset('storage/' . $p->imagen) }}" alt="{{ $p->titulo }}"
                         class="w-full h-32 object-cover group-hover:scale-105 transition duration-300">
                @else
                    <div class="w-full h-32 bg-[#fff0ef] flex items-center justify-center text-3xl">🗓</div>
                @endif
                <div class="p-3">
                    <p class="text-xs font-bold text-[#fc5648]">
                        {{ \Carbon\Carbon::parse($p->fecha)->locale('es')->isoFormat('D MMM') }}
                    </p>
                    <p class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mt-0.5">{{ $p->titulo }}</p>
                    @if($p->ubicacion)
                        <p class="text-xs text-gray-400 truncate mt-1">📍 {{ $p->ubicacion }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endif
