@extends('layouts.pindoor')

@section('title', 'Explorar atractivos — Pindoor')
@section('description', 'Todos los lugares, restaurantes, cafeterías, museos y atracciones de Valparaíso.')
@section('bodyClass', 'bg-gray-50 text-gray-900 font-serif')

@section('content')
<div class="max-w-2xl mx-auto px-3 py-4">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('puntos.index') }}"
           class="flex items-center justify-center w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm text-gray-500 hover:text-gray-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-lg font-bold text-gray-900">Explorar atractivos</h1>
    </div>

    {{-- Buscador --}}
    <form method="GET" action="{{ route('puntos.explorar') }}" class="mb-4">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar lugar, categoría…"
                   class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-gray-200 bg-white shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-[#fc5648]/30">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </form>

    {{-- Pills categorías --}}
    <div class="overflow-x-auto pb-1 mb-4" style="-ms-overflow-style:none;scrollbar-width:none;">
        <div class="flex gap-2 w-max">
            <a href="{{ route('puntos.explorar', array_filter(['search' => request('search')])) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap
                      {{ !request('category') ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300' }}">
                Todos
            </a>
            @foreach($categorias as $cat)
            <a href="{{ route('puntos.explorar', array_filter(['category' => $cat->slug, 'search' => request('search')])) }}"
               class="px-4 py-1.5 rounded-full text-xs font-semibold border transition-colors whitespace-nowrap
                      {{ request('category') == $cat->slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-300' }}">
                {{ $cat->nombre }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- Filtro activo --}}
    @if(request()->anyFilled(['category', 'search']))
    <div class="mb-4 px-3 py-2 bg-[#fff0ef] rounded-xl flex items-center justify-between">
        <span class="text-xs text-[#fc5648] font-semibold">
            @if(request('search')) "{{ request('search') }}"
            @elseif(request('category')) {{ $categorias->firstWhere('slug', request('category'))?->nombre }}
            @endif
        </span>
        <a href="{{ route('puntos.explorar') }}" class="text-xs text-gray-400 font-bold">✕ Borrar</a>
    </div>
    @endif

    {{-- Resultados --}}
    @if($atractivos->count())
        <p class="text-xs text-gray-400 mb-3">{{ $atractivos->total() }} atractivos</p>
        <div class="grid grid-cols-2 gap-3">
            @foreach($atractivos as $atractivo)
                @include('puntos.partials._card_mobile')
            @endforeach
        </div>
        <div class="mt-8 flex justify-center">
            {{ $atractivos->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-5xl mb-3">🕵️‍♂️</div>
            <p class="font-bold text-gray-700 mb-1">Sin resultados</p>
            <p class="text-sm text-gray-400 mb-4">Prueba con otra búsqueda</p>
            <a href="{{ route('puntos.explorar') }}" class="text-sm font-bold text-[#fc5648] underline">Ver todos</a>
        </div>
    @endif

</div>
@endsection
