<?php

namespace App\Mail;

use App\Models\ReservaRuta;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
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
            with: [
                'reserva' => $this->reserva->load('rutaOperador.ruta', 'horario'),
                'qrPng'   => $this->generarQrCheckin(),
            ],
        );
    }

    /**
     * QR que el operador escanea en el punto de encuentro: abre la página de
     * check-in del admin (route flow.confirmacion no participa acá, es solo
     * para el control de ingreso, no para el pago).
     */
    private function generarQrCheckin(): string
    {
        $url = route('admin.reservas.checkin.show', $this->reserva->codigo_reserva);

        return (new Builder(
            writer: new PngWriter(),
            data: $url,
            size: 220,
            margin: 8,
        ))->build()->getString();
    }

    public function attachments(): array
    {
        return [];
    }
}
