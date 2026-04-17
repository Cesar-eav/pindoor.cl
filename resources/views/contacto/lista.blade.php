<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Contactos - El Pionero de Valparaíso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (!file_exists(public_path('build/manifest.json')) && !file_exists(public_path('hot')))
        <style>
            /* Tu CSS de Tailwind compilado */
        </style>
    @endif
</head>
<body class="font-sans antialiased bg-gray-100">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-2xl font-semibold mb-6 text-gray-900">Mensajes de Contacto Recibidos</h3>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">¡Éxito!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($contactos->isEmpty())
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Información:</strong>
                            <span class="block sm:inline">Aún no hay mensajes de contacto.</span>
                        </div>
                    @else
                        <div class="hidden lg:block overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Fecha
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nombre Completo
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Correo
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Teléfono
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Motivo
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($contactos as $contacto)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $contacto->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $contacto->nombre }} {{ $contacto->apellido }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                                <a href="mailto:{{ $contacto->correo }}" class="hover:underline">{{ $contacto->correo }}</a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $contacto->telefono ?? 'N/A' }}
                                            </td>
                                            {{-- Motivo se muestra completo en escritorio --}}
                                            <td class="px-6 py-4 text-sm text-gray-500 break-words">
                                                {{ $contacto->motivo }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="lg:hidden space-y-6">
                            @foreach ($contactos as $contacto)
                                <div class="bg-white shadow-md rounded-lg p-5 border border-gray-200">
                                    <div class="text-sm text-gray-500 mb-2">
                                        <span class="font-semibold text-gray-700">Fecha:</span> {{ $contacto->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div class="mb-2">
                                        <span class="font-semibold text-gray-700">Nombre:</span> {{ $contacto->nombre }} {{ $contacto->apellido }}
                                    </div>
                                    <div class="mb-2">
                                        <span class="font-semibold text-gray-700">Correo:</span> <a href="mailto:{{ $contacto->correo }}" class="text-blue-600 hover:underline">{{ $contacto->correo }}</a>
                                    </div>
                                    <div class="mb-3">
                                        <span class="font-semibold text-gray-700">Teléfono:</span> {{ $contacto->telefono ?? 'N/A' }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-700 block mb-1">Motivo:</span>
                                        <p class="text-sm text-gray-800 leading-relaxed">{{ $contacto->motivo }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($contactos instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $contactos->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

</body>
</html>