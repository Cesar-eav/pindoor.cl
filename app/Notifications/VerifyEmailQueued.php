<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailQueued extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Activa tu cuenta en Pindoor')
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('Gracias por registrar tu negocio en Pindoor. Para activar tu cuenta, haz clic en el botón de abajo.')
            ->action('Verificar mi correo', $url)
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.')
            ->salutation('El equipo de Pindoor');
    }
}
