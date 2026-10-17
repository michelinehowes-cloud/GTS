<?php

namespace App\Mail;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The notification instance.
     *
     * @var \App\Models\Notification
     */
    public Notification $notification;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                config('mail.from.address', 'gts@gto.com.ly'),
                config('mail.from.name', 'مكتب تدريب الخريجين')
            ),
            subject: $this->notification->title,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $data = $this->notification->data;
        if (is_string($data)) {
            $data = json_decode($data, true) ?? [];
        } elseif (!is_array($data)) {
            $data = [];
        }

        $actionUrl = $data['action_url'] ?? $data['url'] ?? $data['link'] ?? null;
        $recipientName = $this->notification->user?->name;

        return new Content(
            view: 'emails.notification',
            with: [
                'notificationTitle' => $this->notification->title,
                'notificationMessage' => $this->notification->message,
                'notificationData' => $data,
                'actionUrl' => $actionUrl,
                'recipientName' => $recipientName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
