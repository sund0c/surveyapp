<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemporaryPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $temporaryPassword,
        public bool $isNewAccount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isNewAccount
                ? 'Akun Anda di Survei Kepuasan Layanan Persandian'
                : 'Password Sementara - Survei Kepuasan Layanan Persandian',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.temporary-password',
            with: [
                'recipientName' => $this->recipientName,
                'temporaryPassword' => $this->temporaryPassword,
                'isNewAccount' => $this->isNewAccount,
                'loginUrl' => route('login'),
            ],
        );
    }
}
