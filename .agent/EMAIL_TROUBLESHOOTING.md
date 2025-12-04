# دليل اختبار وإصلاح الإشعارات عبر البريد الإلكتروني
## Email Notifications Testing & Troubleshooting Guide

## 🔧 الإصلاحات التي تمت

### ✅ 1. إصلاح الكود
تم إضافة `'send_email' => true` في دالتي:
- `approveApplication()` - السطر 243
- `rejectApplication()` - السطر 264

في الملف: `app/Http/Controllers/TrainingController.php`

هذا يضمن أن الإشعار سيتم إرساله عبر البريد الإلكتروني عند قبول أو رفض طلب التدريب.

---

## ⚠️ المشكلة الأساسية: إعدادات Gmail

### المشكلة:
إعدادات البريد الإلكتروني في ملف `.env` تستخدم كلمة مرور عادية، لكن **Gmail لا يقبل كلمات المرور العادية** منذ 2022.

### الحل:
يجب الحصول على **App Password** (كلمة مرور تطبيقات) من حساب Google.

---

## 📝 خطوات الحصول على App Password من Gmail

### الخطوة 1️⃣: تفعيل التحقق بخطوتين (2FA)
1. اذهب إلى: https://myaccount.google.com/security
2. ابحث عن "التحقق بخطوتين" أو "2-Step Verification"
3. قم بتفعيله إذا لم يكن مفعلاً

### الخطوة 2️⃣: إنشاء App Password
1. بعد تفعيل التحقق بخطوتين، اذهب إلى: https://myaccount.google.com/apppasswords
2. أو ابحث عن "App passwords" في إعدادات Google
3. اختر "Other (Custom name)"
4. أدخل اسماً مثل: "Graduate Training System"
5. اضغط "Generate"
6. **انسخ كلمة المرور المكونة من 16 حرف** (ستظهر مرة واحدة فقط!)

### الخطوة 3️⃣: تحديث ملف .env
افتح ملف `.env` وغير السطر 35:

```env
# من:
MAIL_PASSWORD=Moneebmohamed@20

# إلى:
MAIL_PASSWORD=xxxx xxxx xxxx xxxx  # كلمة مرور التطبيق (16 حرف)
```

**ملاحظة:** قد تحتوي كلمة المرور على مسافات، احتفظ بها كما هي.

### الخطوة 4️⃣: التأكد من الإعدادات الأخرى

