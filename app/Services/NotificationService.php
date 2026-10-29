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
        if ($users->isEmpty()) {
            return collect();
        }

        $sendEmail = !empty($options['send_email']);
        $sendWhatsapp = !empty($options['send_whatsapp']);

        // إذا لم يكن مطلوباً إرسال بريد أو واتساب، نستخدم الحفظ الجماعي السريع (Bulk Insert) لتوفير أقصى سرعة
        if (!$sendEmail && !$sendWhatsapp && $users->count() > 5) {
            $now = now();
            $dataToInsert = [];
            foreach ($users as $user) {
                $dataToInsert[] = [
                    'user_id' => $user->id,
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'sender_id' => $options['sender_id'] ?? null,
                    'model_type' => $options['model_type'] ?? null,
                    'model_id' => $options['model_id'] ?? null,
                    'data' => isset($options['data']) ? json_encode($options['data']) : null,
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            foreach (array_chunk($dataToInsert, 500) as $chunk) {
                Notification::insert($chunk);
            }
            return collect();
        }

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
        $typeLabel = $training->type_arabic ?: 'برنامج تدريبي';
        $title = "{$typeLabel} جديد متاح: {$training->title}";

        $datesStr = ($training->start_date ? $training->start_date->format('Y-m-d') : 'قريباً')
                  . ($training->end_date ? ' إلى ' . $training->end_date->format('Y-m-d') : '');

        $message = "تم فتح باب التسجيل في {$typeLabel}: «{$training->title}».\n"
                 . "📅 التاريخ: {$datesStr}\n"
                 . ($training->timing_text ? "⏰ التوقيت: {$training->timing_text}\n" : "")
                 . "📍 المقر: " . ($training->location ?? 'جامعة طرابلس');

        $type = 'info';

        $details = [
            'البرنامج / الورشة' => $training->title,
            'النوع' => $typeLabel,
            'تاريخ الانعقاد' => $datesStr,
        ];

        if ($training->timing_text) {
            $details['توقيت الجلسات'] = $training->timing_text;
        }

        if ($training->training_days_text) {
            $details['أيام التدريب'] = $training->training_days_text;
        }

        $details['المكان'] = $training->location ?? 'جامعة طرابلس';
        $details['المقاعد المتاحة'] = $training->seats;

        $options = [
            'model_type' => get_class($training),
            'model_id' => $training->id,
            'data' => [
                'details' => $details,
                'action_url' => route('graduate.trainings.show', $training->id),
            ],
        ];

        // إشعار للخريجين
        $this->sendToRole('graduate', $title, $message, $type, array_merge($options, [
            'send_email' => false,
        ]));

        // إشعار لمسؤولي الإرشاد المهني
        $this->sendToRole('career_guidance_officer', $title, $message, $type, $options);

        // إشعار للتقييم والمتابعة
        $this->sendToRole('evaluation_followup', $title, $message, $type, $options);

        // إشعار لمسؤول الميديا
        $this->sendToRole('media_officer', $title, $message, $type, $options);
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
            'send_email' => false,
        ]);

        // إشعار لمسؤولي الإرشاد المهني
        $this->sendToRole('career_guidance_officer', "فرصة عمل جديدة: {$jobOpportunity->title}", $message, $type, [
            'model_type' => get_class($jobOpportunity),
            'model_id' => $jobOpportunity->id,
            'send_email' => false,
        ]);

        // إشعار لمدير النظام
        $this->sendToRole('admin', "فرصة عمل جديدة: {$jobOpportunity->title}", $message, $type, [
            'model_type' => get_class($jobOpportunity),
            'model_id' => $jobOpportunity->id,
            'send_email' => false,
        ]);
    }

    /**
     * إرسال إشعار عند تسجيل شركة جديدة ذاتياً
     */
    public function notifyNewCompanyRegistration(\App\Models\Company $company): void
    {
        $title = 'طلب تسجيل شركة جديدة';
        $message = "قامت شركة ({$company->name}) بالتسجيل في المنظومة وتنتظر المراجعة والاعتماد.";
        $type = 'info';

        $this->sendToRoles(['partnership_officer', 'admin'], $title, $message, $type, [
            'model_type' => get_class($company),
            'model_id' => $company->id,
            'send_email' => false,
        ]);
    }

    /**
     * إرسال إشعار للشركة عند اعتمادها
     */
    public function notifyCompanyApproved(\App\Models\Company $company): void
    {
        if (!$company->user) {
            return;
        }

        $title = 'تم اعتماد وتفعيل حساب شركتكم';
        $message = "تهانينا! تم اعتماد وتفعيل حساب شركة ({$company->name}) في المنظومة. يمكنكم الآن تسجيل الدخول وإضافة وإدارة فرص العمل والتدريب.";
        $type = 'success';

        $this->sendToUser($company->user, $title, $message, $type, [
            'model_type' => get_class($company),
            'model_id' => $company->id,
            'send_email' => false,
        ]);
    }

    /**
     * إرسال إشعار للشركة عند رفض الطلب
     */
    public function notifyCompanyRejected(\App\Models\Company $company, ?string $reason = null): void
    {
        if (!$company->user) {
            return;
        }

        $title = 'تحديث بشأن طلب تسجيل شركتكم';
        $message = "نأسف لإبلاغكم بأنه لم يتم اعتماد طلب تسجيل شركة ({$company->name}) حالياً." . ($reason ? " السبب: {$reason}" : "");
        $type = 'warning';

        $this->sendToUser($company->user, $title, $message, $type, [
            'model_type' => get_class($company),
            'model_id' => $company->id,
            'send_email' => false,
        ]);
    }

    /**
     * إرسال إشعار للمسؤولين عند قيام شركة بإنشاء فرصة عمل تنتظر الاعتماد
     */
    public function notifyCompanyJobSubmitted(\App\Models\JobOpportunity $jobOpportunity): void
    {
        $companyName = $jobOpportunity->company ? $jobOpportunity->company->name : 'إحدى الشركات';
        $title = 'فرصة عمل جديدة بانتظار الاعتماد';
        $message = "أضافت شركة ({$companyName}) فرصة جديدة: \"{$jobOpportunity->title}\" وتنتظر المراجعة والاعتماد قبل النشر.";
        $type = 'info';

        $this->sendToRoles(['partnership_officer', 'career_guidance_officer', 'admin'], $title, $message, $type, [
            'model_type' => get_class($jobOpportunity),
            'model_id' => $jobOpportunity->id,
            'send_email' => false,
        ]);
    }

    /**
     * إرسال إشعار للشركة عند اعتماد فرصة العمل ونشرها
     */
    public function notifyJobOpportunityApproved(\App\Models\JobOpportunity $jobOpportunity): void
    {
        $recipient = $jobOpportunity->creator ?? ($jobOpportunity->company ? $jobOpportunity->company->user : null);

        if ($recipient) {
            $title = 'تمت الموافقة على نشر فرصة العمل';
            $message = "تمت الموافقة على فرصة العمل \"{$jobOpportunity->title}\" ونشرها رسمياً للخريجين في المنظومة.";
            $type = 'success';

            $this->sendToUser($recipient, $title, $message, $type, [
                'model_type' => get_class($jobOpportunity),
                'model_id' => $jobOpportunity->id,
                'send_email' => false,
            ]);
        }

        // إشعار الخريجين بالفرصة الجديدة
        $this->notifyNewJobOpportunity($jobOpportunity);
    }

    /**
     * إرسال إشعار للشركة عند رفض فرصة العمل
     */
    public function notifyJobOpportunityRejected(\App\Models\JobOpportunity $jobOpportunity, ?string $reason = null): void
    {
        $recipient = $jobOpportunity->creator ?? ($jobOpportunity->company ? $jobOpportunity->company->user : null);

        if ($recipient) {
            $title = 'لم تتم الموافقة على نشر فرصة العمل';
            $message = "نأسف، لم تتم الموافقة على نشر فرصة العمل \"{$jobOpportunity->title}\"." . ($reason ? " سبب الرفض: {$reason}" : "");
            $type = 'warning';

            $this->sendToUser($recipient, $title, $message, $type, [
                'model_type' => get_class($jobOpportunity),
                'model_id' => $jobOpportunity->id,
                'send_email' => false,
            ]);
        }
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
        $training = $application->training;
        $typeLabel = $training->type_arabic ?: 'البرنامج التدريبي';
        $title = "تم قبول طلب التدريب 🎓";

        $startDateStr = $training->start_date ? $training->start_date->format('Y-m-d') : null;
        $endDateStr = $training->end_date ? $training->end_date->format('Y-m-d') : null;

        $datesStr = '';
        if ($startDateStr && $endDateStr) {
            $datesStr = ($startDateStr === $endDateStr) ? $startDateStr : "من {$startDateStr} إلى {$endDateStr}";
        } elseif ($startDateStr) {
            $datesStr = "يبدأ في {$startDateStr}";
        } else {
            $datesStr = 'يحدد لاحقاً';
        }

        $timingStr = $training->timing_text ?: 'يحدد لاحقاً مع إدارة التدريب';

        $message = "تهانينا! تم قبول طلبك للتسجيل في {$typeLabel}: «{$training->title}».\n"
                 . "📅 الموعد: {$datesStr}\n"
                 . "⏰ التوقيت: {$timingStr}\n"
                 . ($training->training_days_text ? "🗓️ الأيام: {$training->training_days_text}\n" : "")
                 . "📍 المقر: " . ($training->location ?? 'جامعة طرابلس');

        $type = 'success';

        $details = [
            'البرنامج التدريبي' => $training->title,
            'نوع البرنامج'      => $typeLabel,
            'تاريخ الانعقاد'    => $datesStr,
            'توقيت الجلسات'     => $timingStr,
        ];

        if ($training->training_days_text) {
            $details['أيام التدريب'] = $training->training_days_text;
        }

        $details['المقر / القاعة'] = $training->location ?? 'جامعة طرابلس';

        if ($training->company && $training->company->name) {
            $details['الشركة المقدمة'] = $training->company->name;
        }

        if ($training->instructor_name || ($training->trainer && $training->trainer->name)) {
            $details['المدرب / المحاضر'] = $training->instructor_name ?: $training->trainer->name;
        }

        if ($training->duration) {
            $details['المدة'] = $training->duration;
        }

        $this->sendToUser($application->user, $title, $message, $type, [
            'model_type' => get_class($application),
            'model_id' => $application->id,
            'send_email' => true,
            'data' => [
                'details' => $details,
                'action_url' => route('graduate.trainings.show', $training->id),
            ],
        ]);
    }

    /**
     * إرسال إشعار عند رفض طلب تدريب
     */
    public function notifyTrainingApplicationRejected($application): void
    {
        $training = $application->training;
        $typeLabel = $training->type_arabic ?: 'البرنامج التدريبي';
        $title = "تحديث بخصوص طلب {$typeLabel}";
        $message = "نأسف لإبلاغك بأنه لم يتم قبول طلبك في {$typeLabel}: «{$training->title}» نظراً لاكتمال المقاعد المتاحة.";
        $type = 'warning';

        $details = [
            'البرنامج / الورشة' => $training->title,
            'النوع' => $typeLabel,
            'تاريخ التدريب' => ($training->start_date ? $training->start_date->format('Y-m-d') : '—') . ($training->end_date ? ' إلى ' . $training->end_date->format('Y-m-d') : ''),
        ];

        if ($training->timing_text) {
            $details['التوقيت'] = $training->timing_text;
        }

        $this->sendToUser($application->user, $title, $message, $type, [
            'model_type' => get_class($application),
            'model_id' => $application->id,
            'send_email' => true,
            'data' => [
                'details' => $details,
                'action_url' => route('graduate.trainings'),
            ],
        ]);
    }

    /**
     * إرسال إشعار وتأكيد بالبريد الإلكتروني عند التسجيل في ورشة عمل أو فعالية علمية
     */
    public function notifyJobFairEventRegistration(User $user, \App\Models\JobFairEvent $event): void
    {
        $typeLabel = $event->type_label;
        $title = "تأكيد التسجيل في {$event->type_short_label}: {$event->title}";

        $ampmStart = ($event->start_time && $event->start_time->format('A') === 'AM') ? 'ص' : 'م';
        $ampmEnd = ($event->end_time && $event->end_time->format('A') === 'AM') ? 'ص' : 'م';

        $timeDisplay = '';
        if ($event->start_time && $event->end_time) {
            $timeDisplay = "من " . $event->start_time->format('h:i') . " {$ampmStart} إلى " . $event->end_time->format('h:i') . " {$ampmEnd}";
        } elseif ($event->start_time) {
            $timeDisplay = "الساعة " . $event->start_time->format('h:i') . " {$ampmStart}";
        }

        $dateDisplay = $event->start_time ? $event->start_time->format('Y-m-d') : ($event->jobFair ? $event->jobFair->event_date->format('Y-m-d') : 'قريباً');

        $message = "تم تأكيد تسجيلك بنجاح في {$typeLabel}: «{$event->title}».\n"
                 . "📅 التاريخ: {$dateDisplay}\n"
                 . ($timeDisplay ? "⏰ التوقيت: {$timeDisplay}\n" : "")
                 . "📍 القاعة / المكان: " . ($event->location ?: 'المدرج الرئيسي - جامعة طرابلس');

        $details = [
            'الفعالية / الورشة' => $event->title,
            'النوع' => $typeLabel,
            'التاريخ' => $dateDisplay,
        ];

        if ($timeDisplay) {
            $details['التوقيت'] = $timeDisplay;
        }

        $details['المكان'] = $event->location ?: 'جامعة طرابلس';

        if ($event->speaker_name) {
            $details['المحاضر / المدرب'] = $event->speaker_name . ($event->speaker_title ? " ({$event->speaker_title})" : "");
        }

        $this->sendToUser($user, $title, $message, 'success', [
            'model_type' => get_class($event),
            'model_id' => $event->id,
            'send_email' => true,
            'data' => [
                'details' => $details,
                'action_url' => route('job-fair.public.events.show', $event->id),
            ],
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
        } catch (\Throwable $e) {
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
            'sent_to_company' => 'مرسل للشركة',
            'approved' => 'مقبول',
            'accepted' => 'مقبول وتم التوظيف',
            'rejected' => 'مرفوض',
            'interview' => 'تم تحديد موعد مقابلة',
            'under_review' => 'قيد الدراسة (تحت المراجعة)',
            'interview_scheduled' => 'تم تحديد موعد مقابلة',
            'withdrawn' => 'مسحوب',
        ];

        $statusLabel = $statusText[$status] ?? $status;
        $title = 'تحديث حالة الترشيح';
        $jobTitle = ($nomination->jobOpportunity && $nomination->jobOpportunity->title)
            ? $nomination->jobOpportunity->title
            : 'الفرصة الوظيفية';
        $message = "تم تحديث حالة ترشيحك للوظيفة: {$jobTitle} إلى: {$statusLabel}";
        $type = in_array($status, ['approved', 'accepted']) ? 'success' : ($status === 'rejected' ? 'warning' : 'info');

        // إشعار للخريج فقط إذا كان لديه حساب مستخدم نشط
        $user = $nomination->graduate ? ($nomination->graduate->user ?? null) : null;
        if ($user instanceof User) {
            $this->sendToUser($user, $title, $message, $type, [
                'model_type' => get_class($nomination),
                'model_id' => $nomination->id,
                'send_email' => false,
            ]);
        }
    }

    /**
     * إرسال إشعار فوري للخريج عند اعتماد وإصدار شهادته التدريبية
     */
    public function notifyCertificateIssued(\App\Models\Certificate $certificate): void
    {
        if (!$certificate->user) {
            return;
        }

        $title = 'تم اعتماد وإصدار شهادتك التدريبية 🎓';
        $message = "تهانينا يا {$certificate->recipient_name}! تم اعتماد وإصدار شهادتك الرسمية في «{$certificate->title}». يمكنك الآن معاينتها وتحميلها PDF عبر سجل الشهادات.";

        $this->sendToUser($certificate->user, $title, $message, 'success', [
            'model_type' => get_class($certificate),
            'model_id' => $certificate->id,
            'data' => [
                'certificate_code' => $certificate->certificate_code,
                'hours' => $certificate->hours,
                'url' => route('graduate.certificates.show', $certificate->id),
            ],
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
