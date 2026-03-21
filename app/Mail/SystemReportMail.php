<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SystemReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $userName,
        public string $startDate,
        public string $endDate,
        public string $pdfPath,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your System Report is Ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.system-report',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('System_Report_' . $this->startDate . '_to_' . $this->endDate . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
