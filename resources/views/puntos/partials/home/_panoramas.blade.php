{{-- Próximos panoramas --}}
@if(isset($proximosPanoramas) && $proximosPanoramas->isNotEmpty() && !request()->filled('lat') && !request()->filled('category') && !request()->filled('search'))
<div id="panoramas-mobile-section" class="px-3 pt-3">
    <div class="flex items-center gap-2 mb-2">
        <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
        <span class="text-md font-bold text-gray-900 tracking-tight">{{ __('ui.home.panoramas_titulo') }}</span>
        <span class="flex-1 h-px bg-gray-200"></span>
        <a href="{{ route('atractivos.panoramas') }}" class="text-[11px] text-[#fc5648] font-semibold shrink-0">{{ __('ui.home.ver_todos') }}</a>
    </div>
    <div class="overflow-x-auto pb-1 scrollbar-hide"
         style="scrollbar-width:none"
         x-data="{
             stopped: false,
             last: null,
             init() {
                 const el = this.$el;
                 const step = (ts) => {
                     if (this.last !== null && !this.stopped) {
                         el.scrollLeft += 30 * (ts - this.last) / 1000;
                     }
                     this.last = ts;
                     requestAnimationFrame(step);
                 };
                 requestAnimationFrame(step);
             }
         }"
         @click="stopped = true">
        <div class="flex">
        @foreach($proximosPanoramas as $p)
        @php $hrefPanorama = $p->slug
            ? route('panoramas.show', $p)
            : ($p->punto_slug
                ? route('puntos.evento', ['slug' => $p->punto_slug, 'item' => $p->modulo_item_id])
                : route('artista.evento', ['slug' => $p->artista_slug, 'item' => $p->modulo_item_id])); @endphp
        <a href="{{ $hrefPanorama }}"
           class="flex-none w-36 bg-white border border-gray-100 shadow-sm overflow-hidden">
            @if($p->imagen)
                <img src="{{ asset('storage/' . $p->imagen) }}"
                     alt="{{ $p->titulo }}"
                     class="w-full h-24 object-cover">
            @else
                <div class="w-full h-24 bg-[#fff0ef] flex items-center justify-center text-3xl">🗓</div>
            @endif
            <div class="px-2.5 py-2">
                <p class="text-[11px] font-bold text-gray-900 leading-tight line-clamp-2">{{ $p->titulo }}</p>
                <p class="text-[10px] text-[#fc5648] font-semibold mt-0.5">
                    {{ \Carbon\Carbon::parse($p->fecha)->locale('es')->isoFormat('D MMM') }}
                </p>
                @if($p->ubicacion)
                <p class="text-[10px] text-gray-400 truncate">📍 {{ $p->ubicacion }}</p>
                @endif
            </div>
        </a>
        @endforeach
        </div>
    </div>
</div>
@endif
