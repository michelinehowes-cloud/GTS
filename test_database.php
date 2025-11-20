<?php
// ملف اختبار اتصال قاعدة البيانات
try {
    echo "🔍 اختبار اتصال قاعدة البيانات...\n";
    
    // تحميل ملفات Laravel
    require_once __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // اختبار الاتصال
    $pdo = DB::connection()->getPdo();
    echo "✅ تم الاتصال بقاعدة البيانات بنجاح\n";
    echo "📊 قاعدة البيانات: " . DB::connection()->getDatabaseName() . "\n";
    
    // التحقق من وجود جدول المستخدمين
    $tables = DB::select('SHOW TABLES');
    echo "📋 عدد الجداول: " . count($tables) . "\n";
    
    $usersTableExists = false;
    foreach ($tables as $table) {
        $tableName = $table->{'Tables_in_' . DB::connection()->getDatabaseName()};
        echo "   - $tableName\n";
        if ($tableName === 'users') {
            $usersTableExists = true;
        }
    }
    
    if ($usersTableExists) {
        echo "✅ جدول المستخدمين موجود\n";
        
        // التحقق من عدد المستخدمين
        $userCount = DB::table('users')->count();
        echo "👥 عدد المستخدمين: $userCount\n";
        
        // عرض المستخدمين الموجودين
        $users = DB::table('users')->select('name', 'email', 'role')->get();
        if ($users->count() > 0) {
            echo "\n📋 المستخدمين الموجودين:\n";
            foreach ($users as $user) {
                echo "   👤 {$user->name} ({$user->email}) - {$user->role}\n";
            }
        } else {
            echo "⚠️ لا يوجد مستخدمين في قاعدة البيانات\n";
        }
    } else {
        echo "❌ جدول المستخدمين غير موجود\n";
        echo "💡 قد تحتاج إلى تشغيل التهجيرات:\n";
        echo "   php artisan migrate\n";
    }
    
} catch (Exception $e) {
    echo "❌ خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage() . "\n";
    echo "\n🔧 استكشاف الأخطاء:\n";
    echo "1. تأكد من تشغيل خادم MySQL\n";
    echo "2. تحقق من إعدادات قاعدة البيانات في ملف .env\n";
    echo "3. تأكد من وجود قاعدة البيانات 'laravel'\n";
    echo "4. تأكد من صلاحيات المستخدم 'root'\n";
}
?>
