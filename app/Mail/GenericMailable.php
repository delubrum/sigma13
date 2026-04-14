<?php

declare(strict_types=1);

namespace App\Mail;

use App\Domain\Shared\Data\EmailData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class GenericMailable extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly EmailData $emailData
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->emailData->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->emailData->template,
            with: array_merge($this->emailData->data, [
                'icon' => $this->emailData->icon,
                'color' => $this->emailData->color,
                'emailData' => $this->emailData,
            ]),
        );
    }

    /** @return array<int, \Illuminate\Mail\Mailables\Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
