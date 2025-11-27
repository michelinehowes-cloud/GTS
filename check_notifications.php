<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Notification;

echo "إحصائيات الإشعارات لكل المستخدمين:\n";
echo str_repeat('=', 50) . "\n";

$stats = Notification::selectRaw('user_id, COUNT(*) as total, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread')
    ->groupBy('user_id')
    ->with('user:id,name,email')
    ->get();

foreach ($stats as $stat) {
    echo "المستخدم: " . ($stat->user->name ?? 'غير معروف') . " (" . $stat->user_id . ")\n";
    echo "إجمالي الإشعارات: " . $stat->total . "\n";
    echo "غير مقروءة: " . $stat->unread . "\n";
    echo str_repeat('-', 30) . "\n";
}

echo "\nإجمالي الإشعارات في النظام: " . Notification::count() . "\n";
echo "إجمالي الإشعارات غير المقروءة: " . Notification::unread()->count() . "\n";

// عرض أحدث 10 إشعارات
echo "\nأحدث 10 إشعارات:\n";
echo str_repeat('=', 50) . "\n";

$recentNotifications = Notification::with(['user:id,name', 'sender:id,name'])
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

foreach ($recentNotifications as $notification) {
    echo "ID: {$notification->id}\n";
    echo "العنوان: {$notification->title}\n";
    echo "المستخدم: " . ($notification->user->name ?? 'غير معروف') . "\n";
    echo "المرسل: " . ($notification->sender->name ?? 'النظام') . "\n";
    echo "النوع: {$notification->type}\n";
    echo "مقروء: " . ($notification->is_read ? 'نعم' : 'لا') . "\n";
    echo "التاريخ: {$notification->created_at}\n";
    echo str_repeat('-', 30) . "\n";
}
