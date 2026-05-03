<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewClientNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo cliente registrado en Pindoor')
            ->greeting('¡Nuevo registro!')
            ->line("**{$this->user->name}** se acaba de registrar.")
            ->line("Email: {$this->user->email}")
            ->line("Fecha: " . $this->user->created_at->format('d/m/Y H:i'))
            ->action('Ver en el panel', url('/admin/clientes'))
            ->salutation('Pindoor');
    }
}
