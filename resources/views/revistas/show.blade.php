<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de la Revista') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-4">{{ $revista->titulo }}</h1>
                    @if ($revista->fecha_publicacion)
                        <p class="mb-2">{{ __('Fecha de Publicación') }}: {{ $revista->fecha_publicacion->format('Y-m-d') }}</p>
                    @endif
                    @if ($revista->descripcion)
                        <p class="mb-4">{{ __('Descripción') }}: {{ $revista->descripcion }}</p>
                    @endif

                    <div class="mb-4">
                        <a href="{{ route('revistas.edit', $revista) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('Editar Revista') }}</a>
                        <form action="{{ route('revistas.destroy', $revista) }}" method="POST" class="inline ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('{{ __('¿Estás seguro de eliminar esta revista?') }}')">{{ __('Eliminar Revista') }}</button>
                        </form>
                        <a href="{{ route('revistas.generar-pdf', $revista) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded ml-2" target="_blank">{{ __('Generar PDF') }}</a>
                    </div>

                    <h3 class="text-xl font-semibold mb-2">{{ __('Artículos en esta Revista') }}</h3>
                    <ul class="space-y-2">
                        @forelse ($revista->articulos as $articulo)
                            <li class="bg-gray-100 rounded-md p-3">
                                <a href="{{ route('articulos.show', $articulo) }}" class="hover:underline">{{ $articulo->titulo }}</a>
                            </li>
                        @empty
                            <li class="text-gray-500">{{ __('No hay artículos en esta revista aún.') }}</li>
                        @endforelse
                    </ul>

                    <div class="mt-4">
                        <a href="{{ route('articulos.create') }}?revista_id={{ $revista->id }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">{{ __('Crear Nuevo Artículo') }}</a>
                        <a href="{{ route('revistas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">{{ __('Volver a la Lista de Revistas') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>