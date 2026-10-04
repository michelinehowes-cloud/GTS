<?php
// ملف لتشغيل عملية البذر يدوياً
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "بدء إضافة المستخدمين التجريبيين...\n";

$seedPassword = env('SEED_USER_PASSWORD', 'UoT@' . date('Y') . '!Secure');

// قائمة المستخدمين للإضافة
$users = [
    [
        'name' => 'مدير النظام',
        'email' => 'admin@tripoliuniversity.edu.ly',
        'password' => $seedPassword,
        'role' => 'admin'
    ],
    [
        'name' => 'منسق التدريب',
        'email' => 'training@tripoliuniversity.edu.ly',
        'password' => $seedPassword,
        'role' => 'training_coordinator'
    ],
    [
        'name' => 'مسؤول الشراكات',
        'email' => 'partnership@tripoliuniversity.edu.ly',
        'password' => $seedPassword,
        'role' => 'partnership_officer'
    ],
    [
        'name' => 'مسؤول الإرشاد المهني',
        'email' => 'guidance@tripoliuniversity.edu.ly',
        'password' => $seedPassword,
        'role' => 'career_guidance_officer'
    ],
    [
        'name' => 'مسؤول التقييم والمتابعة',
        'email' => 'evaluation@tripoliuniversity.edu.ly',
        'password' => $seedPassword,
        'role' => 'evaluation_followup'
    ],
    [
        'name' => 'خريج تجريبي',
        'email' => 'graduate@tripoliuniversity.edu.ly',
        'password' => $seedPassword,
        'role' => 'graduate'
    ],
    [
        'name' => 'ممثل شركة تجريبية',
        'email' => 'company@tripoliuniversity.edu.ly',
        'password' => $seedPassword,
        'role' => 'company'
    ],
    [
        'name' => 'مسؤول الميديا',
        'email' => 'media@tripoliuniversity.edu.ly',
        'password' => $seedPassword,
        'role' => 'media_officer'
    ]
];

foreach ($users as $userData) {
    // التحقق إذا كان المستخدم موجوداً بالفعل
    $existingUser = User::where('email', $userData['email'])->first();
    
    if ($existingUser) {
        echo "المستخدم {$userData['email']} موجود بالفعل\n";
        continue;
    }
    
    // إنشاء المستخدم الجديد
    User::create([
        'name' => $userData['name'],
        'email' => $userData['email'],
        'password' => Hash::make($userData['password']),
        'role' => $userData['role'],
        'is_active' => true,
        'email_verified_at' => now(),
    ]);
    
    echo "تم إنشاء المستخدم: {$userData['name']} ({$userData['email']})\n";
}

echo "\n✅ تم إنشاء جميع المستخدمين بنجاح!\n";
echo "\n📋 معلومات الدخول:\n";
echo "========================\n";
foreach ($users as $user) {
    echo "👤 {$user['name']}\n";
    echo "📧 البريد: {$user['email']}\n";
    echo "🔑 كلمة المرور: {$user['password']}\n";
    echo "🎯 الدور: {$user['role']}\n";
    echo "---\n";
}

echo "\nيمكنك الآن استخدام أي من هذه الحسابات للدخول إلى النظام.\n";
?>
