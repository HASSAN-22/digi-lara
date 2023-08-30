<?php

namespace App\Mail;

use App\Models\Shopconfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmailMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private readonly string $email, private readonly string $message, private readonly string $issue)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.mailer.from.address'),
            to: $this->email,
            subject: $this->issue,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $logo = json_decode(Shopconfig::getValue('store_detail')->value)->logo;
        return new Content(
            view: 'emails.email',
            with: ['msg'=>$this->message,'logo'=>$logo]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
