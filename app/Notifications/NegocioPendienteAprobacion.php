<?php

namespace App\Notifications;

use App\Models\PuntoInteres;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NegocioPendienteAprobacion extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PuntoInteres $punto) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo negocio pendiente de aprobación en Pindoor')
            ->greeting('¡Nuevo registro pendiente!')
            ->line("**{$this->punto->title}** se registró y espera aprobación antes de publicarse.")
            ->line("Categoría: " . ($this->punto->categoria?->nombre ?? '—'))
            ->line("WhatsApp de contacto: " . ($this->punto->contacto_whatsapp ?: '—'))
            ->line("Email del usuario: " . ($this->punto->user?->email ?? '—'))
            ->action('Revisar en el panel', url('/admin/clientes'))
            ->salutation('Pindoor');
    }
}
