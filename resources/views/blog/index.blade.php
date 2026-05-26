@extends('layouts.pindoor')

@section('title', 'Blog — Pindoor.cl')
@section('description', 'Historias, guías y rincones de Valparaíso.')
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-12 md:py-16">

    {{-- Encabezado --}}
    <div class="mb-12">
        <p class="text-xs font-black uppercase tracking-[.25em] text-[#fc5648] mb-2">Pindoor</p>
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight">Blog</h1>
        <p class="mt-3 text-lg text-gray-500" style="font-family:'Lora',serif;">
            Historias, guías y rincones de Valparaíso.
        </p>
    </div>

    @if($posts->isEmpty())
    <div class="bg-white rounded-3xl border border-gray-100 px-8 py-20 text-center text-gray-400">
        <div class="text-5xl mb-4">✍️</div>
        <p class="font-semibold text-lg">Pronto habrá artículos aquí.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($posts as $post)
        <a href="{{ route('blog.show', $post->slug) }}"
           class="group bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden
                  hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">

            {{-- Portada --}}
            <div class="aspect-[16/10] overflow-hidden bg-gray-100">
                @if($post->imagen_portada_url)
                    <img src="{{ $post->imagen_portada_url }}" alt="{{ $post->titulo }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="w-full h-full flex items-center justify-center text-5xl text-gray-200 bg-gray-50">
                        📝
                    </div>
                @endif
            </div>

            {{-- Contenido --}}
            <div class="p-6 flex flex-col flex-1">
                <p class="text-xs font-black uppercase tracking-widest text-[#fc5648] mb-2">
                    {{ $post->publicado_en?->translatedFormat('d \d\e F, Y') }}
                </p>
                <h2 class="text-lg font-extrabold text-gray-900 leading-snug
                           group-hover:text-[#fc5648] transition line-clamp-2 mb-2">
                    {{ $post->titulo }}
                </h2>
                @if($post->resumen)
                <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 flex-1"
                   style="font-family:'Lora',serif;">
                    {{ $post->resumen }}
                </p>
                @endif
                <span class="mt-4 text-sm font-bold text-[#fc5648] group-hover:underline">
                    Leer →
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>
@endsection
