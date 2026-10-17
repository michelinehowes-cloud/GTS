<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GraduateAccountCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $email;
    protected $temporaryPassword;
    protected $graduateName;

    /**
     * Create a new notification instance.
     */
    public function __construct($email, $temporaryPassword, $graduateName)
    {
        $this->email = $email;
        $this->temporaryPassword = $temporaryPassword;
        $this->graduateName = $graduateName;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(config('mail.from.address', 'gts@gto.com.ly'), config('mail.from.name', 'مكتب تدريب الخريجين'))
            ->subject('بيانات تفعيل حسابك في المنظومة - مكتب تدريب وتأهيل الخريجين')
            ->view('emails.graduate_account_created', [
                'email' => $this->email,
                'temporaryPassword' => $this->temporaryPassword,
                'graduateName' => $this->graduateName,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'email' => $this->email,
            'message' => 'تم إنشاء حساب جديد لك في النظام',
        ];
    }
}
