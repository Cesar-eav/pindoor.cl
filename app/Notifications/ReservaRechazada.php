<?php

namespace App\Notifications;

use App\Models\ReservaRuta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaRechazada extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ReservaRuta $reserva) {}

    public function via(): array
    {
        return ['mail', \App\Notifications\Channels\TelegramChannel::class];
    }

    public function toMail(): MailMessage
    {
        $r = $this->reserva;

        $prefijo = $r->es_prueba ? '[PRUEBA] ' : '';
        $motivo = $r->estado === 'anulada' ? 'anulado' : 'rechazado';

        return (new MailMessage)
            ->subject("{$prefijo}Reserva {$r->codigo_reserva} — pago {$motivo}")
            ->greeting("😕 Pago {$motivo}")
            ->line("Flow reportó el pago de esta reserva como {$motivo}. El cliente no completó la compra.")
            ->line("Ruta: {$r->rutaOperador?->ruta?->titulo} ({$r->rutaOperador?->operador?->nombre})")
            ->line("Cliente: {$r->nombre_cliente} — {$r->email_cliente} — {$r->telefono_cliente}")
            ->line("Fecha de visita: {$r->fecha_visita->format('d/m/Y')}")
            ->line('Total: $' . number_format($r->precio_total, 0, ',', '.'))
            ->action('Ver en el panel', url('/admin/reservas'))
            ->salutation('Pindoor');
    }

    public function toTelegram(): string
    {
        $r = $this->reserva;

        $prefijo = $r->es_prueba ? "🧪 <b>[PRUEBA]</b>\n" : '';
        $motivo = $r->estado === 'anulada' ? 'anulado' : 'rechazado';

        return $prefijo . "😕 <b>Pago {$motivo}</b>\n"
            . "Código: {$r->codigo_reserva}\n"
            . "Cliente: {$r->nombre_cliente}\n"
            . "Tel: {$r->telefono_cliente}\n"
            . "Email: {$r->email_cliente}\n"
            . "Ruta: {$r->rutaOperador?->ruta?->titulo}\n"
            . "Fecha: {$r->fecha_visita->format('d/m/Y')}\n"
            . 'Total: $' . number_format($r->precio_total, 0, ',', '.');
    }
}
