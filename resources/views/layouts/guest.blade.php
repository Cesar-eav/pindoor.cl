<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Pindoor') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen flex relative overflow-x-hidden">

        {{-- Panel izquierdo (solo desktop) --}}
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12 relative overflow-hidden"
             style="background: linear-gradient(155deg, #ff6b5b 0%, #fc5648 45%, #e83a2c 100%)">

            {{-- Manchas decorativas sutiles --}}
            <div class="absolute -top-24 -left-24 w-72 h-72 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-0 left-1/4 w-48 h-48 bg-black/5 rounded-full"></div>

            {{-- Logo --}}
            <div class="relative z-10">
                <a href="/" class="inline-flex items-center">
                    <span class="text-white font-bold text-3xl tracking-tight">Pin</span><span class="text-white/65 font-bold text-3xl tracking-tight">door</span>
                </a>
            </div>

            {{-- Texto --}}
            <div class="relative z-10">
                <h1 class="text-white text-4xl font-bold leading-tight mb-4">
                    Tu negocio,<br>visible en Valparaíso
                </h1>
                <p class="text-white/80 text-base leading-relaxed mb-8">
                    Carta, menú del día, ofertas y agenda.<br>Todo en un solo lugar.
                </p>
                <ul class="space-y-3">
                    <li class="flex items-center gap-3 text-white/80 text-sm">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white text-xs shrink-0">✓</span>
                        Perfil gratuito para tu establecimiento
                    </li>
                    <li class="flex items-center gap-3 text-white/80 text-sm">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white text-xs shrink-0">✓</span>
                        Publica ofertas, menú del día y agenda
                    </li>
                    <li class="flex items-center gap-3 text-white/80 text-sm">
                        <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white text-xs shrink-0">✓</span>
                        Comunicación directa con tu público
                    </li>
                </ul>
            </div>

            <p class="relative z-10 text-white/35 text-sm">© {{ date('Y') }} Pindoor · Valparaíso, Chile</p>
        </div>

        {{-- Círculos en arco vertical sobre el límite (solo desktop) --}}
        @if($featuredPuntos->count())
        @php
            // Arco que baja hacia el centro y sube en los extremos (bowing hacia el formulario)
            $offsets = [-10, 4, 16, 16, 4, -10]; // px hacia la derecha
        @endphp
        <div class="hidden lg:flex absolute top-0 bottom-0 flex-col justify-around items-center py-16 z-30"
             style="left: calc(50% - 2.75rem)">
            @foreach($featuredPuntos->take(6) as $i => $punto)
            <div class="w-[5.5rem] h-[5.5rem] rounded-full overflow-hidden border-[3px] border-white shadow-xl shrink-0"
                 style="transform: translateX({{ $offsets[$i] }}px)">
                <img src="{{ asset('storage/' . $punto->imagenPrincipal->ruta) }}"
                     alt="{{ $punto->title }}"
                     class="w-full h-full object-cover">
            </div>
            @endforeach
        </div>
        @endif

        {{-- Panel derecho: formulario --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-8 bg-gray-50">

            <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 px-8 py-8">
                {{ $slot }}
            </div>

            <p class="mt-4 text-xs text-gray-400">
                © {{ date('Y') }} Pindoor · <a href="/" class="hover:text-gray-600 transition">Volver al inicio</a>
            </p>
        </div>

    </body>
</html>
