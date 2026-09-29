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

    /** @param array $content see BaseApprovalHandler::mailContent() */
    public function __construct(public Approval $approval, public array $content = [], public bool $reminder = false)
    {
    }

    public function envelope(): Envelope
    {
        $fromAddress = config('approval.mail_from_address') ?: config('mail.from.address');
        $fromName    = config('approval.mail_from_name') ?: config('mail.from.name');

        return new Envelope(
            from: $fromAddress ? new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName) : null,
            subject: ($this->reminder ? 'Reminder - ' : '').'Approval Needed: '.$this->approval->title
                .(isset($this->content['total']['value']) && ($this->content['total']['money'] ?? false)
                    ? ' - Tk '.number_format((float) $this->content['total']['value'], 2) : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approval-request',
            with: [
                'approval'      => $this->approval,
                'content'       => $this->content,
                'amountInWords' => $this->amountInWords(),
                'reminder'      => $this->reminder,
            ],
        );
    }

    /** Taka in words for a money total, via the Accounts package's converter when installed. */
    private function amountInWords(): ?string
    {
        $total = $this->content['total'] ?? null;
        $converter = 'ME\\AccSfl\\Services\\NumberToWordsService';

        if (! $total || ! ($total['money'] ?? false) || ! class_exists($converter)) {
            return null;
        }

        return app($converter)->taka((float) $total['value']);
    }
}
