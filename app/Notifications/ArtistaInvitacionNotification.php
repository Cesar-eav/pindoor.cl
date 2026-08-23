<?php

namespace App\Notifications;

use App\Models\ArtistaInvitacion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArtistaInvitacionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ArtistaInvitacion $invitacion) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $artista = $this->invitacion->artista;

        return (new MailMessage)
            ->subject("Te invitaron a administrar {$artista->nombre} en Pindoor")
            ->greeting('¡Hola!')
            ->line("Te invitaron a administrar el perfil de **{$artista->nombre}** en Pindoor.")
            ->line('Al aceptar, podrás editar su información, galería y agenda junto al resto del equipo.')
            ->action('Aceptar invitación', route('artista.invitacion.aceptar', $this->invitacion->token))
            ->line('Este link vence en 7 días.')
            ->salutation('Pindoor');
    }
}
