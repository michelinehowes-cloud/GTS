<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $type;
    public $data;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $message, $type = 'info', $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['database'];

        // إرسال بريد إلكتروني إذا تم طلبه صراحة في البيانات
        if (isset($this->data['send_email']) && $this->data['send_email'] === true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line($this->message);

        if (isset($this->data['action_url'])) {
            $mail->action('عرض التفاصيل', $this->data['action_url']);
        } elseif (isset($this->data['link'])) {
            $mail->action('عرض التفاصيل', $this->data['link']);
        } else {
            $mail->action('الدخول للنظام', url('/'));
        }

        return $mail->line('شكراً لاستخدامكم نظامنا.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'data' => $this->data,
            'created_at' => now()
        ];
    }
}
