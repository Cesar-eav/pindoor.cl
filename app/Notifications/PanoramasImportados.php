<?php

namespace App\Notifications;

use App\Models\Panorama;
use Illuminate\Notifications\Notification;

class PanoramasImportados extends Notification
{
    public function __construct(
        public string $fuente,
        public int $creados,
        public int $actualizados,
        public int $omitidos,
    ) {}

    public function via(): array
    {
        return [\App\Notifications\Channels\TelegramChannel::class];
    }

    public function toTelegram(): string
    {
        $info = Panorama::FUENTES[$this->fuente] ?? ['label' => ucfirst($this->fuente), 'emoji' => '📥'];

        return "{$info['emoji']} <b>Importación {$info['label']}</b>\n"
            . "Creados: {$this->creados}\n"
            . "Actualizados: {$this->actualizados}\n"
            . "Omitidos: {$this->omitidos}";
    }
}
