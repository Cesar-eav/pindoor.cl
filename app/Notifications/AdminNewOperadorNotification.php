<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNewOperadorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $user) {}

    public function via(): array
    {
        return ['mail', \App\Notifications\Channels\TelegramChannel::class];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo operador turístico registrado en Pindoor')
            ->greeting('¡Nuevo registro!')
            ->line("**{$this->user->name}** se acaba de registrar como operador turístico.")
            ->line("Email: {$this->user->email}")
            ->line("Fecha: " . $this->user->created_at->format('d/m/Y H:i'))
            ->action('Ver en el panel', url('/admin/operadores'))
            ->salutation('Pindoor');
    }

    public function toTelegram(): string
    {
        return "🧭 <b>Nuevo operador turístico registrado</b>\n"
            . "Nombre: {$this->user->name}\n"
            . "Email: {$this->user->email}\n"
            . "Fecha: " . $this->user->created_at->format('d/m/Y H:i');
    }
}
