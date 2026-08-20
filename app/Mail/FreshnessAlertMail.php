<?php

namespace App\Mail;

use App\Models\FreshnessCheckLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent by Console\Commands\CheckFreshness when a drift is first detected — plan §4 flow 3. */
class FreshnessAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FreshnessCheckLog $log,
        public string $profileName,
        public string $reportUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "We found a data mismatch for {$this->profileName} — divin.ai");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.freshness-alert', with: [
            'discrepancies' => $this->log->discrepancies,
        ]);
    }
}
