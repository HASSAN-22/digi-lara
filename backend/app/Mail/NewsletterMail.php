<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\Shopconfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private readonly string $email, private readonly string $msg, private readonly string $msgSubject)
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
            subject: $this->msgSubject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = json_decode(Shopconfig::getValue('store_detail')->value);
        return new Content(
            view: 'emails.newsletter',
            with: ['msg'=>$this->msg,'logo'=>$data->logo]
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
