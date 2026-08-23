<div>
    {{-- Secciones sin filtros --}}
    @if(!$hayFiltros)

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

        @if(isset($recomendaciones) && $recomendaciones->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="w-1 h-5 rounded-full bg-[#fc5648] shrink-0"></span>
                <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">Pindoor Recomienda</h2>
                <span class="flex-1 h-px bg-gray-200"></span>
            </div>
            <div class="relative"
                 x-data="{
                     stopped: false, dragging: false,
                     dragStartX: 0, dragScrollLeft: 0, last: null,
                     canScrollRight: false, canScrollLeft: false,
                     check() {
                         this.canScrollRight = this.$refs.scroller.scrollLeft + this.$refs.scroller.clientWidth < this.$refs.scroller.scrollWidth - 4;
                         this.canScrollLeft = this.$refs.scroller.scrollLeft > 4;
                     },
                     init() {
                         this.check();
                         const el = this.$refs.scroller;
                         const step = (ts) => {
                             if (this.last !== null && !this.stopped && !this.dragging)
                                 el.scrollLeft += 30 * (ts - this.last) / 1000;
                             this.last = ts;
                             this.check();
                             requestAnimationFrame(step);
                         };
                         requestAnimationFrame(step);
                     }
                 }">
                <div x-ref="scroller"
                     class="overflow-x-auto border border-[#fc5648]/20 shadow-sm"
                     style="scrollbar-width:none;cursor:grab"
                     @scroll="check()"
                     @mouseenter="stopped = true"
                     @mouseleave="stopped = false; dragging = false; last = null; $el.style.cursor = 'grab'"
                     @mousedown.prevent="dragging = true; dragStartX = $event.pageX - $el.offsetLeft; dragScrollLeft = $el.scrollLeft; $el.style.cursor = 'grabbing'"
                     @mouseup="dragging = false; $el.style.cursor = 'grab'"
                     @mousemove="if (dragging) { $el.scrollLeft = dragScrollLeft - ($event.pageX - $el.offsetLeft - dragStartX) * 1.5 }">
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
                        @click="$refs.scroller.scrollBy({ left: -420, behavior: 'smooth' })"
                        class="absolute left-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button type="button"
                        x-show="canScrollRight" x-cloak
                        @click="$refs.scroller.scrollBy({ left: 420, behavior: 'smooth' })"
                        class="absolute right-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if(isset($ultimosPosts) && $ultimosPosts->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="w-1 h-5 rounded-full bg-[#fc5648] shrink-0"></span>
                <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ __('ui.home.blog_titulo') }}</h2>
                <span class="flex-1 h-px bg-gray-200"></span>
                <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-[#fc5648] hover:underline shrink-0">{{ __('ui.home.ver_todos') }}</a>
            </div>
            <div class="relative"
                 x-data="{
                     canScrollRight: false, canScrollLeft: false,
                     check() {
                         this.canScrollRight = this.$refs.scroller.scrollLeft + this.$refs.scroller.clientWidth < this.$refs.scroller.scrollWidth - 4;
                         this.canScrollLeft = this.$refs.scroller.scrollLeft > 4;
                     },
                 }"
                 x-init="check()">
                <div x-ref="scroller"
                     class="flex gap-3 overflow-x-auto snap-x snap-mandatory pb-2"
                     style="scrollbar-width:none"
                     @scroll="check()">
                    @foreach($ultimosPosts as $post)
                    <a href="{{ route('blog.show', $post->slug) }}"
                       class="group relative shrink-0 w-72 rounded-2xl overflow-hidden shadow-sm h-52 snap-start">
                        @if($post->imagen_portada)
                            <img src="{{ asset('storage/' . $post->imagen_portada) }}" alt="{{ $post->titulo }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="absolute inset-0 bg-gray-800"></div>
                        @endif
                        <div class="absolute inset-0 bg-linear-to-t from-black/95 via-black/20 to-transparent"></div>
                        <div class="relative z-10 h-full flex flex-col justify-end p-4">
                            <h3 class="text-sm font-extrabold text-white leading-snug line-clamp-3">{{ $post->titulo }}</h3>

                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Flechas: indican que hay más guías para ver a los lados --}}
                <button type="button"
                        x-show="canScrollLeft" x-cloak
                        @click="$refs.scroller.scrollBy({ left: -420, behavior: 'smooth' })"
                        class="absolute left-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button type="button"
                        x-show="canScrollRight" x-cloak
                        @click="$refs.scroller.scrollBy({ left: 420, behavior: 'smooth' })"
                        class="absolute right-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if(isset($ultimasRutas) && $ultimasRutas->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="w-1 h-5 rounded-full bg-[#fc5648] shrink-0"></span>
                <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ __('ui.home.rutas_titulo') }}</h2>
                <span class="flex-1 h-px bg-gray-200"></span>
                <a href="{{ route('rutas.index') }}" class="text-sm font-semibold text-[#fc5648] hover:underline shrink-0">{{ __('ui.home.ver_todos') }}</a>
            </div>
            <div class="relative"
                 x-data="{
                     canScrollRight: false, canScrollLeft: false,
                     check() {
                         this.canScrollRight = this.$refs.scroller.scrollLeft + this.$refs.scroller.clientWidth < this.$refs.scroller.scrollWidth - 4;
                         this.canScrollLeft = this.$refs.scroller.scrollLeft > 4;
                     },
                 }"
                 x-init="check()">
                <div x-ref="scroller"
                     class="flex gap-3 overflow-x-auto snap-x snap-mandatory pb-2"
                     style="scrollbar-width:none"
                     @scroll="check()">
                    @foreach($ultimasRutas as $ruta)
                    <a href="{{ route('rutas.show', $ruta->slug) }}"
                       class="group relative shrink-0 w-72 rounded-2xl overflow-hidden shadow-sm h-52 snap-start">
                        @if($ruta->imagen_portada)
                            <img src="{{ asset('storage/' . $ruta->imagen_portada) }}" alt="{{ $ruta->titulo }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="absolute inset-0 bg-gray-800 flex items-center justify-center text-4xl">🗺️</div>
                        @endif
                        <div class="absolute inset-0 bg-linear-to-t from-black/85 via-black/30 to-transparent"></div>
                        <div class="relative z-10 h-full flex flex-col justify-end p-4">
                            <p class="text-[11px] font-bold text-white/70 mb-1">{{ $ruta->puntos->count() }} {{ $ruta->puntos->count() === 1 ? 'parada' : 'paradas' }}</p>
                            <h3 class="text-sm font-extrabold text-white leading-snug line-clamp-3">{{ $ruta->titulo }}</h3>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Flechas: indican que hay más rutas para ver a los lados --}}
                <button type="button"
                        x-show="canScrollLeft" x-cloak
                        @click="$refs.scroller.scrollBy({ left: -420, behavior: 'smooth' })"
                        class="absolute left-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button type="button"
                        x-show="canScrollRight" x-cloak
                        @click="$refs.scroller.scrollBy({ left: 420, behavior: 'smooth' })"
                        class="absolute right-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        {{-- @if(isset($ultimasExperiencias) && $ultimasExperiencias->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="w-1 h-5 rounded-full bg-[#fc5648] shrink-0"></span>
                <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ __('ui.home.exp_titulo') }}</h2>
                <span class="flex-1 h-px bg-gray-200"></span>
                <a href="{{ route('experiencias.index') }}" class="text-sm font-semibold text-[#fc5648] hover:underline shrink-0">{{ __('ui.home.ver_todas') }}</a>
            </div>
            <div class="flex gap-4 overflow-x-auto snap-x snap-mandatory pb-2" style="scrollbar-width:none">
                @foreach($ultimasExperiencias as $exp)
                <a href="{{ route('experiencias.show', $exp) }}"
                   class="group relative shrink-0 w-72 rounded-2xl overflow-hidden shadow-sm h-52 snap-start">
                    @if($exp->imagen)
                        <img src="{{ asset('storage/' . $exp->imagen) }}" alt="{{ $exp->titulo }}"
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="absolute inset-0 bg-[#fff0ef] flex items-center justify-center text-4xl">🧭</div>
                    @endif
                    <div class="absolute inset-0 bg-linear-to-t from-black/85 via-black/20 to-transparent"></div>
                    <div class="relative z-10 h-full flex flex-col justify-end p-4">
                        <h3 class="text-sm font-extrabold text-white leading-snug line-clamp-3">{{ $exp->titulo }}</h3>
                        @if($exp->descripcion)
                            <p class="text-xs text-white/70 mt-1 line-clamp-2">{{ $exp->descripcion }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif --}}

        @if(isset($categoriasConPuntos) && $categoriasConPuntos->isNotEmpty())
        @foreach($categoriasConPuntos as $entry)
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-4">
                @if($entry->categoria->imagen_portada)
                    <div class="relative w-full h-24 rounded-2xl overflow-hidden">
                        <img src="{{ asset('storage/' . $entry->categoria->imagen_portada) }}"
                             alt="{{ $entry->categoria->nombre }}"
                             class="absolute inset-0 w-full h-full object-cover">
                        @if($entry->categoria->mostrar_nombre_en_imagen)
                            <div class="absolute inset-0 bg-linear-to-t from-black/70 via-black/10 to-transparent"></div>
                            <span class="absolute bottom-3 left-4 text-lg font-extrabold text-white tracking-tight">{{ $entry->categoria->nombre }}</span>
                        @endif
                        <button type="button" wire:click="$set('category', '{{ $entry->categoria->slug }}')"
                                onclick="if (window.filtrarMapa) filtrarMapa('{{ $entry->categoria->slug }}'); if (window.apagarGrupoPillsVisual) apagarGrupoPillsVisual()"
                                class="absolute bottom-3 right-4 text-xs font-semibold text-white/90 hover:text-white hover:underline">
                            {{ __('ui.home.ver_todos') }}
                        </button>
                    </div>
                @else
                    <span class="w-9 h-9 rounded-xl bg-[#fff0ef] text-[#fc5648] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-{{ $entry->categoria->icono ?: 'tag' }}"></i>
                    </span>
                    <h2 class="text-lg font-extrabold text-gray-900 tracking-tight">{{ $entry->categoria->nombre }}</h2>
                    <span class="flex-1 h-px bg-gray-200"></span>
                    <button type="button" wire:click="$set('category', '{{ $entry->categoria->slug }}')"
                                onclick="if (window.filtrarMapa) filtrarMapa('{{ $entry->categoria->slug }}'); if (window.apagarGrupoPillsVisual) apagarGrupoPillsVisual()"
                            class="text-sm font-semibold text-[#fc5648] hover:underline shrink-0">
                        {{ __('ui.home.ver_todos') }}
                    </button>
                @endif
            </div>

            <div class="relative"
                 x-data="{
                     dragging: false, startX: 0, scrollStart: 0, canScrollRight: false, canScrollLeft: false,
                     check() {
                         this.canScrollRight = this.$refs.scroller.scrollLeft + this.$refs.scroller.clientWidth < this.$refs.scroller.scrollWidth - 4;
                         this.canScrollLeft = this.$refs.scroller.scrollLeft > 4;
                     },
                 }"
                 x-init="check()">
                <div x-ref="scroller"
                     class="overflow-x-auto pb-0"
                     style="scrollbar-width:none;cursor:grab"
                     @scroll="check()"
                     @mousedown.prevent="dragging = true; startX = $event.pageX - $el.offsetLeft; scrollStart = $el.scrollLeft; $el.style.cursor = 'grabbing'"
                     @mouseup="dragging = false; $el.style.cursor = 'grab'"
                     @mouseleave="dragging = false; $el.style.cursor = 'grab'"
                     @mousemove="if (dragging) { $el.scrollLeft = scrollStart - ($event.pageX - $el.offsetLeft - startX) * 1.5; check(); }">
                    <div class="flex gap-4">
                        @foreach($entry->puntos as $atractivo)
                        <div class="shrink-0 w-68">
                            @include('puntos.partials._card_desktop')
                        </div>
                        @endforeach
                        <div class="shrink-0 w-56">
                            @if($entry->categoria->es_cliente)
                                <a href="{{ route('publicita.index') }}"
                                   class="w-full h-full flex flex-col items-center justify-center gap-2 text-center px-5 rounded-2xl bg-linear-to-br from-[#fff0ef] to-[#ffe0dd] border border-[#fc5648]/20 text-[#fc5648] hover:shadow-md transition">
                                    <span class="text-3xl">✨</span>
                                    <span class="text-sm font-bold leading-snug">Llega a más turistas y nuevos clientes</span>
                                    <span class="text-xs text-[#fc5648]/70 leading-snug">Publica tu negocio en Pindoor y comparte fotos, eventos, promociones y mucho más</span>
                                    <span class="mt-1 inline-block bg-[#fc5648] text-white text-xs font-extrabold px-4 py-1.5 rounded-full">Comenzar ahora</span>
                                </a>
                            @else
                                <button type="button" wire:click="$set('category', '{{ $entry->categoria->slug }}')"
                                    onclick="if (window.filtrarMapa) filtrarMapa('{{ $entry->categoria->slug }}'); if (window.apagarGrupoPillsVisual) apagarGrupoPillsVisual()"
                                        class="w-full h-full flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-gray-200 text-[#fc5648] hover:border-[#fc5648] hover:bg-[#fff0ef] transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <span class="text-sm font-bold">Ver más</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Flechas: indican que hay más puntos para ver a los lados --}}
                <button type="button"
                        x-show="canScrollLeft" x-cloak
                        @click="$refs.scroller.scrollBy({ left: -420, behavior: 'smooth' })"
                        class="absolute left-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
                <button type="button"
                        x-show="canScrollRight" x-cloak
                        @click="$refs.scroller.scrollBy({ left: 420, behavior: 'smooth' })"
                        class="absolute right-1 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white shadow-md border border-gray-100 flex items-center justify-center text-gray-500 hover:text-[#fc5648] hover:shadow-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
        @endforeach
        @endif

    @endif

    {{-- Spinner de carga --}}
    <div wire:loading class="flex justify-center py-12">
        <svg class="animate-spin h-8 w-8 text-[#fc5648]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
    </div>

    {{-- Grid de atractivos --}}
    <div wire:loading.remove class="pb-12">
        @if($hayFiltros)
        @if($atractivos->count())
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($atractivos as $atractivo)
                    @include('puntos.partials._card_desktop')
                @endforeach
            </div>
            <div class="mt-12 mb-4 flex justify-center">
                {{ $atractivos->links() }}
            </div>
        @elseif($panoramas->isEmpty() && $artistas->isEmpty() && $operadores->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-16 text-center border-2 border-dashed border-gray-200">
                <div class="text-5xl mb-4">🕵️‍♂️</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ __('ui.explorar.sin_resultados') }}</h3>
                <p class="text-gray-400 mb-5 text-sm">{{ __('ui.home.sin_resultados_sub') }}</p>
                <button wire:click="$set('search', ''); $set('category', ''); $set('grupo', '')"
                        onclick="document.getElementById('barra-buscar-input').value = ''; if (window.filtrarMapaPorGrupo) filtrarMapaPorGrupo('')"
                        class="bg-[#fc5648] text-white px-5 py-2.5 rounded-xl font-bold hover:bg-gray-900 transition text-sm">
                    {{ __('ui.explorar.ver_todos') }}
                </button>
            </div>
        @endif
        @endif

        @if($panoramas->isNotEmpty())
        <div class="mt-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-700">
                    {{ __('ui.home.panoramas_con') }} "{{ $search }}"
                </h2>
                <a href="{{ route('atractivos.panoramas') }}" class="text-xs text-[#fc5648] font-semibold hover:underline">
                    {{ __('ui.home.ver_todos') }}
                </a>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($panoramas as $panorama)
                <a href="{{ route('panoramas.show', $panorama) }}"
                   class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group">
                    @if($panorama->imagen)
                        <img src="{{ asset('storage/' . $panorama->imagen) }}" alt="{{ $panorama->titulo }}"
                             class="w-full h-36 object-cover group-hover:scale-105 transition duration-300">
                    @else
                        <div class="w-full h-36 bg-linear-to-br from-[#fff0ef] to-[#ffe4e1] flex items-center justify-center text-3xl">🗓</div>
                    @endif
                    <div class="p-3">
                        <p class="font-bold text-gray-900 text-sm leading-snug line-clamp-2">{{ $panorama->titulo }}</p>
                        <p class="text-xs text-[#fc5648] font-semibold mt-1">
                            {{ \Carbon\Carbon::parse($panorama->fecha)->locale('es')->isoFormat('D MMM') }}
                            @if($panorama->fecha_fin && $panorama->fecha_fin != $panorama->fecha)
                                — {{ \Carbon\Carbon::parse($panorama->fecha_fin)->locale('es')->isoFormat('D MMM') }}
                            @endif
                        </p>
                        @if($panorama->ubicacion)
                            <p class="text-xs text-gray-400 mt-0.5 truncate">📍 {{ $panorama->ubicacion }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($artistas->isNotEmpty())
        <div class="mt-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-700">
                    Artistas y agrupaciones con "{{ $search }}"
                </h2>
                <a href="{{ route('artista.index') }}" class="text-xs text-violet-600 font-semibold hover:underline">
                    Ver La Escena
                </a>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($artistas as $artista)
                <a href="{{ route('artista.show', $artista->slug) }}"
                   class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group flex items-center gap-4 p-4">
                    @if($artista->imagen_perfil)
                        <img src="{{ asset('storage/' . $artista->imagen_perfil) }}" alt="{{ $artista->nombre }}"
                             class="w-14 h-14 rounded-full object-cover border-2 border-violet-100 shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-full bg-violet-100 flex items-center justify-center text-2xl shrink-0">
                            {{ \App\Models\Artista::DISCIPLINAS[$artista->disciplina]['emoji'] ?? '🎨' }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm leading-snug group-hover:text-violet-700 transition">{{ $artista->nombre }}</p>
                        <p class="text-xs text-violet-600 font-semibold mt-0.5">{{ $artista->disciplinaLabel() }}</p>
                        @if($artista->ciudad)
                            <p class="text-xs text-gray-400 mt-0.5 truncate">📍 {{ $artista->ciudad }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($operadores->isNotEmpty())
        <div class="mt-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-700">
                    Operadores turísticos con "{{ $search }}"
                </h2>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($operadores as $operador)
                <a href="{{ route('operador.show', $operador->slug) }}"
                   class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden group flex items-center gap-4 p-4">
                    @if($operador->imagen_perfil)
                        <img src="{{ asset('storage/' . $operador->imagen_perfil) }}" alt="{{ $operador->nombre }}"
                             class="w-14 h-14 rounded-full object-cover border-2 border-teal-100 shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-full bg-teal-100 flex items-center justify-center text-2xl shrink-0">🧭</div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 text-sm leading-snug group-hover:text-teal-700 transition">{{ $operador->nombre }}</p>
                        <p class="text-xs text-teal-600 font-semibold mt-0.5">Operador turístico</p>
                        @if($operador->ciudad)
                            <p class="text-xs text-gray-400 mt-0.5 truncate">📍 {{ $operador->ciudad }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
