<?php
/**
 * أداة الإعداد والتشخيص لمنصة تدريب الخريجين على DirectAdmin / cPanel
 * جامعة طرابلس - gto.com.ly
 */

header('Content-Type: text/html; charset=UTF-8');

$coreDir = realpath(__DIR__ . '/../gts_core');
$publicDir = __DIR__;
$envFile = $coreDir ? $coreDir . '/.env' : null;

// قراءة ملف .env بشكل دقيق
function parseEnvCustom($path) {
    if (!file_exists($path)) return [];
    $env = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            $env[$name] = $value;
        }
    }
    return $env;
}

$action = $_GET['action'] ?? null;
$message = null;
$messageType = 'info';

if ($action === 'symlink') {
    if (!$coreDir) {
        $message = "خطأ: مجلد gts_core غير موجود!";
        $messageType = 'danger';
    } else {
        $target = $coreDir . '/storage/app/public';
        $link = $publicDir . '/storage';

        if (file_exists($link)) {
            $message = "رابط التخزين (storage) موجود بالفعل مسبقاً!";
            $messageType = 'warning';
        } else {
            if (!file_exists($target)) {
                @mkdir($target, 0775, true);
            }
            if (@symlink($target, $link)) {
                $message = "تم إنشاء رابط التخزين بنجاح (Storage Symlink Created)! ستظهر الشعارات والملفات الآن بشكل سليم.";
                $messageType = 'success';
            } else {
                $message = "تعذر إنشاء symlink برمجياً. قد تحتاج لتفعيله عبر SSH أو لوحة التحكم.";
                $messageType = 'danger';
            }
        }
    }
} elseif ($action === 'clean_cache') {
    if ($coreDir) {
        // حذف ملفات الـ Views المترجمة القديمة
        $views = glob($coreDir . '/storage/framework/views/*.php');
        foreach ($views as $v) { @unlink($v); }

        // حذف كاش bootstrap
        $bootCache = glob($coreDir . '/bootstrap/cache/*.php');
        foreach ($bootCache as $b) {
            if (!str_contains($b, '.gitignore')) { @unlink($b); }
        }

        // استدعاء artisan optimize:clear
        if (file_exists($coreDir . '/vendor/autoload.php')) {
            try {
                require $coreDir . '/vendor/autoload.php';
                $app = require_once $coreDir . '/bootstrap/app.php';
                $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
                $kernel->call('optimize:clear');
            } catch (\Throwable $e) {}
        }

        $message = "تم تنظيف كافة ملفات الكاش والـ Views القديمة بنجاح! المنصة الآن جاهزة للترجمة الجديدة على السيرفر.";
        $messageType = 'success';
    }
} elseif ($action === 'test_db') {
    if (!$coreDir || !file_exists($envFile)) {
        $message = "ملف .env غير موجود داخل gts_core! يرجى إنشاؤه أولاً.";
        $messageType = 'danger';
    } else {
        $env = parseEnvCustom($envFile);
        $host = $env['DB_HOST'] ?? 'localhost';
        $db = $env['DB_DATABASE'] ?? '';
        $user = $env['DB_USERNAME'] ?? '';
        $pass = $env['DB_PASSWORD'] ?? '';

        try {
            $pdo = new PDO("mysql:host={$host};dbname={$db};charset=utf8mb4", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            $stmt = $pdo->query("SHOW TABLES");
            $tablesCount = count($stmt->fetchAll());
            $message = "اتصال قاعدة البيانات ناجح 100%! تم العثور على ($tablesCount) جدول داخل قاعدة البيانات `{$db}`.";
            $messageType = 'success';
        } catch (\Exception $e) {
            $message = "فشل الاتصال بقاعدة البيانات (المستخدم: '{$user}'، السيرفر: '{$host}'): " . htmlspecialchars($e->getMessage());
            $messageType = 'danger';
        }
    }
} elseif ($action === 'delete_self') {
    @unlink(__FILE__);
    echo "<h2 style='font-family:sans-serif;text-align:center;margin-top:50px;color:green;'>تم حذف ملف الإعداد الذاتي بنجاح لأمان الموقع.</h2><p style='text-align:center;'><a href='/'>الانتقال إلى الرئيسية</a></p>";
    exit;
}

// قراءة آخر خطأ من سجلات لارافيل
$lastError = null;
if ($coreDir) {
    $logFiles = glob($coreDir . '/storage/logs/laravel*.log');
    if (!empty($logFiles)) {
        rsort($logFiles);
        $latestLog = $logFiles[0];
        if (file_exists($latestLog) && filesize($latestLog) > 0) {
            $lines = file($latestLog);
            $recentLines = array_slice($lines, -25);
            $lastError = implode('', $recentLines);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تهيئة وتشخيص منصة الخريجين - جامعة طرابلس</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0d3882;
            --accent: #EECA3E;
            --border: rgba(255, 255, 255, 0.12);
            --text: #f8fafc;
        }
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #070d1e 0%, #0d1e44 100%);
            color: var(--text);
            margin: 0;
            padding: 30px 15px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 780px;
            width: 100%;
            background: rgba(13, 30, 68, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 20px;
        }
        .header h1 { color: #fff; margin: 0 0 10px; font-size: 24px; font-weight: 800; }
        .header p { color: var(--accent); margin: 0; font-size: 15px; font-weight: 600; }
        .status-box {
            background: rgba(0, 0, 0, 0.35);
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .status-item:last-child { border-bottom: none; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 700; }
        .badge-success { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid #10b981; }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid #ef4444; }
        
        .alert { padding: 15px; border-radius: 12px; margin-bottom: 20px; font-weight: 600; font-size: 14px; }
        .alert-success { background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #a7f3d0; }
        .alert-danger { background: rgba(239, 68, 68, 0.15); border: 1px solid #ef4444; color: #fca5a5; }
        .alert-warning { background: rgba(245, 158, 11, 0.15); border: 1px solid #f59e0b; color: #fde68a; }

        .actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }
        .btn {
            display: block;
            text-align: center;
            padding: 14px 18px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            font-size: 14px;
        }
        .btn-primary { background: linear-gradient(135deg, #0d3882, #1a56be); color: #fff; border: 1px solid rgba(255,255,255,0.2); }
        .btn-primary:hover { box-shadow: 0 0 20px rgba(26, 86, 190, 0.5); transform: translateY(-2px); }
        .btn-accent { background: linear-gradient(135deg, #eeca3e, #f59e0b); color: #070d1e; }
        .btn-accent:hover { box-shadow: 0 0 20px rgba(238, 202, 62, 0.4); transform: translateY(-2px); }
        .btn-danger { background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid #ef4444; grid-column: span 2; }
        .btn-danger:hover { background: #ef4444; color: #fff; }
        
        .log-box {
            background: #000;
            border: 1px solid rgba(239, 68, 68, 0.4);
            border-radius: 8px;
            padding: 12px;
            font-family: monospace;
            font-size: 11px;
            direction: ltr;
            text-align: left;
            color: #fca5a5;
            max-height: 220px;
            overflow-y: auto;
            white-space: pre-wrap;
            margin-top: 15px;
        }
        .footer-link { text-align: center; margin-top: 15px; }
        .footer-link a { color: #93c5fd; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>تهيئة وتشخيص منصة الخريجين</h1>
            <p>مكتب تدريب الخريجين - جامعة طرابلس (gto.com.ly)</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="status-box">
            <div class="status-item">
                <span>إصدار PHP الحالي:</span>
                <span class="badge badge-success">PHP <?= PHP_VERSION ?></span>
            </div>
            <div class="status-item">
                <span>مجلد كود المنصة (gts_core):</span>
                <span class="badge <?= $coreDir ? 'badge-success' : 'badge-danger' ?>">
                    <?= $coreDir ? 'موجود ومتصل' : 'غير موجود في المسار العلوي' ?>
                </span>
            </div>
            <div class="status-item">
                <span>ملف الإعدادات (.env):</span>
                <span class="badge <?= ($coreDir && file_exists($envFile)) ? 'badge-success' : 'badge-danger' ?>">
                    <?= ($coreDir && file_exists($envFile)) ? 'تم ضبطه' : 'مفقود' ?>
                </span>
            </div>
            <div class="status-item">
                <span>رابط ملفات التخزين (Storage Link):</span>
                <span class="badge <?= file_exists($publicDir . '/storage') ? 'badge-success' : 'badge-warning' ?>">
                    <?= file_exists($publicDir . '/storage') ? 'مفعل وجاهز' : 'غير مفعل بعد' ?>
                </span>
            </div>
        </div>

        <div class="actions-grid">
            <a href="?action=test_db" class="btn btn-primary">🗄️ 1. فحص الاتصال بقاعدة البيانات</a>
            <a href="?action=clean_cache" class="btn btn-accent">🧹 2. تنظيف ملفات الكاش القديمة (هام)</a>
            <a href="?action=symlink" class="btn btn-accent">🔗 3. إنشاء رابط التخزين (Storage Link)</a>
            <a href="/" target="_blank" class="btn btn-primary">🚀 4. فتح الصفحة الرئيسية للمنصة (gto.com.ly)</a>
            <a href="?action=delete_self" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا الملف للأمان؟')" class="btn btn-danger">🔒 حذف هذا الملف نهائياً بعد انتهاء الإعداد</a>
        </div>

        <?php if ($lastError): ?>
            <h4 style="margin: 20px 0 5px; color: #f87171; font-size: 13px;">آخر تفاصيل مسجلة في ملف الأخطاء (laravel.log):</h4>
            <div class="log-box"><?= htmlspecialchars($lastError) ?></div>
        <?php endif; ?>

        <div class="footer-link">
            <a href="/">الانتقال إلى الواجهة الرئيسية للمنصة &larr;</a>
        </div>
    </div>
</body>
</html>
