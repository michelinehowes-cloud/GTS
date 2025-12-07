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
            ->subject('حساب جديد في نظام إدارة الخريجين')
            ->greeting('مرحباً ' . $this->graduateName . ',')
            ->line('تم إنشاء حساب لك في نظام إدارة الخريجين بجامعة طرابلس.')
            ->line('**بيانات الدخول:**')
            ->line('البريد الإلكتروني: **' . $this->email . '**')
            ->line('كلمة المرور المؤقتة: **' . $this->temporaryPassword . '**')
            ->line('')
            ->line('⚠️ **مهم جداً:**')
            ->line('• يجب عليك تغيير كلمة المرور عند أول تسجيل دخول')
            ->line('• لا تشارك كلمة المرور مع أي شخص')
            ->line('• احفظ بيانات الدخول في مكان آمن')
            ->line('')
            ->action('تسجيل الدخول الآن', url('/login'))
            ->line('إذا كانت لديك أي استفسارات، يرجى التواصل مع مكتب تدريب الخريجين.')
            ->salutation('مع تحياتنا،')
            ->salutation('فريق نظام إدارة الخريجين')
            ->salutation('جامعة طرابلس');
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
