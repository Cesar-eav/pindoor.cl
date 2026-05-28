<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Experiencias</h2>
            <a href="{{ route('admin.experiencias.create') }}"
               class="bg-[#fc5648] text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-[#d94439] transition">
                + Nueva experiencia
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Búsqueda y filtros --}}
            <form method="GET" action="{{ route('admin.experiencias.index') }}"
                  class="bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 space-y-3">
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="search" value="{{ $search }}"
                               placeholder="Buscar por título, proveedor o ubicación…"
                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#fc5648] outline-none">
                    </div>
                    @if($search || $categoria)
                    <a href="{{ route('admin.experiencias.index') }}"
                       class="flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 font-semibold px-3 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Limpiar
                    </a>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" name="categoria" value=""
                            class="px-3 py-1 rounded-full text-xs font-bold border transition
                                   {{ !$categoria ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                        Todas
                    </button>
                    @foreach($categorias as $slug => $cat)
                    <button type="submit" name="categoria" value="{{ $slug }}"
                            class="px-3 py-1 rounded-full text-xs font-bold border transition
                                   {{ $categoria === $slug ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:border-gray-400' }}">
                        {{ $cat['emoji'] }} {{ $cat['label'] }}
                    </button>
                    @endforeach
                    <button type="submit" name="categoria" value="gratuito"
                            class="px-3 py-1 rounded-full text-xs font-bold border transition
                                   {{ $categoria === 'gratuito' ? 'bg-green-700 text-white border-green-700' : 'bg-green-50 text-green-700 border-green-200 hover:border-green-400' }}">
                        🎟️ Gratis
                    </button>
                </div>
            </form>

            @if($experiencias->isEmpty())
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 px-5 py-16 text-center">
                    <div class="text-4xl mb-3">🎭</div>
                    @if($search || $categoria)
                        <p class="font-bold text-gray-700 mb-1">Sin resultados</p>
                        <a href="{{ route('admin.experiencias.index') }}"
                           class="text-[#fc5648] font-bold hover:underline text-sm">Ver todas</a>
                    @else
                        <p class="font-bold text-gray-700 mb-1">No hay experiencias aún</p>
                        <a href="{{ route('admin.experiencias.create') }}"
                           class="text-[#fc5648] font-bold hover:underline text-sm">Crea la primera</a>
                    @endif
                </div>
            @else
            <div class="space-y-2">
                @foreach($experiencias as $experiencia)
                @php
                    $cat = $experiencia->categoria
                        ? (\App\Models\Experiencia::CATEGORIAS[$experiencia->categoria] ?? null)
                        : null;
                @endphp

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm flex items-stretch overflow-hidden
                            {{ !$experiencia->activo ? 'opacity-60' : '' }}">

                    {{-- Imagen --}}
                    <div class="shrink-0 w-20 self-stretch">
                        @if($experiencia->imagen)
                            <img src="{{ asset('storage/' . $experiencia->imagen) }}"
                                 alt="{{ $experiencia->titulo }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center text-2xl">
                                {{ $cat ? $cat['emoji'] : '✨' }}
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 px-4 py-3 min-w-0">
                        <p class="font-semibold text-gray-800 leading-snug truncate">{{ $experiencia->titulo }}</p>
                        @if($experiencia->proveedor)
                        <p class="text-xs text-gray-500 mt-0.5">👤 {{ $experiencia->proveedor }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                            @if($cat)
                            <span class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                {{ $cat['emoji'] }} {{ $cat['label'] }}
                            </span>
                            @endif
                            @if($experiencia->es_gratuito)
                            <span class="text-[10px] font-bold bg-green-50 text-green-700 px-2 py-0.5 rounded-full">🎟️ Gratis</span>
                            @elseif($experiencia->precio)
                            <span class="text-[10px] font-bold bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                💰 ${{ number_format($experiencia->precio, 0, ',', '.') }}
                            </span>
                            @endif
                            @if(!empty($experiencia->dias_semana))
                            <span class="text-[10px] font-bold bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full">
                                🔁 {{ $experiencia->dias_semana_label }}
                            </span>
                            @endif
                            @if($experiencia->hora)
                            <span class="text-[10px] text-gray-500">🕐 {{ $experiencia->hora }}</span>
                            @endif
                            @if($experiencia->duracion)
                            <span class="text-[10px] text-gray-500">⏱ {{ $experiencia->duracion }}</span>
                            @endif
                            @if($experiencia->ubicacion)
                            <span class="text-[10px] text-gray-400 truncate max-w-[200px]">📍 {{ $experiencia->ubicacion }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="flex flex-col items-end justify-between px-4 py-3 shrink-0 gap-2">
                        <form action="{{ route('admin.experiencias.toggle', $experiencia) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-[11px] font-bold px-2.5 py-1 rounded-full transition
                                           {{ $experiencia->activo
                                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                                : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                                {{ $experiencia->activo ? '● Visible' : '○ Oculto' }}
                            </button>
                        </form>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.experiencias.edit', $experiencia) }}"
                               class="text-xs font-bold text-blue-600 hover:underline">Editar</a>
                            <form action="{{ route('admin.experiencias.destroy', $experiencia) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar «{{ addslashes($experiencia->titulo) }}»?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-xs font-bold text-red-400 hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </div>

                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
