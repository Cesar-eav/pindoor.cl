<?php

namespace App\Mail;

use App\Models\EventoEntrada;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EntradaConfirmada extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public EventoEntrada $entrada) {}

    public function envelope(): Envelope
    {
        $prefijo = $this->entrada->es_prueba ? '[PRUEBA] ' : '';

        return new Envelope(
            subject: "{$prefijo}Tu entrada Pindoor {$this->entrada->codigo_entrada} está confirmada",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.entrada_confirmada',
            with: [
                'entrada' => $this->entrada->load('moduloItem', 'punto'),
            ],
        );
    }
}
