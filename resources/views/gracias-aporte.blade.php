<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Gracias por tu aporte! - El Pionero de Valparaíso</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Confetti effect -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
</head>
<body class="bg-gradient-to-br from-green-50 via-blue-50 to-purple-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl w-full">
            <!-- Card principal -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <!-- Header con gradiente -->
                <div class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-600 py-12 px-8 text-center">
                    <!-- Ícono de éxito -->
                    <div class="mb-6 flex justify-center">
                        <div class="bg-white rounded-full p-6 shadow-xl animate-bounce">
                            <svg class="w-20 h-20 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                        ¡Muchas Gracias!
                    </h1>
                    <p class="text-xl md:text-2xl text-white font-medium">
                        Tu aporte ha sido recibido exitosamente
                    </p>
                </div>


                    <!-- Mensaje principal -->
                <div class="p-8 md:p-12">
                    <div class="text-center mb-10">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                            <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                            ¡Estamos trabajando para levantar Valparaíso!
                        </h2>
                        <p class="text-lg text-gray-600 max-w-2xl mx-auto leading-relaxed">
                            Tu generosidad es un pilar fundamental que nos permite seguir informando con la independencia y el rigor que nuestra ciudad necesita.
                            Gracias a personas como tú, **El Pionero de Valparaíso** puede honrar su compromiso de decir las cosas como son.
                            <br><br>
                            Porque creemos en Valparaíso y en su potencial, seguimos adelante con nuestra misión:
                            <span class="block text-2xl font-extrabold text-blue-600 mt-2">
                                MENOS POSTALES, MÁS REALIDAD.
                            </span>
                        </p>
                    </div>

                    <!-- Stats/Impacto -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                        <div class="text-center p-6 bg-blue-50 rounded-xl">
                            <div class="text-3xl font-bold text-blue-600 mb-2">100%</div>
                            <div class="text-sm text-gray-600">Independiente</div>
                        </div>
                        <div class="text-center p-6 bg-green-50 rounded-xl">
                            <div class="text-3xl font-bold text-green-600 mb-2">+7</div>
                            <div class="text-sm text-gray-600">Pasíses de alcance</div>
                        </div>
                        <div class="text-center p-6 bg-purple-50 rounded-xl">
                            <div class="text-3xl font-bold text-purple-600 mb-2">Porteño</div>
                            <div class="text-sm text-gray-600">Comprometido con Valpo</div>
                        </div>
                    </div>

                    <!-- Qué haremos con tu aporte -->
                    <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded-2xl p-8 mb-8 border-2 border-yellow-200">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Tu aporte nos ayudará a:
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Mantener nuestra línea editorial independiente y sin censura</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Producir contenido de calidad sobre Valparaíso y su gente</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Mantener nuestra plataforma digital accesible y gratuita</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-700">Seguir siendo un espacio de reflexión y debate ciudadano</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Compartir en redes -->
                    <div class="text-center mb-8">
                        <p class="text-gray-700 mb-4 font-medium">¿Quieres ayudarnos aún más?</p>
                        <p class="text-gray-600 mb-6">Comparte El Pionero con tus amigos y familiares</p>

                        <!-- Botón WhatsApp destacado -->
                        <div class="mb-6">
                            <a href="https://wa.me/?text={{ urlencode('Acabo de apoyar a El Pionero de Valparaíso, un medio independiente comprometido con nuestra ciudad. ¡Únete y apoya el periodismo local! ' . route('aportes')) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-3 px-10 py-5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold text-lg rounded-2xl shadow-2xl hover:shadow-3xl transform hover:scale-110 transition-all duration-300 animate-pulse hover:animate-none border-4 border-green-300">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                </svg>
                                <span class="flex flex-col items-start">
                                    <span class="text-sm font-normal opacity-90">Compartir por</span>
                                    <span>WhatsApp</span>
                                </span>
                            </a>
                        </div>

                        <!-- Otros botones de redes sociales -->
                        <p class="text-sm text-gray-500 mb-4">También puedes compartir en:</p>
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('inicio')) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition transform hover:scale-105">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode('Apoya el periodismo independiente de El Pionero de Valparaíso') }}&url={{ urlencode(route('inicio')) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 px-5 py-2.5 bg-black hover:bg-gray-800 text-white font-medium rounded-lg transition transform hover:scale-105">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                                Twitter/X
                            </a>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('inicio') }}"
                           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Volver al Inicio
                        </a>
                        <a href="{{ route('inicio.columnas') }}"
                           class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white hover:bg-gray-50 text-gray-700 font-bold rounded-xl border-2 border-gray-300 hover:border-gray-400 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Leer Columnas
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-6 text-center border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        Si tienes alguna pregunta sobre tu aporte, puedes contactarnos en
                        <a href="{{ route('contacto.formulario') }}" class="text-blue-600 hover:text-blue-800 font-medium underline">nuestro formulario de contacto</a>
                    </p>
                </div>
            </div>

            <!-- Mensaje adicional -->
            <div class="text-center mt-8">
                <p class="text-gray-600 italic">
                    "El periodismo independiente es fundamental para una democracia saludable"
                </p>
            </div>
        </div>
    </div>

    <!-- Script para confetti -->
    <script>
        // Lanzar confetti cuando carga la página
        window.addEventListener('load', function() {
            // Confetti desde la izquierda
            confetti({
                particleCount: 100,
                spread: 70,
                origin: { y: 0.6, x: 0 }
            });

            // Confetti desde la derecha
            setTimeout(() => {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6, x: 1 }
                });
            }, 250);

            // Confetti desde el centro
            setTimeout(() => {
                confetti({
                    particleCount: 150,
                    spread: 90,
                    origin: { y: 0.6, x: 0.5 }
                });
            }, 500);
        });
    </script>

    <!-- Animaciones CSS adicionales -->
    <style>
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.7;
            }
        }

        .delay-75 {
            animation-delay: 0.75s;
        }

        .delay-150 {
            animation-delay: 1.5s;
        }
    </style>
</body>
</html>
