<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mi Negocio &mdash; {{ $punto->title }}
            </h2>
            <a href="{{ route('cliente.perfil.editar') }}"
               class="px-4 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-xl hover:opacity-90 transition">
                Editar Perfil
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded-xl px-5 py-3">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Cabecera del negocio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex gap-5 items-start">
                @if($punto->imagen_perfil)
                    <img src="{{ asset('storage/' . $punto->imagen_perfil) }}"
                         alt="Logo {{ $punto->title }}"
                         class="w-20 h-20 rounded-2xl object-cover border border-gray-100 shrink-0">
                @else
                    <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center shrink-0">
                        <span class="text-3xl">🏪</span>
                    </div>
                @endif

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xl font-bold text-gray-900">{{ $punto->title }}</h3>
                        <span class="text-xs px-2 py-0.5 rounded-full font-bold
                            {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                            {{ $punto->activo ? 'Visible en el mapa' : 'Pausado' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $punto->categoria?->icono }} {{ $punto->categoria?->nombre ?? '—' }}
                        &middot; 📍 {{ $punto->sector }}
                    </p>
                    @if($punto->horario)
                        <p class="text-sm text-gray-400 mt-1">🕐 {{ $punto->horario }}</p>
                    @endif
                    @if($punto->enlace)
                        <a href="{{ $punto->enlace }}" target="_blank"
                           class="text-xs text-pindoor-accent hover:underline mt-1 block truncate">
                            {{ $punto->enlace }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Oferta del día: actualización rápida --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h4 class="font-bold text-gray-700 mb-1">Oferta del día</h4>
                <p class="text-xs text-gray-400 mb-4">
                    Actualiza tu menú, promoción o novedad del día. Se mostrará en tu ficha pública.
                </p>

                <form method="POST" action="{{ route('cliente.oferta.actualizar') }}">
                    @csrf @method('PATCH')
                    <textarea
                        name="oferta_del_dia"
                        rows="4"
                        maxlength="1000"
                        placeholder="Ej: Hoy tenemos happy hour 18–20 h, capuchino + croissant $3.500..."
                        class="w-full border-gray-300 rounded-xl shadow-sm text-sm focus:ring-pindoor-accent resize-none"
                    >{{ old('oferta_del_dia', $punto->oferta_del_dia) }}</textarea>

                    <div class="flex justify-between items-center mt-3">
                        @if($punto->oferta_del_dia)
                            <span class="text-xs text-gray-400">
                                Última actualización: {{ $punto->updated_at->diffForHumans() }}
                            </span>
                        @else
                            <span class="text-xs text-gray-300 italic">Sin oferta activa</span>
                        @endif
                        <button type="submit"
                                class="px-5 py-2 bg-pindoor-accent text-white text-sm font-bold rounded-lg hover:opacity-90 transition">
                            Publicar
                        </button>
                    </div>
                </form>
            </div>

            {{-- Descripción del negocio --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-gray-700">Descripción del negocio</h4>
                    <a href="{{ route('cliente.perfil.editar') }}"
                       class="text-xs text-pindoor-accent hover:underline">Editar</a>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $punto->description ?: 'Sin descripción aún.' }}
                </p>

                @if($punto->tags && count($punto->tags))
                    <div class="flex flex-wrap gap-2 mt-4">
                        @foreach($punto->tags as $tag)
                            <span class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded-full">
                                {{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Estado del perfil de búsqueda --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-amber-800">Perfil de búsqueda</h4>
                    <a href="{{ route('cliente.perfil.editar') }}#busqueda"
                       class="text-xs text-amber-700 hover:underline font-medium">Completar</a>
                </div>
                <p class="text-xs text-amber-700 mb-3">
                    Este texto es invisible para los turistas, pero lo usa el buscador para encontrar tu negocio.
                    Cuanto más detallado, mejor te encontrarán.
                </p>
                @if($punto->descripcion_busqueda)
                    <p class="text-sm text-amber-900 leading-relaxed line-clamp-3">
                        {{ $punto->descripcion_busqueda }}
                    </p>
                @else
                    <p class="text-sm text-amber-600 italic">
                        Aún no has completado tu perfil de búsqueda.
                        <a href="{{ route('cliente.perfil.editar') }}#busqueda" class="font-bold hover:underline">
                            Hazlo ahora
                        </a>
                    </p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
