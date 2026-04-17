@php
    use Illuminate\Support\Str;
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>La Brújula - Atractivos | El Pionero de Valparaíso</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 text-gray-900 font-serif text-base">
    <div class="w-full mx-auto md:p-4">
        <x-header />
        <x-navbar />

        <div class="max-w-7xl mx-auto px-4">
            <section class="my-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-3">
                    🧭 La Brújula
                </h1>
                <p class="text-xl text-gray-700">
                    Descubre los mejores rincones y experiencias de Valparaíso
                </p>
            </section>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-8 border-t-4 border-[#fc5648]">
                <form id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Categoría</label>
                            <select id="categoryFilter" name="category" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#fc5648] outline-none bg-gray-50">
                                <option value="">Todas las categorías</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat }}" @selected(request('category') == $cat)>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Búsqueda rápida</label>
                            <div class="flex">
                                <input type="text" id="searchFilter" name="search" value="{{ request('search') }}" 
                                       placeholder="¿Qué estás buscando?" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-[#fc5648] outline-none">
                                <button type="button" id="searchBtn" class="bg-[#fc5648] text-white px-5 py-2 rounded-r-lg hover:bg-[#d94439] transition">
                                    🔍
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-4">
                            @if(request()->anyFilled(['category', 'search']))
                                <button type="button" id="clearFiltersBtn"
                                   class="text-sm font-semibold text-gray-500 hover:text-[#fc5648] transition flex items-center gap-1 underline">
                                    <span>✕</span> Borrar filtros
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <div class="atractivos-container">
                @if ($atractivos->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach ($atractivos as $atractivo)
                            <article class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 flex flex-col h-full">
                                <div class="relative">
                                    <a href="{{ route('atractivos.show', $atractivo->id) }}" class="block overflow-hidden">
                                        @if ($atractivo->image)
                                            <img src="{{ asset('storage/' . $atractivo->image) }}"
                                                 alt="{{ $atractivo->title }}"
                                                 class="w-full h-56 object-cover transform hover:scale-105 transition-transform duration-500" />
                                        @else
                                            <div class="w-full h-56 bg-gray-200 flex items-center justify-center text-4xl">📍</div>
                                        @endif
                                    </a>
                                    <span class="absolute top-4 left-4 bg-[#fc5648] text-white text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full shadow-lg">
                                        {{ $atractivo->category }}
                                    </span>
                                </div>

                                <div class="p-5 flex-grow">
                                    <h3 class="text-xl font-bold text-gray-900 mb-1 leading-tight min-h-[3rem]">

                                <a href="https://maps.google.com/?q={{ $atractivo->lat }},{{ $atractivo->lng }}"
                                   target="_blank"
                                   rel="noopener"
                                   >
                                    📍
                                </a>
                                        <a href="{{ route('atractivos.show', $atractivo->id) }}" class="hover:text-[#fc5648] transition">
                                            {{ $atractivo->title }}
                                        </a>
                                    </h3>

                                    <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                                        {{ Str::limit(strip_tags($atractivo->description), 160) }}
                                    </p>

                                </div>

                            </article>
                        @endforeach
                    </div>

                    <div class="mt-16 mb-12 flex justify-center">
                        {{ $atractivos->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm p-20 text-center border-2 border-dashed border-gray-200">
                        <div class="text-6xl mb-6">🕵️‍♂️</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Sin resultados</h3>
                        <p class="text-gray-500 mb-6">No encontramos lugares que coincidan con "{{ request('search') }}".</p>
                        <a href="{{ route('atractivos.index') }}" class="bg-[#fc5648] text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-900 transition">
                            Ver toda La Brújula
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <x-footer />
    </div>

    <script>
        // Función para cargar atractivos con Axios
        async function loadAtractivos(params = {}) {
            try {
                const response = await window.axios.get('{{ route('atractivos.index') }}', {
                    params: params,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                // Reemplazar el contenedor de atractivos
                document.querySelector('.atractivos-container').innerHTML = response.data;
            } catch (error) {
                console.error('Error cargando atractivos:', error);
            }
        }

        // Listener para cambios en el filtro de categoría
        document.getElementById('categoryFilter').addEventListener('change', function() {
            const params = {
                category: this.value,
                search: document.getElementById('searchFilter').value
            };
            loadAtractivos(params);
        });

        // Listener para el botón de búsqueda
        document.getElementById('searchBtn').addEventListener('click', function() {
            const params = {
                category: document.getElementById('categoryFilter').value,
                search: document.getElementById('searchFilter').value
            };
            loadAtractivos(params);
        });

        // Listener para Enter en el campo de búsqueda
        document.getElementById('searchFilter').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const params = {
                    category: document.getElementById('categoryFilter').value,
                    search: this.value
                };
                loadAtractivos(params);
            }
        });

        // Listener para borrar filtros
        const clearBtn = document.getElementById('clearFiltersBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                document.getElementById('categoryFilter').value = '';
                document.getElementById('searchFilter').value = '';
                loadAtractivos({});
            });
        }
    </script>
</body>
</html>