<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Previsualización del PDF') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-auto">
                        {!! $pdfHtml !!}
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('revistas.generar-pdf', $revista) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">{{ __('Descargar PDF') }}</a>
                        <a href="{{ route('revistas.show', $revista) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded ml-2">{{ __('Volver a la Revista') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>