<x-mail::message>
# ¡Entrada confirmada!

Hola {{ $entrada->nombre_cliente }}, tu pago fue procesado correctamente.

<x-mail::table>
| Campo | Valor |
|:------|:------|
| Código de entrada | **{{ $entrada->codigo_entrada }}** |
| Evento | {{ $entrada->evento_titulo_al_pagar }} |
| Lugar | {{ $entrada->punto?->title }} |
| Fecha | {{ $entrada->evento_fecha_al_pagar->translatedFormat('d \d\e F Y') }} |
| Entradas | {{ $entrada->cantidad_entradas }} |
| Total pagado | ${{ number_format($entrada->monto_total, 0, ',', '.') }} |
</x-mail::table>

Presenta este correo o el código {{ $entrada->codigo_entrada }} en el ingreso al evento.

— Pindoor.cl
</x-mail::message>
