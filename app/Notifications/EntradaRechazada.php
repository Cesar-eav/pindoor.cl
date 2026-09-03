<?php

namespace App\Notifications;

use App\Models\EventoEntrada;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EntradaRechazada extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public EventoEntrada $entrada) {}

    public function via(): array
    {
        return ['mail', \App\Notifications\Channels\TelegramChannel::class];
    }

    public function toMail(): MailMessage
    {
        $e = $this->entrada;

        $prefijo = $e->es_prueba ? '[PRUEBA] ' : '';
        $motivo = $e->estado === 'anulada' ? 'anulado' : 'rechazado';

        return (new MailMessage)
            ->subject("{$prefijo}Entrada {$e->codigo_entrada} — pago {$motivo}")
            ->greeting("😕 Pago {$motivo}")
            ->line("Flow reportó el pago de esta entrada como {$motivo}. El cliente no completó la compra.")
            ->line("Evento: {$e->evento_titulo_al_pagar} — {$e->punto?->title}")
            ->line("Cliente: {$e->nombre_cliente} — {$e->email_cliente} — {$e->telefono_cliente}")
            ->line("Fecha del evento: {$e->evento_fecha_al_pagar->format('d/m/Y')}")
            ->line('Total: $' . number_format($e->monto_total, 0, ',', '.'))
            ->salutation('Pindoor');
    }

    public function toTelegram(): string
    {
        $e = $this->entrada;

        $prefijo = $e->es_prueba ? "🧪 <b>[PRUEBA]</b>\n" : '';
        $motivo = $e->estado === 'anulada' ? 'anulado' : 'rechazado';

        return $prefijo . "😕 <b>Pago {$motivo}</b>\n"
            . "Código: {$e->codigo_entrada}\n"
            . "Evento: {$e->evento_titulo_al_pagar}\n"
            . "Cliente: {$e->nombre_cliente}\n"
            . "Tel: {$e->telefono_cliente}\n"
            . "Email: {$e->email_cliente}\n"
            . "Fecha: {$e->evento_fecha_al_pagar->format('d/m/Y')}\n"
            . 'Total: $' . number_format($e->monto_total, 0, ',', '.');
    }
}
