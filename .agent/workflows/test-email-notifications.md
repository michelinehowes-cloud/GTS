---
description: دليل فحص واختبار نظام الإشعارات عبر البريد الإلكتروني
---

# دليل فحص نظام الإشعارات عبر البريد الإلكتروني

> ⚠️ **تحديث مهم (2025-12-03):**  
> تم إصلاح مشكلة عدم إرسال البريد الإلكتروني عند قبول/رفض طلبات التدريب.  
> **التغييرات:** تمت إضافة `'send_email' => true` في دالتي `approveApplication` و `rejectApplication`.  
> **المتطلب:** يجب تحديث إعدادات Gmail في ملف `.env` واستخدام **App Password** بدلاً من كلمة المرور العادية.  
> **راجع:** الملف `.agent/EMAIL_TROUBLESHOOTING.md` للتفاصيل الكاملة.

## 📋 نظرة عامة
هذا الدليل يوضح كيفية فحص واختبار نظام الإشعارات عبر البريد الإلكتروني في نظام التدريب والتوظيف للخريجين.

## 🔧 الإعدادات المطلوبة

### 1. إعدادات البريد الإلكتروني في `.env`

#### خيار 1: استخدام Mailpit (للتطوير المحلي - موصى به)
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@graduate-system.ly"
MAIL_FROM_NAME="${APP_NAME}"
```

**لتشغيل Mailpit:**
```bash
# تحميل Mailpit من: https://github.com/axllent/mailpit/releases
# أو استخدام Docker:
docker run -d --name=mailpit -p 8025:8025 -p 1025:1025 axllent/mailpit
```

**عرض الرسائل:** افتح المتصفح على `http://localhost:8025`

#### خيار 2: استخدام Log (للتطوير السريع)
```env
MAIL_MAILER=log
```
**عرض الرسائل:** افتح `storage/logs/laravel.log`

#### خيار 3: استخدام Gmail (للإنتاج)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**ملاحظة:** يجب إنشاء App Password من إعدادات حساب Google

## 🧪 طرق الاختبار

### الطريقة 1: الوضع التفاعلي (موصى به)
```bash
php artisan test:email-notification
```

سيعرض لك قائمة تفاعلية للاختيار من بينها:
- إرسال لمستخدم محدد (User ID)
- إرسال لبريد إلكتروني محدد
- إرسال لجميع المستخدمين
- عرض الإشعارات المرسلة
- اختبار إرسال بريد مباشر

### الطريقة 2: إرسال لمستخدم محدد
```bash
php artisan test:email-notification --user=1
```

### الطريقة 3: إرسال لبريد إلكتروني محدد
```bash
php artisan test:email-notification --email=test@example.com
```

### الطريقة 4: إرسال لجميع المستخدمين
```bash
php artisan test:email-notification --all
```

## 📊 فحص النتائج

### 1. التحقق من قاعدة البيانات
```bash
php artisan tinker
```

```php
// عرض آخر 5 إشعارات
\App\Models\Notification::latest()->take(5)->get(['id', 'title', 'user_id', 'sent_at', 'created_at']);

// عرض الإشعارات المرسلة عبر البريد
\App\Models\Notification::whereNotNull('sent_at')->count();

// عرض الإشعارات غير المقروءة
\App\Models\Notification::where('is_read', false)->count();
```

### 2. التحقق من Logs
```bash
# عرض آخر 50 سطر من ملف اللوج
tail -n 50 storage/logs/laravel.log

# أو على Windows:
Get-Content storage/logs/laravel.log -Tail 50
```

### 3. التحقق من Mailpit
افتح المتصفح على: `http://localhost:8025`

## 🔍 استكشاف الأخطاء

### المشكلة: "Connection refused"
**الحل:**
- تأكد من تشغيل Mailpit أو خادم SMTP
- تحقق من إعدادات `MAIL_HOST` و `MAIL_PORT`

### المشكلة: "Authentication failed"
**الحل:**
- تحقق من `MAIL_USERNAME` و `MAIL_PASSWORD`
- إذا كنت تستخدم Gmail، تأكد من استخدام App Password

### المشكلة: لا يتم إرسال الرسائل
**الحل:**
```bash
# مسح الكاش
php artisan config:clear
php artisan cache:clear

# إعادة تحميل الإعدادات
php artisan config:cache
```

### المشكلة: الرسائل تصل إلى Spam
**الحل:**
- استخدم بريد إلكتروني حقيقي في `MAIL_FROM_ADDRESS`
- أضف SPF و DKIM records في DNS
- استخدم خدمة بريد موثوقة (SendGrid, Mailgun, etc.)

## 📝 اختبار متقدم

### إرسال إشعار مخصص
```bash
php artisan tinker
```

```php
$user = \App\Models\User::first();
$service = app(\App\Services\NotificationService::class);

$service->sendToUser(
    $user,
    'عنوان الإشعار',
    'محتوى الإشعار التجريبي',
    'info',
    ['send_email' => true]
);
```

### فحص قالب البريد الإلكتروني
```bash
# عرض قالب البريد
cat resources/views/emails/notification.blade.php
```

## ✅ قائمة التحقق

- [ ] تم تكوين إعدادات البريد في `.env`
- [ ] تم تشغيل خادم البريد (Mailpit/SMTP)
- [ ] تم اختبار إرسال بريد بسيط
- [ ] تم التحقق من وصول الرسائل
- [ ] تم فحص قاعدة البيانات للإشعارات
- [ ] تم التحقق من تصميم قالب البريد
- [ ] تم اختبار الإشعارات لأدوار مختلفة

## 🎯 الخطوات التالية

1. **للتطوير:** استخدم Mailpit أو Log
2. **للإنتاج:** استخدم خدمة بريد احترافية (SendGrid, Amazon SES, Mailgun)
3. **للأمان:** استخدم متغيرات البيئة ولا تشارك بيانات الاعتماد
4. **للأداء:** استخدم Queue لإرسال الرسائل في الخلفية

## 📚 موارد إضافية

- [Laravel Mail Documentation](https://laravel.com/docs/mail)
- [Mailpit GitHub](https://github.com/axllent/mailpit)
- [Gmail App Passwords](https://support.google.com/accounts/answer/185833)
