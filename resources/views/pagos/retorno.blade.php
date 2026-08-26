@extends('layouts.pindoor')

@section('title', 'Tu reserva · Pindoor')
@section('bodyClass', 'bg-[#f9fafb] text-gray-900')

@section('content')
<div class="max-w-lg mx-auto px-4 py-16 text-center">

    @if($reserva->estado === 'pagada')
        <div class="text-6xl mb-4">🎉</div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">¡Reserva confirmada!</h1>
        <p class="text-gray-500 mb-8">Te enviamos un email con los detalles a {{ $reserva->email_cliente }}.</p>
    @elseif($reserva->estado === 'pendiente')
        <div class="text-6xl mb-4">⏳</div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">Estamos confirmando tu pago</h1>
        <p class="text-gray-500 mb-8">Puede tardar unos minutos. Te avisaremos por email cuando esté listo.</p>
    @else
        <div class="text-6xl mb-4">😕</div>
        <h1 class="text-2xl font-extrabold text-gray-900 mb-2">El pago no se completó</h1>
        <p class="text-gray-500 mb-8">Estado: {{ $reserva->estado }}. Si crees que es un error, contáctanos con tu código de reserva.</p>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 text-left">
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-400">Código de reserva</dt>
                <dd class="font-bold text-gray-900">{{ $reserva->codigo_reserva }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-400">Ruta</dt>
                <dd class="font-bold text-gray-900">{{ $reserva->rutaOperador->ruta->titulo }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-400">Fecha</dt>
                <dd class="font-bold text-gray-900">{{ $reserva->fecha_visita->format('d-m-Y') }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-400">Hora</dt>
                <dd class="font-bold text-gray-900">{{ substr($reserva->horario->hora, 0, 5) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-400">Personas</dt>
                <dd class="font-bold text-gray-900">
                    {{ $reserva->cantidad_adultos }} adulto{{ $reserva->cantidad_adultos === 1 ? '' : 's' }}
                    @if($reserva->cantidad_ninos > 0)
                        · {{ $reserva->cantidad_ninos }} niño{{ $reserva->cantidad_ninos === 1 ? '' : 's' }}
                    @endif
                </dd>
            </div>
            <div class="flex justify-between pt-3 border-t border-gray-100">
                <dt class="text-gray-400">Total</dt>
                <dd class="font-extrabold text-[#fc5648]">${{ number_format($reserva->precio_total, 0, ',', '.') }}</dd>
            </div>
        </dl>
    </div>

    <a href="{{ route('rutas.show', $reserva->rutaOperador->ruta->slug) }}"
       class="inline-block mt-8 text-sm font-bold text-[#fc5648] hover:underline">
        ← Volver a la ruta
    </a>
</div>
@endsection
