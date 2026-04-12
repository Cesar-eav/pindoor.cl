<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panoramas — Pindoor.cl</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-serif">

<x-navbar_labrujula />

<div class="min-h-[70vh] flex flex-col items-center justify-center px-4 text-center py-24">

    {{-- Ícono animado --}}
    <div class="relative mb-8">
        <div class="w-28 h-28 rounded-full bg-[#fc5648]/10 flex items-center justify-center mx-auto">
            <svg class="w-14 h-14 text-[#fc5648]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1 1 .03 2.798-1.344 2.798H4.142c-1.373 0-2.344-1.798-1.344-2.798L4 15.3" />
            </svg>
        </div>
        {{-- Punto decorativo --}}
        <span class="absolute top-1 right-1 w-4 h-4 bg-[#fc5648] rounded-full animate-ping opacity-60"></span>
        <span class="absolute top-1 right-1 w-4 h-4 bg-[#fc5648] rounded-full"></span>
    </div>

    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
        Panoramas
    </h1>

    <p class="text-lg text-gray-500 max-w-md mx-auto mb-3">
        Estamos preparando algo genial para ti.
    </p>
    <p class="text-sm text-gray-400 max-w-sm mx-auto mb-10">
        Pronto encontrarás aquí una guía curada de los mejores panoramas, actividades
        y experiencias que tiene para ofrecer Valparaíso.
    </p>

    {{-- Barra de progreso decorativa --}}
    <div class="w-64 bg-gray-200 rounded-full h-1.5 mb-10 overflow-hidden">
        <div class="bg-[#fc5648] h-1.5 rounded-full w-1/3 animate-pulse"></div>
    </div>

    <a href="{{ url('labrujula') }}"
       class="inline-flex items-center gap-2 bg-[#fc5648] text-white font-bold px-6 py-3 rounded-2xl shadow hover:bg-[#d94439] transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Volver al inicio
    </a>
</div>

</body>
</html>
