<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apóyanos - El Pionero de Valparaíso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <a href="{{ route('inicio') }}" class="inline-block mb-6 text-blue-600 hover:text-blue-800 transition">
                    <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al inicio
                </a>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Apoya a El Pionero de Valparaíso</h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Tu aporte nos ayuda a seguir informando con independencia y calidad. Cada contribución cuenta.
                </p>
            </div>

            <!-- Mensaje de agradecimiento -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">¿Por qué tu aporte es importante?</h3>
                        <p class="text-blue-800">
                            El Pionero de Valparaíso es un medio independiente que depende del apoyo de sus lectores para mantener su línea editorial libre y comprometida con la verdad. Tu aporte nos permite seguir investigando, informando y siendo la voz de nuestra comunidad.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Opciones de aporte -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 py-6 px-8">
                    <h2 class="text-2xl font-bold text-white text-center">Elige el monto de tu aporte</h2>
                </div>

                <div class="p-8">
                    <!-- Montos predefinidos -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Aporte $2.000 -->
                        <a
                            href="https://www.flow.cl/btn.php?token=w35fce8fc4e90c00f82519856f6101d038d07efa"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group relative bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 border-2 border-blue-300 hover:border-blue-500 rounded-xl p-6 transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                        >
                            <div class="text-center">
                                <div class="bg-blue-500 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-600 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-3xl font-bold text-blue-900 mb-2">$2.000</p>
                                <p class="text-sm text-gray-600">Aporte básico</p>
                            </div>
                        </a>

                        <!-- Aporte $5.000 -->
                        <a
                            href="https://www.flow.cl/btn.php?token=d678ef5a7acf6c925130f845b2dcd6bc6b9fad35"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group relative bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 border-2 border-green-300 hover:border-green-500 rounded-xl p-6 transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                        >
                            <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">
                                Popular
                            </div>
                            <div class="text-center">
                                <div class="bg-green-500 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-green-600 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-3xl font-bold text-green-900 mb-2">$5.000</p>
                                <p class="text-sm text-gray-600">Aporte solidario</p>
                            </div>
                        </a>

                        <!-- Aporte $10.000 -->
                        <a
                            href="https://www.flow.cl/btn.php?token=pab362f53b2b92cd16d6da1fcc989f6e4f2827bc"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="group relative bg-gradient-to-br from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 border-2 border-purple-300 hover:border-purple-500 rounded-xl p-6 transition-all duration-300 transform hover:scale-105 hover:shadow-lg"
                        >
                            <div class="text-center">
                                <div class="bg-purple-500 text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 group-hover:bg-purple-600 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                    </svg>
                                </div>
                                <p class="text-3xl font-bold text-purple-900 mb-2">$10.000</p>
                                <p class="text-sm text-gray-600">Aporte generoso</p>
                            </div>
                        </a>
                    </div>

                    <!-- Separador -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500 font-medium">o</span>
                        </div>
                    </div>

                    <!-- Aporte libre -->
                    <div class="bg-gradient-to-br from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-xl p-8">
                        <div class="text-center">
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-3">Aporte Libre</h3>
                            <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                ¿Deseas aportar un monto diferente? Haz clic aquí para ingresar el monto que prefieras.
                            </p>
                            <a
                                href="https://www.flow.cl/btn.php?token=ff8eb580410e3b84c59fc5cbf3e4b0e6cfad11ce"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-block bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200"
                            >
                                Hacer Aporte Personalizado
                            </a>
                        </div>
                    </div>

                    <!-- Información adicional -->
                    <div class="mt-8 text-center text-sm text-gray-500">
                        <p class="flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Transacciones seguras procesadas por Flow
                        </p>
                    </div>
                </div>
            </div>

            <!-- Agradecimiento final -->
            <div class="mt-12 text-center">
                <p class="text-lg text-gray-700 font-medium mb-4">¡Gracias por tu apoyo!</p>
                <p class="text-gray-600">
                    Cada aporte, sin importar su tamaño, nos ayuda a seguir adelante con nuestra misión de informar con calidad y transparencia.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
