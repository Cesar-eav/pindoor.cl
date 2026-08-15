@php
    use Illuminate\Support\Str;
@endphp

@extends('layouts.pindoor')

@section('title', 'Re-vival · Revive los panoramas de Valparaíso | Pindoor')
@section('canonical', route('revival.index'))
@section('description', 'Revive los mejores eventos y panoramas que ya pasaron en Valparaíso — un balance en fotos, video y palabras para entusiasmarte con las próximas ediciones.')
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12 md:py-16">

    {{-- Breadcrumbs --}}
    <nav class="flex items-center space-x-2 text-xs font-bold uppercase tracking-widest text-gray-400 mb-6">
        <a href="{{ route('atractivos.panoramas') }}" class="hover:text-[#fc5648] transition">Panoramas</a>
        <span class="text-gray-300">/</span>
        <span class="text-[#fc5648]">Re-vival</span>
    </nav>

    {{-- Encabezado --}}
    <div class="mb-12">
        <p class="text-xs font-black uppercase tracking-[.25em] text-[#fc5648] mb-2">Pindoor · Panoramas</p>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">🎬 Re-vival</h1>
        <p class="mt-3 text-lg text-gray-500" style="font-family:'Lora',serif;">
            Un balance de los panoramas que ya vivimos, para entusiasmarte con lo que viene.
        </p>
    </div>

    @if($revivals->isEmpty())
    <div class="bg-white rounded-3xl border border-gray-100 px-8 py-20 text-center text-gray-400">
        <div class="text-5xl mb-4">🎬</div>
        <p class="font-semibold text-lg">Pronto revivirá aquí el primer panorama.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($revivals as $revival)
        <a href="{{ route('revival.show', $revival->slug) }}"
           class="group bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden
                  hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">

            {{-- Portada --}}
            <div class="aspect-16/10 overflow-hidden bg-gray-100">
                @if($revival->imagen_portada_url)
                    <img src="{{ $revival->imagen_portada_url }}" alt="{{ $revival->titulo }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center text-5xl text-gray-200 bg-gray-50">
                        🎬
                    </div>
                @endif
            </div>

            {{-- Contenido --}}
            <div class="p-6 flex flex-col flex-1">
                <p class="text-xs font-black uppercase tracking-widest text-[#fc5648] mb-2">
                    {{ $revival->publicado_en?->translatedFormat('d \d\e F, Y') }}
                </p>
                <h2 class="text-lg font-extrabold text-gray-900 leading-snug
                           group-hover:text-[#fc5648] transition line-clamp-2 mb-2">
                    {{ $revival->titulo }}
                </h2>
                <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 flex-1"
                   style="font-family:'Lora',serif;">
                    {{ Str::limit(strip_tags($revival->contenido), 140) }}
                </p>
                @if($revival->autor)
                <p class="text-xs text-gray-400 mt-3">Por {{ $revival->autor }}</p>
                @endif
                <span class="mt-4 text-sm font-bold text-[#fc5648] group-hover:underline">
                    Revivir →
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>
@endsection
