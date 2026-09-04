<?php

namespace App\Mail;

use App\Models\GuideDownload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuideVisiteurMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GuideDownload $guideDownload, public string $downloadUrl)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre guide Finance Pro est disponible',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guide-visiteur',
            with: [
                'guideDownload' => $this->guideDownload,
                'downloadUrl'   => $this->downloadUrl,
            ],
        );
    }
}
