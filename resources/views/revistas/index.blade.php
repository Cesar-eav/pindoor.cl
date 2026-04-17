<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800  leading-tight">
            {{ __('Revistas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('revistas.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Crear Nueva Revista') }}
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-200 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">{{ __('Éxito') }}!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <ul class="space-y-2">
                        @forelse ($revistas as $revista)
                            <li class="bg-gray-100  rounded-md p-4 flex justify-between items-center">
                                <a href="{{ route('revistas.show', $revista) }}" class="hover:underline">
                                    {{ $revista->titulo }} ({{ $revista->fecha_publicacion ? $revista->fecha_publicacion->format('Y-m-d') : 'Sin fecha' }})
                                </a>
                                <div>

                                    <a href="{{ route('previsualizar.pdf', $revista) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">{{ __('Previsualizar PDF') }}</a>

                                    <a href="{{ route('revistas.edit', $revista) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm">{{ __('Editar') }}</a>
                                    <form action="{{ route('revistas.destroy', $revista) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm ml-2" onclick="return confirm('{{ __('¿Estás seguro de eliminar esta revista?') }}')">{{ __('Eliminar') }}</button>
                                    </form>
                                    {{-- ELIMINAR ESTE ENLACE --}}
                                    {{-- <a href="{{ route('revistas.generar-pdf', $revista) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-1 px-2 rounded text-sm ml-2" target="_blank">{{ __('Generar PDF') }}</a> --}}
                                </div>
                            </li>
                        @empty
                            <li class="text-gray-500" >{{ __('No hay revistas creadas aún.') }}</li>
                        @endforelse
                    </ul>

                    <div class="mt-4">
                        {{ $revistas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>