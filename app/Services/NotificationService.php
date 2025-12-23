<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Mail\NotificationMail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * إنشاء إشعار جديد
     */
    public function create(array $data): Notification
    {
        $notification = Notification::create($data);

        // إرسال البريد الإلكتروني إذا كان مطلوباً
        if (isset($data['send_email']) && $data['send_email']) {
            $this->sendEmailNotification($notification);
        }

        // إرسال إشعار الواتساب إذا كان مطلوباً
        if (isset($data['send_whatsapp']) && $data['send_whatsapp']) {
            $this->sendWhatsappNotification($notification);
        }

        return $notification;
    }

    /**
     * إرسال إشعار لمستخدم واحد
     */
    public function sendToUser(User $user, string $title, string $message, string $type = 'info', array $options = []): Notification
    {
        return $this->create(array_merge([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'user_id' => $user->id,
        ], $options));
    }

    /**
     * إرسال إشعار لعدة مستخدمين
     */
    public function sendToUsers(Collection $users, string $title, string $message, string $type = 'info', array $options = []): Collection
    {
        $notifications = collect();

        foreach ($users as $user) {
            $notifications->push($this->sendToUser($user, $title, $message, $type, $options));
        }

        return $notifications;
    }

    /**
     * إرسال إشعار لمستخدمين بدور معين
     */
    public function sendToRole(string $role, string $title, string $message, string $type = 'info', array $options = []): Collection
    {
        $users = User::where('role', $role)->get();
        return $this->sendToUsers($users, $title, $message, $type, $options);
    }

    /**
     * إرسال إشعار لمستخدمين بأدوار متعددة
     */
    public function sendToRoles(array $roles, string $title, string $message, string $type = 'info', array $options = []): Collection
    {
        $users = User::whereIn('role', $roles)->get();
        return $this->sendToUsers($users, $title, $message, $type, $options);
    }

    /**
     * إرسال إشعار عند إنشاء تدريب جديد
     */
    public function notifyNewTraining($training): void
    {
        $title = 'تدريب جديد متاح';
        $message = "تم إضافة تدريب جديد: {$training->title}";
        $type = 'info';

        // إشعار للخريجين
        $this->sendToRole('graduate', $title, $message, $type, [
            'model_type' => get_class($training),
            'model_id' => $training->id,
            'send_email' => true,
        ]);

        // إشعار لمسؤولي الإرشاد المهني
        $this->sendToRole('career_guidance_officer', "تدريب جديد: {$training->title}", $message, $type, [
            'model_type' => get_class($training),
            'model_id' => $training->id,
        ]);

        // إشعار للتقييم والمتابعة
        $this->sendToRole('evaluation_followup', "تدريب جديد: {$training->title}", $message, $type, [
            'model_type' => get_class($training),
            'model_id' => $training->id,
        ]);

        // إشعار لمسؤول الميديا
        $this->sendToRole('media_officer', "تدريب جديد: {$training->title}", $message, $type, [
            'model_type' => get_class($training),
            'model_id' => $training->id,
        ]);
    }

    /**
     * إرسال إشعار عند إنشاء فرصة عمل جديدة
     */
    public function notifyNewJobOpportunity($jobOpportunity): void
    {
        $title = 'فرصة عمل جديدة';
        $message = "تم إضافة فرصة عمل جديدة: {$jobOpportunity->title}";
        $type = 'success';

        // إشعار للخريجين
        $this->sendToRole('graduate', $title, $message, $type, [
            'model_type' => get_class($jobOpportunity),
            'model_id' => $jobOpportunity->id,
            'send_email' => true,
        ]);

        // إشعار لمسؤولي الإرشاد المهني
        $this->sendToRole('career_guidance_officer', "فرصة عمل جديدة: {$jobOpportunity->title}", $message, $type, [
            'model_type' => get_class($jobOpportunity),
            'model_id' => $jobOpportunity->id,
        ]);

        // إشعار لمدير النظام
        $this->sendToRole('admin', "فرصة عمل جديدة: {$jobOpportunity->title}", $message, $type, [
            'model_type' => get_class($jobOpportunity),
            'model_id' => $jobOpportunity->id,
        ]);
    }

    /**
     * إرسال إشعار إجراء نظامي عام
     */
    public function notifySystemAction(string $title, string $message, array $roles = [], string $type = 'info', $model = null): void
    {
        $options = [];
        if ($model) {
            $options['model_type'] = get_class($model);
            $options['model_id'] = $model->id;
        }

        if (empty($roles)) {
            $roles = ['graduate', 'company', 'training_coordinator', 'partnership_officer', 'career_guidance_officer'];
        }

        $this->sendToRoles($roles, $title, $message, $type, $options);
    }

    /**
     * إرسال إشعار عند الموافقة على طلب تدريب
     */
    public function notifyTrainingApplicationApproved($application): void
    {
        $title = 'تمت الموافقة على طلب التدريب';
        $message = "تمت الموافقة على طلبك للتدريب: {$application->training->title}";
        $type = 'success';

        $this->sendToUser($application->user, $title, $message, $type, [
            'model_type' => get_class($application),
            'model_id' => $application->id,
            'send_email' => true,
        ]);
    }

    /**
     * إرسال إشعار عند رفض طلب تدريب
     */
    public function notifyTrainingApplicationRejected($application): void
    {
        $title = 'تم رفض طلب التدريب';
        $message = "تم رفض طلبك للتدريب: {$application->training->title}";
        $type = 'warning';

        $this->sendToUser($application->user, $title, $message, $type, [
            'model_type' => get_class($application),
            'model_id' => $application->id,
            'send_email' => true,
        ]);
    }

    /**
     * إرسال إشعار تذكيري
     */
    public function sendReminder(User $user, string $title, string $message, array $options = []): Notification
    {
        return $this->sendToUser($user, $title, $message, 'warning', array_merge([
            'send_email' => true,
        ], $options));
    }

    /**
     * إرسال إشعار تحديث حالة
     */
    public function sendStatusUpdate(User $user, string $title, string $message, array $options = []): Notification
    {
        return $this->sendToUser($user, $title, $message, 'info', $options);
    }

    /**
     * إرسال البريد الإلكتروني للإشعار
     */
    private function sendEmailNotification(Notification $notification): void
    {
        try {
            if ($notification->user && $notification->user->email) {
                Mail::to($notification->user->email)->send(new NotificationMail($notification));
                $notification->markAsSent();
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send email notification: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على إحصائيات الإشعارات
     */
    public function getStats(): array
    {
        return [
            'total' => Notification::count(),
            'unread' => Notification::unread()->count(),
            'today' => Notification::whereDate('created_at', today())->count(),
            'by_type' => [
                'info' => Notification::ofType('info')->count(),
                'success' => Notification::ofType('success')->count(),
                'warning' => Notification::ofType('warning')->count(),
                'danger' => Notification::ofType('danger')->count(),
            ],
        ];
    }

    /**
     * تنظيف الإشعارات القديمة
     */
    public function cleanupOldNotifications(int $days = 30): int
    {
        return Notification::where('created_at', '<', now()->subDays($days))
            ->where('is_read', true)
            ->delete();
    }

    /**
     * إرسال إشعار عند تقديم طلب توظيف
     */
    public function notifyJobApplication($jobApplication): void
    {
        $title = 'طلب توظيف جديد';
        $message = "تقدم {$jobApplication->user->name} بطلب للوظيفة: {$jobApplication->jobOpportunity->title}";
        $type = 'info';

        // إشعار لمسؤول الإرشاد المهني
        $this->sendToRole('career_guidance_officer', $title, $message, $type, [
            'model_type' => get_class($jobApplication),
            'model_id' => $jobApplication->id,
        ]);

        // إشعار لمسؤول الشراكات والتوظيف
        $this->sendToRole('partnership_officer', $title, $message, $type, [
            'model_type' => get_class($jobApplication),
            'model_id' => $jobApplication->id,
        ]);

        // إشعار للمدير
        $this->sendToRole('admin', $title, $message, $type, [
            'model_type' => get_class($jobApplication),
            'model_id' => $jobApplication->id,
        ]);
    }

    /**
     * إرسال إشعار عند ترشيح خريج لوظيفة
     */
    public function notifyJobNomination($nomination, $nominatedBy = null): void
    {
        $title = 'تم ترشيحك لوظيفة';
        $message = "تم ترشيحك للوظيفة: {$nomination->jobOpportunity->title}";
        if ($nominatedBy) {
            $message .= " من قبل {$nominatedBy->name}";
        }
        $type = 'success';

        // إشعار للخريج المرشح
        $this->sendToUser($nomination->graduate->user, $title, $message, $type, [
            'model_type' => get_class($nomination),
            'model_id' => $nomination->id,
            'send_email' => true,
        ]);

        // إشعار لمسؤول الشراكات والتوظيف
        $this->sendToRole(
            'partnership_officer',
            "ترشيح جديد: {$nomination->graduate->user->name}",
            "تم ترشيح {$nomination->graduate->user->name} للوظيفة: {$nomination->jobOpportunity->title}",
            $type,
            [
                'model_type' => get_class($nomination),
                'model_id' => $nomination->id,
            ]
        );
    }

    /**
     * إرسال إشعار عند تحديث حالة الترشيح
     */
    public function notifyNominationStatusUpdate($nomination, $status): void
    {
        $statusText = [
            'pending' => 'قيد المراجعة',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
            'interview' => 'تم تحديد موعد مقابلة',
        ];

        $title = 'تحديث حالة الترشيح';
        $message = "تم تحديث حالة ترشيحك للوظيفة: {$nomination->jobOpportunity->title} إلى: {$statusText[$status]}";
        $type = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'warning' : 'info');

        // إشعار للخريج
        $this->sendToUser($nomination->graduate->user, $title, $message, $type, [
            'model_type' => get_class($nomination),
            'model_id' => $nomination->id,
            'send_email' => true,
        ]);
    }

    /**
     * إرسال إشعار الواتساب
     */
    private function sendWhatsappNotification(Notification $notification): void
    {
        try {
            // هنا يجب إضافة منطق إرسال إشعار الواتساب
            // ستحتاج إلى دمج مع API مزود خدمة الواتساب (مثل Twilio, Meta for Developers, الخ)
            // مثال:
            // if ($notification->user && $notification->user->phone_number) {
            //     $client = new \Twilio\Rest\Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
            //     $client->messages->create(
            //         "whatsapp:{$notification->user->phone_number}",
            //         [
            //             'from' => 'whatsapp:' . env('TWILIO_WHATSAPP_FROM'),
            //             'body' => "إشعار جديد: {$notification->title}\n{$notification->message}",
            //         ]
            //     );
            // }
            \Log::info('WhatsApp notification placeholder triggered for: ' . $notification->id);
            // قد تحتاج إلى تحديث حالة الإشعار هنا لتشير إلى إرسال الواتساب
        } catch (\Exception $e) {
            \Log::error('Failed to send WhatsApp notification: ' . $e->getMessage());
        }
    }
}
