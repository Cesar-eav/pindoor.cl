<?php

namespace App\Mail;

use App\Models\ReservaRuta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ReservaRuta $reserva) {}

    public function envelope(): Envelope
    {
        $prefijo = $this->reserva->es_prueba ? '[PRUEBA] ' : '';

        return new Envelope(
            subject: "{$prefijo}Tu reserva Pindoor {$this->reserva->codigo_reserva} está confirmada",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reserva_confirmada',
            with: ['reserva' => $this->reserva->load('rutaOperador.ruta', 'horario')],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
