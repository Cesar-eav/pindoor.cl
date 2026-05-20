@extends('layouts.pindoor')

@section('title', 'Artistas en Valparaíso — Pindoor')
@section('description', 'Descubre artistas de Valparaíso: músicos, artistas visuales, fotógrafos, bailarines y más.')
@section('canonical', route('artistas.index'))
@section('bodyClass', 'bg-gray-100 text-gray-900 font-sans')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">

    {{-- Cabecera --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Artistas en Valparaíso</h1>
        <p class="text-sm text-gray-500 mt-1">Músicos, visuales, fotógrafos, bailarines y más</p>
    </div>

    {{-- Filtro por disciplina --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('artistas.index') }}"
           class="px-3 py-1.5 rounded-full text-xs font-bold border transition
                  {{ !$disciplina ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-gray-600 border-gray-200 hover:border-violet-400 hover:text-violet-600' }}">
            Todos
        </a>
        @foreach($disciplinas as $slug => $d)
            <a href="{{ route('artistas.index', ['disciplina' => $slug]) }}"
               class="px-3 py-1.5 rounded-full text-xs font-bold border transition
                      {{ $disciplina === $slug ? 'bg-violet-600 text-white border-violet-600' : 'bg-white text-gray-600 border-gray-200 hover:border-violet-400 hover:text-violet-600' }}">
                {{ $d['emoji'] }} {{ $d['label'] }}
            </a>
        @endforeach
    </div>

    {{-- Grid de artistas --}}
    @if($artistas->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-8 py-16 text-center">
            <div class="text-5xl mb-4">🎨</div>
            <p class="text-gray-500 font-semibold">No hay artistas en esta categoría aún.</p>
            <a href="{{ route('artista.register') }}"
               class="inline-block mt-4 text-sm font-bold text-violet-600 hover:underline">
                ¿Eres artista? Crea tu perfil →
            </a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($artistas as $artista)
                <a href="{{ route('artista.show', $artista->slug) }}"
                   class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all overflow-hidden group flex flex-col">

                    {{-- Banner + avatar --}}
                    <div class="relative shrink-0">
                        <div class="h-16 w-full"
                             style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 60%, #4f46e5 100%)"></div>
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2">
                            @if($artista->imagen_perfil)
                                <img src="{{ asset('storage/' . $artista->imagen_perfil) }}"
                                     alt="{{ $artista->nombre }}"
                                     class="w-25 h-25 rounded-full object-cover border-[3px] border-white shadow-md">
                            @else
                                <div class="w-25 h-25 rounded-full bg-violet-100 border-[3px] border-white shadow-md flex items-center justify-center text-3xl">
                                    {{ \App\Models\Artista::DISCIPLINAS[$artista->disciplina]['emoji'] ?? '🎨' }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="pt-16 pb-5 px-4 text-center flex-1 flex flex-col gap-1">
                        <p class="font-bold text-gray-900 text-sm leading-snug line-clamp-2 group-hover:text-violet-700 transition">
                            {{ $artista->nombre }}
                        </p>
                        <p class="text-xs font-semibold text-violet-600">
                            {{ \App\Models\Artista::DISCIPLINAS[$artista->disciplina]['emoji'] ?? '' }}
                            {{ \App\Models\Artista::DISCIPLINAS[$artista->disciplina]['label'] ?? $artista->disciplina }}
                        </p>
                        @if($artista->ciudad)
                            <p class="text-xs text-gray-400 truncate">{{ $artista->ciudad }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <p class="text-xs text-gray-400 mt-6 text-center">
            {{ $artistas->count() }} {{ $artistas->count() === 1 ? 'artista' : 'artistas' }}
            @if($disciplina)
                en {{ \App\Models\Artista::DISCIPLINAS[$disciplina]['label'] ?? $disciplina }}
            @endif
        </p>
    @endif

    {{-- CTA para artistas --}}
    <div class="mt-8 bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl p-6 text-white text-center">
        <p class="font-bold text-lg mb-1">¿Eres artista en Valparaíso?</p>
        <p class="text-sm text-white/80 mb-4">Crea tu perfil gratuito y que te encuentren para contratarte o colaborar.</p>
        <a href="{{ route('artista.register') }}"
           class="inline-block bg-white text-violet-700 font-bold text-sm px-5 py-2 rounded-xl hover:bg-violet-50 transition">
            Crear mi perfil →
        </a>
    </div>

</div>
@endsection
