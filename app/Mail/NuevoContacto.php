<?php

namespace App\Mail;

use App\Models\LeadContacto;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevoContacto extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public LeadContacto $lead) {}

    public function envelope(): Envelope
    {
        $tipo = $this->lead->tipo === 'artista' ? 'Artista' : 'Local/Negocio';

        return new Envelope(
            subject: "[Pindoor] Nuevo contacto — {$tipo}: {$this->lead->nombre}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contacto',
            with: ['lead' => $this->lead],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
