<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AbsenConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $confirmationUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Penyelesaian Tugas Driver - Samarent',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.absen-confirmation',
            text: 'emails.absen-confirmation-text',
            with: [
                'confirmationUrl' => $this->confirmationUrl,
                'appName' => config('app.name', 'Samarent'),
                'supportEmail' => config('mail.from.address'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
