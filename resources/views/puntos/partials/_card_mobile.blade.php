<article class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col font-sans">
    <div class="relative">
        <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}" class="block overflow-hidden">
            @if($atractivo->imagenPrincipal)
                <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                     alt="{{ $atractivo->title }}"
                     class="w-full h-36 object-cover">
            @else
                <div class="w-full h-44 bg-gray-100 flex items-center justify-center text-4xl">📍</div>
            @endif
        </a>

        @if($atractivo->categoria)
        <span class="absolute top-3 left-3 bg-[#fc5648] text-white text-[9px] uppercase tracking-widest font-bold px-2.5 py-1 rounded-full shadow">
            {{ $atractivo->categoria->nombre }}
        </span>
        @endif

        @if($atractivo->es_cliente && $atractivo->oferta_del_dia && $atractivo->oferta_activa)
        <span class="absolute top-3 right-3 bg-amber-400 text-amber-900 text-[9px] uppercase font-bold px-2.5 py-1 rounded-full shadow">
            Oferta hoy
        </span>
        @endif

        @if($atractivo->es_cliente && $atractivo->imagen_perfil)
        <img src="{{ asset('storage/' . $atractivo->imagen_perfil) }}"
             class="absolute bottom-2 right-2 w-8 h-8 rounded-lg object-cover border-2 border-white shadow">
        @endif
    </div>

    <div class="px-2.5 py-2">
        @if(isset($atractivo->distancia))
        <span class="inline-block mb-1 text-[9px] font-bold text-[#fc5648] bg-orange-50 border border-orange-100 px-1.5 py-0.5 rounded-lg">
            {{ number_format($atractivo->distancia / 1000, 1) }} km
        </span>
        @endif
        <h3 class="text-xs font-bold text-gray-900 leading-tight line-clamp-2">
            <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
               class="hover:text-[#fc5648] transition">{{ $atractivo->title }}</a>
        </h3>
        @if($atractivo->sector)
        <p class="text-[10px] text-[#fc5648] font-semibold mt-0.5 truncate">{{ $atractivo->sector }}</p>
        @endif
    </div>
</article>
