<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaimOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $profileName,
        public string $code,
        public int $minutesValid = 15,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Verify you own {$this->profileName} — divin.ai");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.claim-otp');
    }
}
