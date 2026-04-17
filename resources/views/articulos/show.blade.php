<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalles del Artículo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-3xl font-bold mb-4">{{ $articulo->titulo }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mb-2">{{ __('Publicado en') }}: <a href="{{ route('revistas.show', $articulo->revista) }}" class="underline">{{ $articulo->revista->titulo }}</a></p>
                    @if ($articulo->columnista)
                        <div class="flex items-center mb-4">
                            @if ($articulo->columnista->foto)
                                <img src="{{ asset('storage/' . $articulo->columnista->foto) }}" alt="{{ $articulo->columnista->nombre }}" class="rounded-full h-10 w-10 mr-2 object-cover">
                            @endif
                            <p class="text-lg font-semibold">{{ $articulo->columnista->nombre }}</p>
                        </div>
                    @endif
                    <div class="mb-6 prose dark:prose-invert max-w-none">
                        {!! $articulo->contenido !!} {{-- Renderizar HTML del editor --}}
                    </div>

                    <div class="mb-4">
                        <a href="{{ route('articulos.edit', $articulo) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('Editar Artículo') }}</a>
                        <form action="{{ route('articulos.destroy', $articulo) }}" method="POST" class="inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('{{ __('¿Estás seguro de eliminar este artículo?') }}')">{{ __('Eliminar Artículo') }}</button>
                        </form>
                    </div>

                    <a href="{{ route('articulos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">{{ __('Volver a la Lista de Artículos') }}</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>