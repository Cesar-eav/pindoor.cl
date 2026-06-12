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

{{-- Próximos panoramas --}}
@if(isset($proximosPanoramas) && $proximosPanoramas->isNotEmpty() && !request()->filled('lat') && !request()->filled('category') && !request()->filled('search'))
<div id="panoramas-mobile-section" class="px-3 pt-3">
    <div class="flex items-center gap-2 mb-2">
        <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
        <span class="text-md font-bold text-gray-900 tracking-tight">Panoramas</span>
        <span class="flex-1 h-px bg-gray-200"></span>
        <a href="{{ route('atractivos.panoramas') }}" class="text-[11px] text-[#fc5648] font-semibold shrink-0">Ver todos →</a>
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
        <a href="{{ route('panoramas.show', $p) }}"
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

    {{-- Entradas del blog --}}
    @if(isset($ultimosPosts) && $ultimosPosts->isNotEmpty() && !request()->filled('category') && !request()->filled('search'))
    <div id="blog-mobile-section" class="mt-5">
        <div class="flex items-center gap-2 mb-3 px-3">
            <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
            <h2 class="text-sm font-bold text-gray-900 tracking-tight">Blog</h2>
            <span class="flex-1 h-px bg-gray-200"></span>
            <a href="{{ route('blog.index') }}" class="text-[11px] font-semibold text-[#fc5648] shrink-0">Ver todos →</a>
        </div>

        <div class="flex gap-3 overflow-x-auto pb-2 px-3" style="-ms-overflow-style:none;scrollbar-width:none;">
            @foreach($ultimosPosts as $post)
            <a href="{{ route('blog.show', $post->slug) }}"
               class="relative shrink-0 rounded-2xl overflow-hidden shadow-sm"
               style="width:72vw;height:11rem;">
                @if($post->imagen_portada)
                    <img src="{{ asset('storage/' . $post->imagen_portada) }}"
                         alt="{{ $post->titulo }}"
                         class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 bg-gray-800"></div>
                @endif
                <div class="absolute inset-0 bg-linear-to-t from-black/85 via-black/25 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-4">
                    <h3 class="text-sm font-bold text-white leading-snug line-clamp-3">{{ $post->titulo }}</h3>
                    @if($post->resumen)
                        <p class="text-[11px] text-white/70 mt-1 line-clamp-2">{{ $post->resumen }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Últimas experiencias --}}
    @if(isset($ultimasExperiencias) && $ultimasExperiencias->isNotEmpty() && !request()->filled('category') && !request()->filled('search'))
    <div id="experiencias-mobile-section" class="mt-5">
        <div class="flex items-center gap-2 mb-3 px-3">
            <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
            <h2 class="text-sm font-bold text-gray-900 tracking-tight">Experiencias</h2>
            <span class="flex-1 h-px bg-gray-200"></span>
            <a href="{{ route('experiencias.index') }}" class="text-[11px] font-semibold text-[#fc5648] shrink-0">Ver todas →</a>
        </div>
        <div class="flex gap-3 px-3" >
            @foreach($ultimasExperiencias as $exp)
            <a href="{{ route('experiencias.show', $exp) }}"
               class="relative flex-1 rounded-2xl overflow-hidden shadow-sm" style="height:10rem;">
                @if($exp->imagen)
                    <img src="{{ asset('storage/' . $exp->imagen) }}"
                         alt="{{ $exp->titulo }}"
                         class="absolute inset-0 w-full h-full object-cover">
                @else
                    <div class="absolute inset-0 bg-[#fff0ef] flex items-center justify-center text-4xl">🧭</div>
                @endif
                <div class="absolute inset-0 bg-linear-to-t from-black/85 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-3">
                    <h3 class="text-xs font-bold text-white leading-snug line-clamp-3">{{ $exp->titulo }}</h3>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

{{-- Resultados --}}

<div id="vista-listado-mobile" class="flex-1 px-3 pt-4 pb-6">

    @if($atractivos->count())
    {{-- Cabecera sección atractivos --}}
    <div class="flex items-center gap-2 mb-3">
        <span class="w-1 h-4 rounded-full bg-gray-800 shrink-0"></span>
        <h2 class="text-md font-bold text-gray-900 tracking-tight">Atractivos</h2>
        <span class="flex-1 h-px bg-gray-200"></span>
        <a href="{{ route('puntos.explorar', request()->only(['search','category'])) }}"
           class="text-[11px] font-semibold text-[#fc5648] shrink-0">Ver todos →</a>
    </div>

    <div class="grid grid-cols-2 gap-3">
        @foreach($atractivos->take(51) as $atractivo)
            @include('puntos.partials._card_mobile')
        @endforeach
    </div>

    @if($atractivos->total() > 51)
    <div class="mt-3 text-center">
        <a href="{{ route('puntos.explorar', request()->only(['search','category'])) }}"
           class="inline-block text-xs font-bold text-white bg-[#fc5648] px-5 py-2 rounded-full shadow">
            Ver todos los atractivos
        </a>
    </div>
    @endif



    {{-- Mensaje fase piloto --}}
    <div class="mt-6 mb-2 px-4 py-4 bg-[#fff0ef] rounded-2xl text-center">
        <p class="text-xs text-gray-700 leading-relaxed">
            Pindoor está en <span class="font-bold text-[#fc5648]">fase piloto</span>. Estamos creciendo y mejorando cada día.
            Si tienes sugerencias o quieres sumarte,
            <a href="{{ route('contacto.index') }}" class="font-bold text-[#fc5648] underline underline-offset-2">contáctanos</a>.
        </p>
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
