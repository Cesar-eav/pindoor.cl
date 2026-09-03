<?php

namespace App\Notifications;

use App\Models\EventoEntrada;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EntradaIniciada extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public EventoEntrada $entrada) {}

    public function via(): array
    {
        return [\App\Notifications\Channels\TelegramChannel::class];
    }

    public function toTelegram(): string
    {
        $e = $this->entrada;

        $prefijo = $e->es_prueba ? "🧪 <b>[PRUEBA]</b>\n" : '';

        return $prefijo . "⚠️ <b>Intento de compra de entrada — pendiente de pago</b>\n"
            . "<i>Aún no confirma que el pago se haya completado.</i>\n\n"
            . "Código: {$e->codigo_entrada}\n"
            . "Evento: {$e->evento_titulo_al_pagar}\n"
            . "Cliente: {$e->nombre_cliente}\n"
            . "Tel: {$e->telefono_cliente}\n"
            . "Email: {$e->email_cliente}\n"
            . "Fecha: {$e->evento_fecha_al_pagar->format('d/m/Y')}\n"
            . "Entradas: {$e->cantidad_entradas}\n"
            . 'Total: $' . number_format($e->monto_total, 0, ',', '.');
    }
}
