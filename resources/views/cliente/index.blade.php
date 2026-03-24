<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mis Puntos de Interés') }}
            </h2>
            <a href="{{ route('cliente.create') }}" class="bg-pindoor-accent hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-bold transition">
                + Nuevo Local
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-gray-100">
                @if($puntos->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-gray-500 mb-4 text-lg text-wrap">Aún no tienes locales registrados en los cerros.</p>
                        <a href="{{ route('cliente.create') }}" class="text-pindoor-accent font-bold hover:underline">¡Comienza registrando tu primer local aquí!</a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($puntos as $punto)
                            <div class="border border-gray-100 rounded-2xl overflow-hidden hover:shadow-md transition">
                                <div class="p-4">
                                    <span class="text-xs font-bold text-gray-400 uppercase">{{ $punto->category }}</span>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $punto->title }}</h3>
                                    <p class="text-sm text-gray-500 italic">📍 {{ $punto->sector }}</p>
                                    
                                    <div class="mt-4 flex gap-2">
                                        <a href="#" class="text-xs bg-gray-100 px-3 py-1 rounded-full font-medium">Editar</a>
                                        <span class="text-xs {{ $punto->activo ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }} px-3 py-1 rounded-full font-medium">
                                            {{ $punto->activo ? 'Público' : 'Pausado' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>