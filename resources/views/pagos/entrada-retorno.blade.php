@extends('layouts.pindoor')

@section('title', 'Tu entrada · Pindoor')
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">

    @if($entrada->estado === 'pagada')
        <div class="text-6xl mb-4">🎉</div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">¡Entrada confirmada!</h1>
        <p class="text-gray-500 mb-8">Te enviamos un email con los detalles a {{ $entrada->email_cliente }}.</p>
    @elseif($entrada->estado === 'pendiente')
        <div class="text-6xl mb-4">⏳</div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Estamos confirmando tu pago</h1>
        <p class="text-gray-500 mb-8">Puede tardar unos minutos. Te avisaremos por email cuando esté listo.</p>
    @else
        <div class="text-6xl mb-4">😕</div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">El pago no se completó</h1>
        <p class="text-gray-500 mb-8">Estado: {{ $entrada->estado }}. Si crees que es un error, contáctanos con tu código de entrada.</p>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-left">
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-400">Código de entrada</dt>
                <dd class="font-bold text-gray-900">{{ $entrada->codigo_entrada }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-400">Evento</dt>
                <dd class="font-bold text-gray-900">{{ $entrada->evento_titulo_al_pagar }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-400">Fecha</dt>
                <dd class="font-bold text-gray-900">{{ $entrada->evento_fecha_al_pagar->format('d-m-Y') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-400">Entradas</dt>
                <dd class="font-bold text-gray-900">{{ $entrada->cantidad_entradas }}</dd>
            </div>
            <div class="flex justify-between pt-3 border-t border-gray-100">
                <dt class="text-gray-400">Total</dt>
                <dd class="font-extrabold text-[#fc5648]">${{ number_format($entrada->monto_total, 0, ',', '.') }}</dd>
            </div>
        </dl>
    </div>

    @if($entrada->punto)
    <a href="{{ route('puntos.show', $entrada->punto->slug) }}"
       class="inline-block mt-8 text-sm font-bold text-[#fc5648] hover:underline">
        ← Volver a {{ $entrada->punto->title }}
    </a>
    @endif
</div>
@endsection
