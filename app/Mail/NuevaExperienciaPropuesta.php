<?php

namespace App\Mail;

use App\Models\Experiencia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaExperienciaPropuesta extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Experiencia $experiencia) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Pindoor] Nueva experiencia propuesta — ' . $this->experiencia->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.experiencia_propuesta',
            with: ['experiencia' => $this->experiencia],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
