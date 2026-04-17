<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Artículos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('articulos.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Crear Nuevo Artículo') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-200 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">{{ __('Éxito') }}!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <ul class="space-y-2">
                        @forelse ($articulos as $articulo)
                            <li class="bg-gray-100 rounded-md p-4 flex justify-between items-center">
                                <div>
                                    <a href="{{ route('articulos.show', $articulo) }}" class="hover:underline">
                                        {{ $articulo->titulo }}
                                    </a>
                                    <p class="text-sm text-gray-500">{{ __('Autor') }}: {{ $articulo->columnista->nombre ?? 'Anónimo' }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('articulos.edit', $articulo) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">{{ __('Editar') }}</a>
                                    <form action="{{ route('articulos.destroy', $articulo) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm ml-2" onclick="return confirm('{{ __('¿Estás seguro de eliminar este artículo?') }}')">{{ __('Eliminar') }}</button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="text-gray-500">{{ __('No hay artículos creados aún.') }}</li>
                        @endforelse
                    </ul>

                    <div class="mt-4">
                        {{ $articulos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>