تأكد من الإعدادات التالية في ملف `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587                          # ⚠️ غيّر من 465 إلى 587
MAIL_USERNAME=moneeb20mohamed@gmail.com
MAIL_PASSWORD=xxxx xxxx xxxx xxxx      # App Password
MAIL_ENCRYPTION=tls                     # ⚠️ غيّر من ssl إلى tls
MAIL_FROM_ADDRESS="moneeb20mohamed@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### الخطوة 5️⃣: إعادة تشغيل السيرفر
بعد تعديل ملف `.env`:

```bash
# أوقف السيرفر بالضغط على Ctrl+C
# ثم شغله مرة أخرى:
php artisan serve
```

---

## 🧪 اختبار إرسال البريد الإلكتروني

### الطريقة 1: أمر الاختبار (الأفضل)
```bash
php artisan test:email-notification
```

سيطلب منك:
1. إدخال البريد الإلكتروني للمستلم
2. اختيار نوع الاختبار (اختر "Simple direct email test")

### الطريقة 2: اختبار عملي
1. سجل دخول كمنسق تدريب أو admin
2. اذهب إلى صفحة طلبات التدريب
3. قم بقبول أو رفض أي طلب تدريب
4. تحقق من البريد الإلكتروني للخريج

---

## 🔍 استكشاف الأخطاء

### خطأ 1: "Failed to authenticate"
**السبب:** كلمة المرور خاطئة أو لم تستخدم App Password  
**الحل:** اتبع الخطوات أعلاه للحصول على App Password

### خطأ 2: "Could not connect to SMTP host"
**السبب:** رقم المنفذ أو نوع التشفير خاطئ  
**الحل:** استخدم `MAIL_PORT=587` و `MAIL_ENCRYPTION=tls`

### خطأ 3: "Connection timeout"
**السبب:** الفايروول أو جدار الحماية يمنع الاتصال  
**الحل:** 
- تأكد من اتصالك بالإنترنت
- تحقق من إعدادات الفايروول
- جرب استخدام VPN إذا كان Gmail محجوب

### خطأ 4: لا توجد أخطاء لكن البريد لم يصل
**الأسباب المحتملة:**
1. البريد في مجلد Spam (البريد المزعج)
2. عنوان البريد الإلكتروني للخريج غير صحيح
3. الطلبات في قائمة الانتظار (Queue) ولم تُرسل بعد

**الحل:**
1. تحقق من مجلد Spam
2. تحقق من البريد الإلكتروني للمستخدم في قاعدة البيانات
3. تحقق من الـ Log:
```bash
# افتح ملف:
storage/logs/laravel.log
```

---

## 📊 التحقق من سجلات النظام

### عرض آخر 50 سطر من Log:
```bash
Get-Content storage/logs/laravel.log -Tail 50
```

### البحث عن أخطاء البريد:
```bash
Select-String -Path "storage/logs/laravel.log" -Pattern "Failed to send email"
```

---

## 🗄️ التحقق من قاعدة البيانات

### فحص الإشعارات المُرسلة:
```sql
SELECT id, title, message, user_id, sent_at, created_at 
FROM notifications 
WHERE sent_at IS NOT NULL 
ORDER BY created_at DESC 
LIMIT 10;
```

### فحص الإشعارات التي لم تُرسل:
```sql
SELECT id, title, message, user_id, created_at 
FROM notifications 
WHERE sent_at IS NULL AND created_at > NOW() - INTERVAL 1 DAY
ORDER BY created_at DESC;
```

---

## 🔄 إعادة إرسال الإشعارات يدوياً

إذا كانت هناك إشعارات لم تُرسل، يمكنك إعادة إرسالها:

```bash
php artisan tinker
```

ثم:
```php
use App\Services\NotificationService;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;

// احصل على إشعار معين
$notification = Notification::find(ID); // غيّر ID برقم الإشعار

// أرسل البريد الإلكتروني
if ($notification && $notification->user && $notification->user->email) {
    Mail::to($notification->user->email)->send(new NotificationMail($notification));
    $notification->markAsSent();
    echo "تم إرسال البريد بنجاح!\n";
}
```

---

## ✅ قائمة التحقق السريعة

- [ ] تم تفعيل التحقق بخطوتين على Gmail
- [ ] تم إنشاء App Password من Google
- [ ] تم تحديث `MAIL_PASSWORD` في ملف `.env`
- [ ] تم تغيير `MAIL_PORT` إلى `587`
- [ ] تم تغيير `MAIL_ENCRYPTION` إلى `tls`
- [ ] تم إعادة تشغيل السيرفر
- [ ] تم اختبار إرسال البريد باستخدام أمر الاختبار
- [ ] تم التحقق من مجلد Spam
- [ ] تم فحص ملف `laravel.log` للأخطاء

---

## 🎯 ملخص التغييرات

### في الكود:
✅ تم إضافة `'send_email' => true` في دالتي القبول والرفض

### في الإعدادات (مطلوب من المستخدم):
⚠️ **يجب** تحديث إعدادات Gmail في `.env`:
- الحصول على App Password
- تحديث MAIL_PASSWORD
- تغيير PORT إلى 587
- تغيير ENCRYPTION إلى tls

---

## 📞 إذا استمرت المشكلة

إذا اتبعت جميع الخطوات ولا زالت المشكلة موجودة:

1. جرب استخدام خدمة بريد أخرى (مثل Mailtrap للتطوير)
2. تأكد من أن حساب Gmail غير محظور
3. راجع ملف `app/Mail/NotificationMail.php` للتأكد من صحة القالب
4. تحقق من أن جدول `users` يحتوي على عناوين بريد إلكتروني صحيحة

---

## 🔧 بديل: استخدام Mailtrap للتطوير

إذا أردت تجربة بدون استخدام Gmail:

1. سجل في: https://mailtrap.io (مجاني)
2. احصل على إعدادات SMTP
3. حدّث `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="test@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

ميزة Mailtrap: يلتقط جميع الرسائل ولا يرسلها فعلياً، مفيد للتطوير والاختبار.
