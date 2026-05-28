@extends('layouts.pindoor')

@section('title', 'Experiencias — Pindoor.cl')
@section('canonical', route('experiencias.index'))
@section('description', 'Descubre experiencias únicas en Valparaíso: clases de cueca, talleres de arte, tours culturales, gastronomía y más. Para turistas y locales.')
@section('bodyClass', 'bg-gray-100 text-gray-900 font-sans')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8"
     x-data="{
         open: false,
         current: 0,
         images: {{ $allImages->toJson() }},
         openAt(i) { this.current = i; this.open = true; history.pushState({ lightbox: true }, ''); },
         prev() { this.current = (this.current - 1 + this.images.length) % this.images.length; },
         next() { this.current = (this.current + 1) % this.images.length; },
         close() { if (this.open) { this.open = false; history.back(); } }
     }"
     @keydown.escape.window="close()"
     @keydown.arrow-left.window="open && prev()"
     @keydown.arrow-right.window="open && next()"
     @popstate.window="open && (open = false)">

    {{-- Header --}}
    <section class="mb-8 text-center max-w-2xl mx-auto px-4 font-sans">
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-slate-950 mb-3">
            Expe<span class="text-[#fc5648]">rien</span>cias
        </h1>
        <p class="text-slate-500 text-base md:text-lg font-medium inline-flex flex-wrap items-center justify-center gap-2 bg-slate-50 border border-slate-100 rounded-full px-6 py-2 shadow-sm">
            <span>
                <strong class="text-slate-950 font-extrabold text-2xl align-bottom">{{ $experiencias->count() }}</strong>
                experiencias para vivir en Valparaíso
            </span>
        </p>
    </section>

    {{-- Filtros --}}
    @if($experiencias->isNotEmpty())
    <div class="flex flex-wrap gap-2 justify-center mb-8">
        <a href="{{ route('experiencias.index') }}"
           class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                  {{ !$catActiva ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
            Todas
        </a>
        @foreach($categorias as $slug => $cat)
            @if($experiencias->where('categoria', $slug)->isNotEmpty())
            <a href="{{ route('experiencias.index', ['categoria' => $slug]) }}"
               class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                      {{ $catActiva === $slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300 hover:border-gray-500' }}">
                {{ $cat['emoji'] }} {{ $cat['label'] }}
            </a>
            @endif
        @endforeach
        @if($experiencias->where('es_gratuito', true)->isNotEmpty())
        <a href="{{ route('experiencias.index', ['categoria' => 'gratuito']) }}"
           class="px-4 py-1.5 rounded-full text-xs font-bold border transition-colors
                  {{ $catActiva === 'gratuito' ? 'bg-green-700 text-white border-green-700' : 'bg-green-50 text-green-700 border-green-200 hover:border-green-500' }}">
            🎟️ Gratis
        </a>
        @endif
    </div>
    @endif

    @if($coleccion->isEmpty())
        <div class="text-center py-20">
            <div class="text-5xl mb-3">🎭</div>
            <p class="font-bold text-gray-700 mb-1">No hay experiencias publicadas aún</p>
            <p class="text-sm text-gray-400">Vuelve pronto para descubrir lo que hay en Valparaíso.</p>
        </div>
    @else

    {{-- Grid de experiencias --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($coleccion as $exp)
        @php $idx = $startIndexMap[$exp->id] ?? 0; @endphp

        <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-zoom-in flex flex-col"
             @click="openAt({{ $idx }})">

            {{-- Imagen --}}
            <div class="relative overflow-hidden bg-gray-100 aspect-video sm:bg-gray-900">
                @if($exp->imagen)
                    <img src="{{ asset('storage/' . $exp->imagen) }}"
                         aria-hidden="true"
                         class="hidden sm:block absolute inset-0 w-full h-full object-cover blur-2xl scale-110 brightness-50 pointer-events-none">
                    <img src="{{ asset('storage/' . $exp->imagen) }}"
                         alt="{{ $exp->titulo }}"
                         class="relative z-10 w-full h-full object-cover sm:object-contain transition-transform duration-500 group-hover:scale-105">
                @else
                    <div class="w-full h-full flex items-center justify-center text-5xl text-gray-300">
                        {{ isset($categorias[$exp->categoria]) ? $categorias[$exp->categoria]['emoji'] : '✨' }}
                    </div>
                @endif

                {{-- Badge categoría --}}
                @if($exp->categoria && isset($categorias[$exp->categoria]))
                <div class="absolute top-2 left-2 z-20 bg-black/60 backdrop-blur-sm text-white text-sm font-bold px-2 py-0.5 rounded-lg">
                    {{ $categorias[$exp->categoria]['emoji'] }} {{ $categorias[$exp->categoria]['label'] }}
                </div>
                @endif

                {{-- Badge precio/gratis --}}
                @if($exp->es_gratuito)
                <div class="absolute top-2 right-2 z-20 bg-green-500 text-white text-[12px] font-bold px-2 py-0.5 rounded-lg">
                    🎟️ Gratis
                </div>
                @elseif($exp->precio)
                <div class="absolute top-2 right-2 z-20 bg-black/60 backdrop-blur-sm text-white text-[12px] font-bold px-2 py-0.5 rounded-lg">
                    ${{ number_format($exp->precio, 0, ',', '.') }}
                </div>
                @endif

                {{-- Badge fotos --}}
                @php $totalFotos = ($exp->imagen ? 1 : 0) + $exp->imagenes->count(); @endphp
                @if($totalFotos > 1)
                <div class="absolute bottom-2 left-2 z-20 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $totalFotos }}
                </div>
                @endif
            </div>

            {{-- Contenido --}}
            <div class="p-4 flex-1 flex flex-col gap-1.5">
                <p class="font-bold text-gray-900 leading-snug">{{ $exp->titulo }}</p>

                @if($exp->proveedor)
                <p class="text-xs text-gray-500">👤 {{ $exp->proveedor }}</p>
                @endif

                @if($exp->descripcion)
                <p class="text-xs text-gray-500 line-clamp-2">{{ $exp->descripcion }}</p>
                @endif

                <div class="flex flex-wrap gap-x-3 gap-y-1 mt-1">
                    @if(!empty($exp->dias_semana))
                    <span class="text-xs text-[#fc5648] font-semibold">🔁 {{ $exp->dias_semana_label }}</span>
                    @endif
                    @if($exp->hora)
                    <span class="text-xs text-gray-500">🕐 {{ $exp->hora }}</span>
                    @endif
                    @if($exp->duracion)
                    <span class="text-xs text-gray-500">⏱ {{ $exp->duracion }}</span>
                    @endif
                </div>

                @if($exp->ubicacion)
                <p class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                    <svg class="w-3 h-3 shrink-0 text-[#fc5648]" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $exp->ubicacion }}
                </p>
                @endif

                @if($exp->nivel && $exp->nivel !== 'todos')
                <span class="text-[10px] font-bold bg-[#fff0ef] text-[#fc5648] px-2 py-0.5 rounded-full self-start mt-0.5">
                    {{ \App\Models\Experiencia::NIVELES[$exp->nivel] ?? $exp->nivel }}
                </span>
                @endif

                @if($exp->enlace)
                <a href="{{ $exp->enlace }}" target="_blank" rel="noopener noreferrer"
                   @click.stop
                   class="mt-auto pt-2 inline-flex items-center gap-1.5 text-xs font-bold text-[#fc5648] hover:text-[#d94439] transition">
                    Reservar / Más info
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Lightbox --}}
    <div x-show="open" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
         @click.self="close()" style="display:none;">

        <button @click="close()"
                class="absolute top-4 right-4 text-white bg-white/10 hover:bg-white/25 rounded-full p-2.5 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <button @click="prev()"
                class="absolute left-3 md:left-6 text-white bg-white/10 hover:bg-white/25 rounded-full p-3 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <div class="flex flex-col items-center gap-4 px-16 max-w-2xl w-full">
            <template x-if="images[current] && images[current].src">
                <img :src="images[current].src" :alt="images[current].titulo"
                     class="max-h-[70vh] w-auto object-contain rounded-xl shadow-2xl select-none">
            </template>
            <div class="text-center text-white space-y-1">
                <p class="font-bold text-lg" x-text="images[current] && images[current].titulo"></p>
                <p class="text-sm text-white/70" x-show="images[current] && images[current].proveedor"
                   x-text="images[current] ? '👤 ' + images[current].proveedor : ''"></p>
                <p class="text-sm text-white/70" x-show="images[current] && images[current].ubicacion"
                   x-text="images[current] ? '📍 ' + images[current].ubicacion : ''"></p>
                <p class="text-sm text-white/70" x-show="images[current] && images[current].precio"
                   x-text="images[current] ? images[current].precio : ''"></p>
                <a x-show="images[current] && images[current].enlace"
                   :href="images[current] ? images[current].enlace : '#'"
                   target="_blank" rel="noopener noreferrer"
                   class="mt-1 inline-flex items-center gap-1.5 text-xs font-bold bg-white/10 hover:bg-white/20 text-white px-4 py-1.5 rounded-full transition">
                    Reservar / Más información
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
            <p class="text-white/40 text-sm">
                <span x-text="current + 1"></span> / {{ $allImages->count() }}
            </p>
        </div>

        <button @click="next()"
                class="absolute right-3 md:right-6 text-white bg-white/10 hover:bg-white/25 rounded-full p-3 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    @endif
</div>
@endsection
