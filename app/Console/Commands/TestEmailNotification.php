<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;

class TestEmailNotification extends Command
{
    protected $signature = 'test:email-notification 
                            {--user= : User ID to send test notification to}
                            {--email= : Email address to send test notification to}
                            {--all : Send test notification to all users}';

    protected $description = 'Test email notification system by sending test emails';

    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $this->info('🔍 فحص نظام الإشعارات عبر البريد الإلكتروني');
        $this->newLine();

        // فحص إعدادات البريد الإلكتروني
        $this->checkMailConfiguration();
        $this->newLine();

        // اختيار طريقة الإرسال
        if ($this->option('all')) {
            $this->sendToAllUsers();
        } elseif ($this->option('user')) {
            $this->sendToSpecificUser($this->option('user'));
        } elseif ($this->option('email')) {
            $this->sendToSpecificEmail($this->option('email'));
        } else {
            $this->interactiveMode();
        }

        return 0;
    }

    private function checkMailConfiguration()
    {
        $this->info('📧 فحص إعدادات البريد الإلكتروني:');

        $configs = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];

        foreach ($configs as $key => $value) {
            $status = $value ? '✓' : '✗';
            $displayValue = $key === 'MAIL_PASSWORD' ? '****' : ($value ?: 'غير محدد');
            $this->line("  {$status} {$key}: {$displayValue}");
        }
    }

    private function interactiveMode()
    {
        $this->info('🎯 الوضع التفاعلي');
        $this->newLine();

        $choice = $this->choice(
            'اختر طريقة الاختبار:',
            [
                '1' => 'إرسال لمستخدم محدد (User ID)',
                '2' => 'إرسال لبريد إلكتروني محدد',
                '3' => 'إرسال لجميع المستخدمين',
                '4' => 'عرض الإشعارات المرسلة',
                '5' => 'اختبار إرسال بريد مباشر',
            ],
            '1'
        );

        switch ($choice) {
            case '1':
                $userId = $this->ask('أدخل User ID');
                $this->sendToSpecificUser($userId);
                break;
            case '2':
                $email = $this->ask('أدخل البريد الإلكتروني');
                $this->sendToSpecificEmail($email);
                break;
            case '3':
                if ($this->confirm('هل أنت متأكد من إرسال إشعار لجميع المستخدمين؟')) {
                    $this->sendToAllUsers();
                }
                break;
            case '4':
                $this->showSentNotifications();
                break;
            case '5':
                $email = $this->ask('أدخل البريد الإلكتروني');
                $this->testDirectEmail($email);
                break;
        }
    }

    private function sendToSpecificUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            $this->error("❌ المستخدم غير موجود (ID: {$userId})");
            return;
        }

        if (!$user->email) {
            $this->error("❌ المستخدم ليس لديه بريد إلكتروني");
            return;
        }

        $this->info("📤 إرسال إشعار اختباري إلى: {$user->name} ({$user->email})");

        try {
            $notification = $this->notificationService->sendToUser(
                $user,
                'إشعار اختباري',
                'هذا إشعار اختباري من نظام التدريب والتوظيف للخريجين. إذا وصلك هذا البريد، فإن نظام الإشعارات يعمل بشكل صحيح.',
                'info',
                ['send_email' => true]
            );

            $this->info("✅ تم إنشاء الإشعار بنجاح (ID: {$notification->id})");

            if ($notification->sent_at) {
                $this->info("✅ تم إرسال البريد الإلكتروني بنجاح في: {$notification->sent_at}");
            } else {
                $this->warn("⚠️  لم يتم تحديث حالة الإرسال");
            }

            $this->checkEmailLogs();
        } catch (\Exception $e) {
            $this->error("❌ فشل الإرسال: " . $e->getMessage());
            $this->error("التفاصيل: " . $e->getTraceAsString());
        }
    }

    private function sendToSpecificEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("❌ البريد الإلكتروني غير صالح");
            return;
        }

        // إنشاء مستخدم وهمي للاختبار
        $this->info("📤 إرسال إشعار اختباري إلى: {$email}");

        try {
            // إنشاء إشعار وهمي
            $notification = new Notification([
                'title' => 'إشعار اختباري',
                'message' => 'هذا إشعار اختباري من نظام التدريب والتوظيف للخريجين.',
                'type' => 'info',
            ]);

            // إنشاء مستخدم وهمي مؤقت
            $tempUser = new User();
            $tempUser->email = $email;
            $tempUser->name = 'مستخدم اختباري';

            $notification->user = $tempUser;

            Mail::to($email)->send(new NotificationMail($notification));

            $this->info("✅ تم إرسال البريد الإلكتروني بنجاح");
            $this->checkEmailLogs();
        } catch (\Exception $e) {
            $this->error("❌ فشل الإرسال: " . $e->getMessage());
        }
    }

    private function sendToAllUsers()
    {
        $users = User::whereNotNull('email')->get();

        if ($users->isEmpty()) {
            $this->error("❌ لا يوجد مستخدمين لديهم بريد إلكتروني");
            return;
        }

        $this->info("📤 إرسال إشعار اختباري إلى {$users->count()} مستخدم");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($users as $user) {
            try {
                $this->notificationService->sendToUser(
                    $user,
                    'إشعار اختباري جماعي',
                    'هذا إشعار اختباري جماعي من نظام التدريب والتوظيف للخريجين.',
                    'info',
                    ['send_email' => true]
                );
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ نجح: {$successCount}");
        if ($failCount > 0) {
            $this->error("❌ فشل: {$failCount}");
        }
    }

    private function showSentNotifications()
    {
        $this->info('📋 آخر 10 إشعارات تم إرسالها:');
        $this->newLine();

        $notifications = Notification::with('user')
            ->whereNotNull('sent_at')
            ->latest('sent_at')
            ->limit(10)
            ->get();

        if ($notifications->isEmpty()) {
            $this->warn('لا توجد إشعارات مرسلة');
            return;
        }

        $headers = ['ID', 'المستخدم', 'البريد', 'العنوان', 'النوع', 'تاريخ الإرسال'];
        $rows = [];

        foreach ($notifications as $notification) {
            $rows[] = [
                $notification->id,
                $notification->user->name ?? 'N/A',
                $notification->user->email ?? 'N/A',
                substr($notification->title, 0, 30),
                $notification->type,
                $notification->sent_at->format('Y-m-d H:i'),
            ];
        }

        $this->table($headers, $rows);
    }

    private function testDirectEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error("❌ البريد الإلكتروني غير صالح");
            return;
        }

        $this->info("📧 اختبار إرسال بريد مباشر إلى: {$email}");

        try {
            Mail::raw('هذا بريد اختباري مباشر من نظام Laravel', function ($message) use ($email) {
                $message->to($email)
                    ->subject('اختبار البريد الإلكتروني');
            });

            $this->info("✅ تم إرسال البريد بنجاح");
            $this->checkEmailLogs();
        } catch (\Exception $e) {
            $this->error("❌ فشل الإرسال: " . $e->getMessage());
        }
    }

    private function checkEmailLogs()
    {
        $this->newLine();
        $this->info('💡 نصائح للتحقق من الإرسال:');

        $mailer = config('mail.default');

        if ($mailer === 'log') {
            $this->line('  - تحقق من ملف: storage/logs/laravel.log');
        } elseif ($mailer === 'smtp' && config('mail.mailers.smtp.host') === 'mailpit') {
            $this->line('  - افتح Mailpit في المتصفح: http://localhost:8025');
        } elseif ($mailer === 'smtp' && config('mail.mailers.smtp.host') === 'mailtrap.io') {
            $this->line('  - تحقق من صندوق Mailtrap الخاص بك');
        } else {
            $this->line('  - تحقق من صندوق البريد الإلكتروني للمستلم');
        }
    }
}
