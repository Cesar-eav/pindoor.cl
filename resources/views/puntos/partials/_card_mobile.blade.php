<article class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 flex flex-col">
    <div class="relative">
        <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}" class="block overflow-hidden">
            @if($atractivo->imagenPrincipal)
                <img src="{{ asset('storage/' . $atractivo->imagenPrincipal->ruta) }}"
                     alt="{{ $atractivo->title }}"
                     class="w-full h-96 object-cover">
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
             class="absolute bottom-3 right-3 w-10 h-10 rounded-xl object-cover border-2 border-white shadow">
        @endif
    </div>

    <div class="px-4 py-3">
        @if(isset($atractivo->distancia))
        <span class="inline-block mb-1.5 text-[10px] font-bold text-[#fc5648] bg-orange-50 border border-orange-100 px-2 py-0.5 rounded-lg">
            A {{ number_format($atractivo->distancia / 1000, 1) }} km de ti
        </span>
        @endif
        <h3 class="text-base font-bold text-gray-900 leading-tight mb-1">
            <a href="{{ route('atractivos.show', $atractivo->slug ?? $atractivo->id) }}"
               class="hover:text-[#fc5648] transition">{{ $atractivo->title }}</a>
        </h3>
        @if($atractivo->sector || $atractivo->direccion)
        <div class="flex items-center gap-1.5 text-xs mb-2 flex-wrap">
            @if($atractivo->sector)
            <span class="flex items-center gap-1 text-[#fc5648] font-semibold">
                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                </svg>
                {{ $atractivo->sector }}
            </span>
            @endif
            @if($atractivo->sector && $atractivo->direccion)
            <span class="text-gray-300">·</span>
            @endif
            @if($atractivo->direccion)
            <span class="text-gray-400">{{ $atractivo->direccion }}</span>
            @endif
        </div>
        @endif
        <p class="text-gray-500 text-sm leading-relaxed">
            {{ Str::limit(strip_tags($atractivo->description), 250) }}
        </p>
    </div>
</article>
