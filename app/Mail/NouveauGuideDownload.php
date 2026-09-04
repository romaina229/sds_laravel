<?php

namespace App\Mail;

use App\Models\GuideDownload;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouveauGuideDownload extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public GuideDownload $guideDownload)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📥 Nouveau téléchargement du guide Finance Pro — {$this->guideDownload->organisation}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nouveau-guide-download',
            with: ['guideDownload' => $this->guideDownload],
        );
    }
}
