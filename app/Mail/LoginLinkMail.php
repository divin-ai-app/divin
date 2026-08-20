<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $url,
        public int $minutesValid = 15,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Sign in to divin.ai');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.login-link');
    }
}
