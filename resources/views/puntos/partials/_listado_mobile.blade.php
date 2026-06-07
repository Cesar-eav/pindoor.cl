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
    <div class="flex overflow-x-auto pb-1 scrollbar-hide">
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
@endif

    {{-- Última entrada del blog --}}
    @if(isset($ultimoPost) && $ultimoPost && !request()->filled('category') && !request()->filled('search'))
    <div id="blog-mobile-section" class="mt-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-1 h-4 rounded-full bg-[#fc5648] shrink-0"></span>
            <h2 class="text-sm font-bold text-gray-900 tracking-tight">Blog</h2>
            <span class="flex-1 h-px bg-gray-200"></span>
            <a href="{{ route('blog.index') }}" class="text-[11px] font-semibold text-[#fc5648] shrink-0">Ver todos →</a>
        </div>

        <a href="{{ route('blog.show', $ultimoPost->slug) }}"
           class="block relative  overflow-hidden shadow-sm min-h-40">
            @if($ultimoPost->imagen_portada)
                <img src="{{ asset('storage/' . $ultimoPost->imagen_portada) }}"
                     alt="{{ $ultimoPost->titulo }}"
                     class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-gray-800"></div>
            @endif
            <div class="absolute inset-0 bg-linear-to-t from-black/80 via-black/40 to-transparent"></div>
            <div class="relative z-10 p-4 flex flex-col justify-end min-h-40">
                <span class="text-[10px] font-bold text-white/70 uppercase tracking-widest mb-1">Última entrada</span>
                <h3 class="text-sm font-bold text-white leading-snug line-clamp-2">{{ $ultimoPost->titulo }}</h3>
                @if($ultimoPost->resumen)
                <p class="text-[11px] text-white/75 mt-1 line-clamp-2">{{ $ultimoPost->resumen }}</p>
                @endif
            </div>
        </a>
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

    {{-- Grid con scroll interno --}}
    <div class="overflow-y-auto max-h-250 rounded-2xl" style="-ms-overflow-style:none;scrollbar-width:none;">
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
    </div>



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
