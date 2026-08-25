<?php

namespace App\Notifications;

use App\Models\ReclamoNegocio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoReclamoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ReclamoNegocio $reclamo) {}

    public function via(): array
    {
        return ['mail', \App\Notifications\Channels\TelegramChannel::class];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva solicitud de activación de perfil en Pindoor')
            ->greeting('¡Nueva solicitud!')
            ->line("**{$this->reclamo->name}** solicita activar el perfil de **{$this->reclamo->punto->title}**.")
            ->line("Email: {$this->reclamo->email}")
            ->line("WhatsApp: " . ($this->reclamo->whatsapp ?: '—'))
            ->action('Revisar en el panel', route('admin.reclamos.index'))
            ->salutation('Pindoor');
    }

    public function toTelegram(): string
    {
        return "📩 <b>Nueva solicitud de activación de perfil</b>\n"
            . "Negocio: {$this->reclamo->punto->title} (ID {$this->reclamo->punto_id})\n"
            . "Nombre: {$this->reclamo->name}\n"
            . "Email: {$this->reclamo->email}\n"
            . "WhatsApp: " . ($this->reclamo->whatsapp ?: '—');
    }
}
