<?php

namespace App\Mail;

use App\Models\Shopconfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private readonly string $answer, private readonly string $email)
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
            subject: config('app.name') . '  پاسخ سوال شما ',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $logo = json_decode(Shopconfig::getValue('store_detail')->value)->logo;
        return new Content(
            view: 'emails.contact',
            with: ['msg'=>$this->answer,'logo'=>$logo]
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
