<?php

namespace App\Notifications;

use App\Models\ReclamoNegocio;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReclamoAprobadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ReclamoNegocio $reclamo) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject("¡Tu solicitud para {$this->reclamo->punto->title} fue aprobada!")
            ->greeting('¡Hola ' . $this->reclamo->name . '!')
            ->line("Aprobamos tu solicitud para administrar el perfil de **{$this->reclamo->punto->title}** en Pindoor.")
            ->line('Crea tu contraseña (o continúa con Google) para activarlo y empezar a editar fotos, menú, eventos y promociones.')
            ->action('Activar mi perfil', route('reclamo.activar', $this->reclamo->activation_token))
            ->line('Este link vence en 48 horas.')
            ->salutation('Pindoor');
    }
}
