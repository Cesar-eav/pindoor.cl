<?php

namespace App\Notifications;

use App\Models\ReservaRuta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservaIniciada extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject("{$prefijo}Intento de reserva {$r->codigo_reserva} — pendiente de pago")
            ->greeting('⚠️ Nuevo intento de reserva')
            ->line('Un cliente acaba de iniciar una reserva y fue redirigido a pagar con Flow.')
            ->line('**Este aviso no confirma que el pago se haya completado**, solo que la reserva quedó registrada. Si el pago se confirma, llegará un segundo aviso.')
            ->line("Ruta: {$r->rutaOperador?->ruta?->titulo} ({$r->rutaOperador?->operador?->nombre})")
            ->line("Cliente: {$r->nombre_cliente} — {$r->email_cliente} — {$r->telefono_cliente}")
            ->line("Fecha de visita: {$r->fecha_visita->format('d/m/Y')}")
            ->line("Personas: {$r->cantidad_adultos} adulto(s), {$r->cantidad_ninos} niño(s)")
            ->line('Total: $' . number_format($r->precio_total, 0, ',', '.'))
            ->action('Ver en el panel', url('/admin/reservas'))
            ->salutation('Pindoor');
    }

    public function toTelegram(): string
    {
        $r = $this->reserva;

        $prefijo = $r->es_prueba ? "🧪 <b>[PRUEBA]</b>\n" : '';

        return $prefijo . "⚠️ <b>Intento de reserva — pendiente de pago</b>\n"
            . "<i>Aún no confirma que el pago se haya completado.</i>\n\n"
            . "Código: {$r->codigo_reserva}\n"
            . "Cliente: {$r->nombre_cliente}\n"
            . "Tel: {$r->telefono_cliente}\n"
            . "Email: {$r->email_cliente}\n"
            . "Ruta: {$r->rutaOperador?->ruta?->titulo}\n"
            . "Fecha: {$r->fecha_visita->format('d/m/Y')}\n"
            . "Personas: {$r->cantidad_adultos} adulto(s), {$r->cantidad_ninos} niño(s)\n"
            . 'Total: $' . number_format($r->precio_total, 0, ',', '.');
    }
}
