<?php

namespace App\Mail;

use App\Models\Library;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OverstayAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public Library $library;
    public Collection $overstayed;

    public function __construct(Library $library, Collection $overstayed)
    {
        $this->library = $library;
        $this->overstayed = $overstayed;
    }

    public function envelope(): Envelope
    {
        $count = $this->overstayed->count();

        return new Envelope(
            subject: $count . ' student' . ($count > 1 ? 's are' : ' is') . ' still checked in past shift time — ' . $this->library->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.overstay-alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
