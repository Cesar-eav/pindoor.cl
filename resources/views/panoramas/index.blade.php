@php use Illuminate\Support\Str; @endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panoramas — Pindoor.cl</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 font-serif text-base">
<div class="w-full mx-auto md:p-4">

    <x-navbar_labrujula />

    {{-- Hero --}}
    <div class="max-w-7xl mx-auto px-4">
        <section class="my-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-3">
                Panoramas en <span class="text-[#fc5648]">Valparaíso</span>
            </h1>
            <p class="text-lg text-gray-600 max-w-xl mx-auto">
                Lugares, experiencias y rincones únicos para disfrutar en la ciudad puerto.
            </p>
        </section>

        {{-- Filtro por categoría --}}
        <div class="bg-white rounded-xl shadow p-4 mb-8 border-t-4 border-[#fc5648]">
            <form action="{{ route('atractivos.panoramas') }}" method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Categoría</label>
                    <select name="categoria" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#fc5648] outline-none bg-gray-50">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->slug }}" @selected(request('categoria') == $cat->slug)>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Buscar</label>
                    <div class="flex">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Nombre, sector…"
                               class="w-full px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-[#fc5648] outline-none">
                        <button type="submit" class="bg-[#fc5648] text-white px-4 py-2 rounded-r-lg hover:bg-[#d94439] transition">
                            🔍
                        </button>
                    </div>
                </div>
                @if(request()->anyFilled(['categoria','search']))
                    <a href="{{ route('atractivos.panoramas') }}"
                       class="text-sm text-gray-500 hover:text-[#fc5648] underline self-end pb-2">
                        ✕ Limpiar
                    </a>
                @endif
            </form>
        </div>

        {{-- Grid de panoramas --}}
        @if($panoramas->count())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($panoramas as $punto)
                    <article class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col h-full">
                        <div class="relative">
                            <a href="{{ route('atractivos.show', $punto->slug ?? $punto->id) }}" class="block overflow-hidden">
                                @if($punto->imagenPrincipal)
                                    <img src="{{ asset('storage/' . $punto->imagenPrincipal->ruta) }}"
                                         alt="{{ $punto->title }}"
                                         class="w-full h-52 object-cover transform hover:scale-105 transition-transform duration-500" />
                                @else
                                    <div class="w-full h-52 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-5xl">🏙️</div>
                                @endif
                            </a>
                            @if($punto->categoria)
                                <span class="absolute top-3 left-3 bg-[#fc5648] text-white text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full shadow">
                                    {{ $punto->categoria->nombre }}
                                </span>
                            @endif
                        </div>
                        <div class="p-5 flex-grow flex flex-col">
                            <h3 class="text-lg font-bold text-gray-900 mb-1 leading-tight">
                                <a href="{{ route('atractivos.show', $punto->slug ?? $punto->id) }}"
                                   class="hover:text-[#fc5648] transition">
                                    {{ $punto->title }}
                                </a>
                            </h3>
                            @if($punto->sector)
                                <p class="text-xs text-gray-400 mb-2">📍 {{ $punto->sector }}</p>
                            @endif
                            <p class="text-gray-600 text-sm leading-relaxed flex-grow">
                                {{ Str::limit(strip_tags($punto->description), 120) }}
                            </p>
                            <a href="{{ route('atractivos.show', $punto->slug ?? $punto->id) }}"
                               class="mt-4 inline-block text-sm font-bold text-[#fc5648] hover:underline">
                                Ver más →
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="mt-12 mb-8 flex justify-center">
                {{ $panoramas->links() }}
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-sm p-20 text-center border-2 border-dashed border-gray-200">
                <div class="text-6xl mb-4">🕵️‍♂️</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Sin resultados</h3>
                <p class="text-gray-500 mb-6">No encontramos panoramas que coincidan con tu búsqueda.</p>
                <a href="{{ route('atractivos.panoramas') }}"
                   class="bg-[#fc5648] text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-900 transition">
                    Ver todos los panoramas
                </a>
            </div>
        @endif
    </div>
</div>
</body>
</html>
