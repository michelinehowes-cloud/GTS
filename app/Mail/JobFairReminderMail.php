<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\JobFair;
use App\Models\JobFairRegistration;

class JobFairReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fair;
    public $registration;

    /**
     * Create a new message instance.
     */
    public function __construct(JobFair $fair, JobFairRegistration $registration)
    {
        $this->fair = $fair;
        $this->registration = $registration;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تذكير: غداً موعد معرض التوظيف - ' . $this->fair->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.job_fair_reminder',
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
