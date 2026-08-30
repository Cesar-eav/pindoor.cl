<x-mail::message>
# ¡Reserva confirmada!

Hola {{ $reserva->nombre_cliente }}, tu pago fue procesado correctamente.

<x-mail::table>
| Campo | Valor |
|:------|:------|
| Código de reserva | **{{ $reserva->codigo_reserva }}** |
| Ruta | {{ $reserva->rutaOperador->ruta->titulo }} |
| Fecha | {{ $reserva->fecha_visita->translatedFormat('d \d\e F Y') }} |
| Hora | {{ substr($reserva->horario->hora, 0, 5) }} |
| Adultos | {{ $reserva->cantidad_adultos }} |
@if($reserva->cantidad_ninos > 0)
| Niños | {{ $reserva->cantidad_ninos }} |
@endif
| Total pagado | ${{ number_format($reserva->precio_total, 0, ',', '.') }} |
</x-mail::table>

Presenta este código QR (o el código {{ $reserva->codigo_reserva }}) en el punto de encuentro para registrar tu ingreso.

<div style="text-align:center; margin: 24px 0;">
<img src="{{ $message->embedData($qrPng, 'ticket-qr.png', 'image/png') }}" width="180" height="180" alt="QR de check-in de la reserva {{ $reserva->codigo_reserva }}">
</div>

— Pindoor.cl
</x-mail::message>
