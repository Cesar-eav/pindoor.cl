<?php

namespace App\Notifications;

use App\Models\EventoEntrada;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EntradaPagada extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject("{$prefijo}Entrada {$e->codigo_entrada} pagada y confirmada")
            ->greeting('✅ Entrada pagada')
            ->line('Flow confirmó el pago de esta entrada. **Este es el aviso definitivo de pago exitoso.**')
            ->line("Evento: {$e->evento_titulo_al_pagar} — {$e->punto?->title}")
            ->line("Cliente: {$e->nombre_cliente} — {$e->email_cliente} — {$e->telefono_cliente}")
            ->line("Fecha del evento: {$e->evento_fecha_al_pagar->format('d/m/Y')}")
            ->line("Entradas: {$e->cantidad_entradas}")
            ->line('Total: $' . number_format($e->monto_total, 0, ',', '.'))
            ->salutation('Pindoor');
    }

    public function toTelegram(): string
    {
        $e = $this->entrada;

        $prefijo = $e->es_prueba ? "🧪 <b>[PRUEBA]</b>\n" : '';

        return $prefijo . "✅ <b>Entrada pagada y confirmada</b>\n"
            . "<i>Pago verificado por Flow — aviso definitivo.</i>\n\n"
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
