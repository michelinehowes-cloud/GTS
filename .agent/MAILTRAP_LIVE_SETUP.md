# إعداد Mailtrap Live (وضع الإنتاج)
## دليل الحصول على API Token الصحيح

> ⚠️ **تنبيه:** أنت تستخدم Mailtrap Live - هذا سيرسل رسائل حقيقية للمستخدمين!

---

## 📋 الخطوات للحصول على API Token

### الخطوة 1️⃣: تسجيل الدخول إلى Mailtrap

1. اذهب إلى: https://mailtrap.io
2. سجل دخول إلى حسابك

---

### الخطوة 2️⃣: الذهاب إلى Email Sending (Live)

1. من القائمة الجانبية، اختر **"Email Sending"** أو **"Sending Domains"**
2. **ليس** "Email Testing" (هذا للاختبار فقط)

---

### الخطوة 3️⃣: إضافة Domain (إذا لم تكن قد أضفته)

إذا كانت هذه أول مرة:

1. اضغط **"Add Domain"**
2. أدخل domain (مثل: `graduate-system.ly` أو أي domain تملكه)
3. إذا لم يكن لديك domain، يمكنك استخدام domain تجريبي مؤقتاً

**ملاحظة:** للإرسال الفعلي، ستحتاج إلى التحقق من الـ Domain (عبر DNS records)

---

### الخطوة 4️⃣: الحصول على API Token

بعد إضافة الـ Domain:

1. اذهب إلى **"SMTP/API Settings"** أو **"Settings"**
2. ابحث عن **"API Tokens"** أو **"SMTP Credentials"**
3. اضغط **"Generate New Token"** أو **"Create API Token"**
4. **انسخ الـ Token** (سيظهر مرة واحدة فقط!)

---

### الخطوة 5️⃣: تحديث ملف .env

افتح ملف `.env` وحدّث:

```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=api
MAIL_PASSWORD=your_actual_api_token_here    # ضع الـ Token هنا (بدون <>)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"  # استخدم domain حقيقي
MAIL_FROM_NAME="${APP_NAME}"
```

**مثال بقيم حقيقية:**
```env
MAIL_MAILER=smtp
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=api
MAIL_PASSWORD=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6    # مثال
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@graduate-system.ly"
MAIL_FROM_NAME="Graduate Training System"
```

---

### الخطوة 6️⃣: إعادة تشغيل السيرفر

```bash
# أوقف السيرفر (Ctrl+C)
php artisan serve
```

---

## ⚠️ تحذيرات مهمة

### 🔴 Mailtrap Live سيرسل رسائل حقيقية!

عند استخدام `live.smtp.mailtrap.io`:
- ✉️ **سيتم إرسال الرسائل فعلياً** إلى عناوين البريد الحقيقية
- 📊 لديك حد يومي للرسائل (حسب الخطة)
- 💰 قد تحتاج اشتراك مدفوع للحدود الأعلى

### 💡 هل أنت متأكد؟

**إذا كنت لا تزال في مرحلة التطوير والاختبار:**

✅ **الأفضل:** استخدم **Sandbox** بدلاً من Live:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io    # للتطوير
MAIL_PORT=2525                         # منفذ مختلف
MAIL_USERNAME=your_sandbox_username    # من Mailtrap Sandbox
MAIL_PASSWORD=your_sandbox_password    # من Mailtrap Sandbox
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="test@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**للحصول على بيانات Sandbox:**
1. في Mailtrap، اذهب إلى **"Email Testing"**
2. اختر أو أنشئ Inbox
3. اضغط **"Show Credentials"** → **"Laravel"**
4. انسخ الإعدادات

---

## 🧪 الاختبار

بعد التحديث:

```bash
# اختبار سريع:
php artisan test:email-notification

# أو:
.\test-email.ps1
```

### إذا كنت تستخدم Live:
- ✅ تحقق من البريد الإلكتروني **الحقيقي** للمستلم
- ⚠️ تأكد من أنك لا تُزعج مستخدمين حقيقيين بالاختبار

### إذا كنت تستخدم Sandbox:
- ✅ تحقق من Mailtrap Dashboard: https://mailtrap.io/inboxes
- 📧 لن تصل رسائل حقيقية (آمن للاختبار)

---

## 📊 متى تستخدم أيهما؟

| الوضع | متى تستخدمه | المنفذ | الرسائل |
|------|-------------|--------|---------|
| **Sandbox** | التطوير والاختبار | 2525 | وهمية (لا تُرسل) |
| **Live** | الإنتاج (للمستخدمين الحقيقيين) | 587 | حقيقية (تُرسل فعلاً) |

---

## ✅ الخطوات الموصى بها

### للتطوير (الآن):

1. **استخدم Sandbox:**
   ```env
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   ```

2. **احصل على بيانات Sandbox:**
   - Email Testing → Inbox → Show Credentials

3. **لا تحتاج API Token** في Sandbox (فقط username/password عاديين)

### للإنتاج (لاحقاً):

1. **استخدم Live:**
   ```env
   MAIL_HOST=live.smtp.mailtrap.io
   MAIL_PORT=587
   MAIL_USERNAME=api
   MAIL_PASSWORD=your_api_token
   ```

2. **تحقق من Domain** (DNS records)
3. **اختبر بحذر** على بيانات حقيقية

---

## 🔍 حل المشاكل

### المشكلة: "Authentication failed"

**السبب:** API Token خاطئ أو منتهي الصلاحية

**الحل:**
1. أعد إنشاء API Token من Mailtrap
2. تأكد من نسخه بالكامل (بدون مسافات)
3. ضعه في `.env` بدون `<>` أو علامات اقتباس

### المشكلة: "Domain not verified"

**السبب:** لم تتحقق من الـ Domain في Mailtrap

**الحل:**
1. اذهب إلى Sending Domains في Mailtrap
2. اتبع خطوات التحقق (إضافة DNS records)
3. أو استخدم Sandbox للتطوير (لا يحتاج تحقق)

---

## 🎯 التوصية النهائية

### إذا كنت في مرحلة التطوير:
```env
# استخدم Sandbox (أسهل وأأمن):
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxx  # من Sandbox Credentials
MAIL_PASSWORD=xxxx  # من Sandbox Credentials
```

### إذا كنت جاهزاً للإنتاج:
```env
# استخدم Live:
MAIL_HOST=live.smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=api
MAIL_PASSWORD=your_actual_api_token  # من API Tokens
```

---

## 📞 المساعدة

إذا واجهت أي مشكلة:

1. **تحقق من اللوج:**
   ```bash
   Get-Content storage/logs/laravel.log -Tail 50
   ```

2. **تحقق من Mailtrap Dashboard:**
   - Live: https://mailtrap.io/sending/domains
   - Sandbox: https://mailtrap.io/inboxes

3. **راجع وثائق Mailtrap:**
   https://help.mailtrap.io/

---

**الخلاصة:** استبدل `<YOUR_API_TOKEN>` بالـ Token الفعلي من Mailtrap، أو استخدم Sandbox للتطوير (أسهل)!
