<?php

namespace App\Notifications\Channels;

use App\Models\Configuracion;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramChannel
{
    public function send($notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toTelegram')) {
            return;
        }

        $token = config('services.telegram.token');
        $chatId = $notifiable->routeNotificationFor('telegram', $notification) ?? Configuracion::telegramChatId();

        if (! $token || ! $chatId) {
            return;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $notification->toTelegram($notifiable),
            'parse_mode' => 'HTML',
        ]);

        if ($response->failed()) {
            Log::warning('[telegram] envío falló', ['response' => $response->body()]);
        }
    }
}
