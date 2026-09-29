<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use SerializesModels;

    public function __construct(public ?string $body = null)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Mail from '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test-mail',
            with: ['body' => $this->body ?: 'This is a test mail.'],
        );
    }
}
