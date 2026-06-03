@extends('layouts.pindoor')

@section('title', 'Buscar — Pindoor')
@section('bodyClass', 'bg-gray-50 text-gray-900 font-sans')

@section('content')
<div class="max-w-2xl mx-auto px-4 pt-5 pb-10">

    {{-- Buscador libre --}}
    <form action="{{ route('puntos.index') }}" method="GET">
        <div class="relative">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Café, mirador, museo, ascensor…"
                   autofocus
                   class="w-full pl-12 pr-4 py-4 rounded-2xl border border-gray-200 bg-white shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/40">
        </div>
        <button type="submit"
                class="mt-3 w-full bg-[#fc5648] text-white font-bold py-3 rounded-2xl text-sm shadow">
            Buscar
        </button>
    </form>

    {{-- Categorías agrupadas --}}
    <div class="mt-8 space-y-7">
        @foreach($grupos as $key => $grupo)
        <div>
            {{-- Título del grupo --}}
            <div class="flex items-center gap-2 mb-3">
                <span class="text-base">{{ $grupo['emoji'] }}</span>
                <h2 class="text-sm font-bold text-gray-900 tracking-tight">{{ $grupo['label'] }}</h2>
                <span class="flex-1 h-px bg-gray-200"></span>
            </div>

            {{-- Grid de botones --}}
            <div class="grid grid-cols-2 gap-2">
                @foreach($grupo['items'] as $cat)
                <a href="{{ route('puntos.index', ['category' => $cat->slug]) }}"
                   class="flex items-center gap-3 bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm hover:border-[#fc5648] hover:shadow transition group">
                    @php
                        $iconoEmoji = match($cat->slug) {
                            'comer'        => '🍔',
                            'cafeterias'   => '☕',
                            'picadas'      => '🥘',
                            'alojar'       => '🛏️',
                            'museos'       => '🏛️',
                            'arquitectura' => '🏗️',
                            'monumentos'   => '🗿',
                            'estatuas'     => '🗽',
                            'miradores'    => '📷',
                            'cultura'      => '🎭',
                            'ascensores'   => '🚡',
                            'naturaleza'   => '🌿',
                            'street-art'   => '🎨',
                            'artesania'    => '🧶',
                            'tiendas'      => '🛍️',
                            default        => '📍',
                        };
                    @endphp
                    <span class="text-xl w-7 text-center shrink-0">{{ $iconoEmoji }}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-800 group-hover:text-[#fc5648] transition truncate">
                            {{ $cat->nombre }}
                        </p>
                        <p class="text-[11px] text-gray-400">{{ $cat->puntos_interes_count }} lugares</p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
