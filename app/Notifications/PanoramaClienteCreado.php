<?php

namespace App\Notifications;

use App\Models\PuntoInteres;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class PanoramaClienteCreado extends Notification
{
    public function __construct(
        public PuntoInteres $punto,
        public string $titulo,
        public ?string $fecha,
    ) {}

    public function via(): array
    {
        return [\App\Notifications\Channels\TelegramChannel::class];
    }

    public function toTelegram(): string
    {
        $fecha = $this->fecha ? Carbon::parse($this->fecha)->format('d/m/Y') : 'Sin fecha';

        return "🏢 <b>Nuevo panorama de cliente</b>\n"
            . "Negocio: {$this->punto->title}\n"
            . "Evento: {$this->titulo}\n"
            . "Fecha: {$fecha}";
    }
}
