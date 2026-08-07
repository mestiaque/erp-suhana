<?php

namespace App\Mail;

use App\Models\Approval;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApprovalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Approval $approval)
    {
    }

    public function envelope(): Envelope
    {
        $fromAddress = config('approval.mail_from_address') ?: config('mail.from.address');
        $fromName    = config('approval.mail_from_name') ?: config('mail.from.name');

        return new Envelope(
            from: $fromAddress ? new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName) : null,
            subject: 'Approval Needed: '.$this->approval->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approval-request',
            with: ['approval' => $this->approval],
        );
    }
}
