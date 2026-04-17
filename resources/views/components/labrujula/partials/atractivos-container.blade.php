@php
    use Illuminate\Support\Str;
@endphp

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
                    <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight min-h-[3rem]">
                        <a href="{{ route('atractivos.show', $atractivo->id) }}" class="hover:text-[#fc5648] transition">
                            {{ $atractivo->title }}
                        </a>
                    </h3>

                    <p class="text-gray-600 text-sm mb-4 leading-relaxed">
                        {{ Str::limit(strip_tags($atractivo->description), 160) }}
                    </p>

                    @if ($atractivo->processed_tags)
                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach (array_slice($atractivo->processed_tags, 0, 4) as $tag)
                                <span class="text-[10px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded italic">
                                    #{{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="px-5 pb-5">
                    <a href="{{ route('atractivos.show', $atractivo->id) }}"
                       class="block w-full text-center bg-gray-900 text-white py-2.5 rounded-lg font-bold text-sm hover:bg-[#fc5648] transition-colors duration-300">
                        Explorar lugar
                    </a>
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
