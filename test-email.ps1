# سكريبت اختبار سريع لنظام الإشعارات عبر البريد الإلكتروني
# Quick Email Notification Test Script

Write-Host "================================" -ForegroundColor Cyan
Write-Host "  اختبار الإشعارات البريدية" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# التحقق من إعدادات البريد في .env
Write-Host "[1/4] فحص إعدادات البريد الإلكتروني..." -ForegroundColor Yellow

$envFile = Get-Content .env -ErrorAction SilentlyContinue

if (-not $envFile) {
    Write-Host "❌ ملف .env غير موجود!" -ForegroundColor Red
    exit 1
}

$mailHost = ($envFile | Select-String "MAIL_HOST=").ToString().Split("=")[1]
$mailPort = ($envFile | Select-String "MAIL_PORT=").ToString().Split("=")[1]
$mailUsername = ($envFile | Select-String "MAIL_USERNAME=").ToString().Split("=")[1]
$mailEncryption = ($envFile | Select-String "MAIL_ENCRYPTION=").ToString().Split("=")[1]

Write-Host "  📧 MAIL_HOST: $mailHost" -ForegroundColor Gray
Write-Host "  🔌 MAIL_PORT: $mailPort" -ForegroundColor Gray
Write-Host "  👤 MAIL_USERNAME: $mailUsername" -ForegroundColor Gray
Write-Host "  🔐 MAIL_ENCRYPTION: $mailEncryption" -ForegroundColor Gray
Write-Host ""

# التحقق من الإعدادات الموصى بها لـ Gmail
if ($mailHost -eq "smtp.gmail.com") {
    Write-Host "[تحذير] إعدادات Gmail:" -ForegroundColor Yellow
    
    if ($mailPort -ne "587") {
        Write-Host "  ⚠️  MAIL_PORT يجب أن يكون 587 (حالياً: $mailPort)" -ForegroundColor Red
    } else {
        Write-Host "  ✅ MAIL_PORT صحيح" -ForegroundColor Green
    }
    
    if ($mailEncryption -ne "tls") {
        Write-Host "  ⚠️  MAIL_ENCRYPTION يجب أن يكون tls (حالياً: $mailEncryption)" -ForegroundColor Red
    } else {
        Write-Host "  ✅ MAIL_ENCRYPTION صحيح" -ForegroundColor Green
    }
    
    Write-Host ""
    Write-Host "  💡 تذكير: يجب استخدام App Password من Google" -ForegroundColor Cyan
    Write-Host "     https://myaccount.google.com/apppasswords" -ForegroundColor Gray
    Write-Host ""
}

# التحقق من آخر الإشعارات
Write-Host "[2/4] فحص آخر الإشعارات في قاعدة البيانات..." -ForegroundColor Yellow

$checkLastNotifications = @"
SELECT 
    id, 
    title, 
    user_id, 
    CASE WHEN sent_at IS NOT NULL THEN 'نعم' ELSE 'لا' END as 'تم الإرسال',
    created_at 
FROM notifications 
ORDER BY created_at DESC 
LIMIT 5;
"@

Write-Host "  يمكنك تشغيل هذا الاستعلام في phpMyAdmin:" -ForegroundColor Gray
Write-Host $checkLastNotifications -ForegroundColor DarkGray
Write-Host ""

# التحقق من ملف اللوج
Write-Host "[3/4] فحص آخر الأخطاء في ملف اللوج..." -ForegroundColor Yellow

$logFile = "storage\logs\laravel.log"

if (Test-Path $logFile) {
    $lastErrors = Get-Content $logFile -Tail 20 | Select-String -Pattern "Failed to send email|error|exception" -CaseSensitive:$false
    
    if ($lastErrors) {
        Write-Host "  ⚠️  تم العثور على أخطاء محتملة:" -ForegroundColor Red
        $lastErrors | ForEach-Object { Write-Host "    $_" -ForegroundColor DarkRed }
        Write-Host ""
    } else {
        Write-Host "  ✅ لا توجد أخطاء واضحة" -ForegroundColor Green
        Write-Host ""
    }
} else {
    Write-Host "  ℹ️  ملف اللوج غير موجود بعد" -ForegroundColor Gray
    Write-Host ""
}

# تشغيل الاختبار
Write-Host "[4/4] تشغيل اختبار إرسال البريد..." -ForegroundColor Yellow
Write-Host ""

$runTest = Read-Host "هل تريد تشغيل اختبار إرسال البريد الآن؟ (y/n)"

if ($runTest -eq "y" -or $runTest -eq "Y" -or $runTest -eq "نعم") {
    Write-Host ""
    Write-Host "🚀 تشغيل أمر الاختبار..." -ForegroundColor Green
    Write-Host ""
    
    php artisan test:email-notification
    
    Write-Host ""
    Write-Host "✅ انتهى الاختبار!" -ForegroundColor Green
} else {
    Write-Host ""
    Write-Host "يمكنك تشغيل الاختبار يدوياً لاحقاً:" -ForegroundColor Cyan
    Write-Host "  php artisan test:email-notification" -ForegroundColor Gray
}

Write-Host ""
Write-Host "================================" -ForegroundColor Cyan
Write-Host "📚 للمزيد من المعلومات:" -ForegroundColor Cyan
Write-Host "  - دليل الاختبار: .agent\workflows\test-email-notifications.md" -ForegroundColor Gray
Write-Host "  - دليل الإصلاح: .agent\EMAIL_TROUBLESHOOTING.md" -ForegroundColor Gray
Write-Host "  - ملخص التحديثات: .agent\FIX_SUMMARY_EMAIL_NOTIFICATION.md" -ForegroundColor Gray
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""
