# دليل إرسال الإشعارات عبر البريد الإلكتروني
## Email Notifications Guide

## 📋 نظرة عامة

يتم إرسال الإشعارات عبر البريد الإلكتروني تلقائياً في النظام عند حدوث أحداث محددة. يتم التحكم في إرسال البريد الإلكتروني من خلال خاصية `send_email` في البيانات المرسلة لخدمة الإشعارات.

---

## 📧 متى يتم إرسال الإشعارات عبر البريد الإلكتروني؟

### 1️⃣ **إضافة فرصة تدريب جديدة** (`notifyNewTraining`)
- **المتلقين:** جميع الخريجين (Graduate)
- **يتم الإرسال عند:** إضافة فرصة تدريب جديدة من قبل المسؤولين
- **الموقع في الكود:** `app/Services/NotificationService.php` - السطر 81-111
- **المُستدعى من:** `app/Http/Controllers/TrainingController.php` - السطر 73

**تفاصيل الإشعار:**
```php
العنوان: "تدريب جديد متاح"
الرسالة: "تم إضافة تدريب جديد: [اسم التدريب]"
النوع: info
```

---

### 2️⃣ **إضافة فرصة عمل جديدة** (`notifyNewJobOpportunity`)
- **المتلقين:** جميع الخريجين (Graduate)
- **يتم الإرسال عند:** إضافة فرصة عمل جديدة في النظام
- **الموقع في الكود:** `app/Services/NotificationService.php` - السطر 116-140
- **المُستدعى من:** `app/Http/Controllers/JobOpportunityController.php` - السطر 247

**تفاصيل الإشعار:**
```php
العنوان: "فرصة عمل جديدة"
الرسالة: "تم إضافة فرصة عمل جديدة: [عنوان الوظيفة]"
النوع: success
```

---

### 3️⃣ **الموافقة على طلب التدريب** (`notifyTrainingApplicationApproved`)
- **المتلقي:** الخريج صاحب الطلب
- **يتم الإرسال عند:** موافقة المسؤول على طلب التدريب المقدم من الخريج
- **الموقع في الكود:** `app/Services/NotificationService.php` - السطر 163-174

**تفاصيل الإشعار:**
```php
العنوان: "تمت الموافقة على طلب التدريب"
الرسالة: "تمت الموافقة على طلبك للتدريب: [اسم التدريب]"
النوع: success
```

---

### 4️⃣ **رفض طلب التدريب** (`notifyTrainingApplicationRejected`)
- **المتلقي:** الخريج صاحب الطلب
- **يتم الإرسال عند:** رفض المسؤول لطلب التدريب المقدم من الخريج
- **الموقع في الكود:** `app/Services/NotificationService.php` - السطر 179-190

**تفاصيل الإشعار:**
```php
العنوان: "تم رفض طلب التدريب"
الرسالة: "تم رفض طلبك للتدريب: [اسم التدريب]"
النوع: warning
```

---

### 5️⃣ **إرسال تذكير** (`sendReminder`)
- **المتلقي:** المستخدم المحدد
- **يتم الإرسال عند:** إرسال تذكير يدوي من النظام
- **الموقع في الكود:** `app/Services/NotificationService.php` - السطر 195-200

**تفاصيل الإشعار:**
```php
العنوان: [حسب المحتوى]
الرسالة: [حسب المحتوى]
النوع: warning
```

---

### 6️⃣ **ترشيح خريج لوظيفة** (`notifyJobNomination`)
- **المتلقي:** الخريج المرشح
- **يتم الإرسال عند:** ترشيح خريج لوظيفة من قبل مسؤول الإرشاد المهني أو مسؤول الشراكات
- **الموقع في الكود:** `app/Services/NotificationService.php` - السطر 284-311

**تفاصيل الإشعار:**
```php
العنوان: "تم ترشيحك لوظيفة"
الرسالة: "تم ترشيحك للوظيفة: [عنوان الوظيفة] من قبل [اسم المرشح]"
النوع: success
```

---

### 7️⃣ **تحديث حالة الترشيح** (`notifyNominationStatusUpdate`)
- **المتلقي:** الخريج المرشح
- **يتم الإرسال عند:** تحديث حالة الترشيح (مقبول، مرفوض، تم تحديد موعد مقابلة)
- **الموقع في الكود:** `app/Services/NotificationService.php` - السطر 316-335

**تفاصيل الإشعار:**
```php
العنوان: "تحديث حالة الترشيح"  
الرسالة: "تم تحديث حالة ترشيحك للوظيفة: [عنوان الوظيفة] إلى: [الحالة الجديدة]"
النوع: حسب الحالة (success للقبول، warning للرفض، info لغيرها)
```

**الحالات المتاحة:**
- `pending` - قيد المراجعة
- `approved` - مقبول
- `rejected` - مرفوض
- `interview` - تم تحديد موعد مقابلة

---

## ⚙️ كيفية عمل نظام الإشعارات عبر البريد الإلكتروني

### **الآلية:**

1. **إنشاء الإشعار:**
   - يتم إنشاء سجل في جدول `notifications`
   - إذا كان المعامل `send_email` = `true`، يتم استدعاء `sendEmailNotification()`

2. **إرسال البريد الإلكتروني:**
   ```php
   // من app/Services/NotificationService.php - السطر 213-223
   private function sendEmailNotification(Notification $notification): void
   {
       try {
           if ($notification->user && $notification->user->email) {
               Mail::to($notification->user->email)->send(new NotificationMail($notification));
               $notification->markAsSent();
           }
       } catch (\Exception $e) {
           \Log::error('Failed to send email notification: ' . $e->getMessage());
       }
   }
   ```

3. **تسجيل الإرسال:**
   - يتم تحديث حقل `sent_at` في جدول الإشعارات عند نجاح الإرسال

---

## 📂 الملفات الأساسية

| الملف | الوصف |
|-------|-------|
| `app/Services/NotificationService.php` | خدمة إدارة الإشعارات الرئيسية |
| `app/Models/Notification.php` | نموذج قاعدة البيانات للإشعارات |
| `app/Mail/NotificationMail.php` | قالب البريد الإلكتروني للإشعارات |
| `app/Http/Controllers/TrainingController.php` | المتحكم الخاص بإدارة التدريبات |
| `app/Http/Controllers/JobOpportunityController.php` | المتحكم الخاص بإدارة فرص العمل |

---

## 🔍 ملاحظات مهمة

### ✅ **الإشعارات التي تُرسل تلقائياً عبر البريد الإلكتروني:**
- ✉️ إضافة فرصة تدريب جديدة (للخريجين فقط)
- ✉️ إضافة فرصة عمل جديدة (للخريجين فقط)  
- ✉️ الموافقة/رفض طلب التدريب
- ✉️ ترشيح لوظيفة
- ✉️ تحديث حالة الترشيح
- ✉️ التذكيرات

### ❌ **الإشعارات التي لا تُرسل عبر البريد الإلكتروني:**
- ❌ الإشعارات الداخلية للمسؤولين (career_guidance_officer, partnership_officer, admin, etc.)
- ❌ إشعارات تقديم طلب توظيف (داخلية فقط)
- ❌ إشعارات الإجراءات النظامية العامة (ما لم يتم تحديد `send_email` صراحة)

---

## 🧪 اختبار نظام البريد الإلكتروني

يوجد أمر خاص لاختبار نظام الإشعارات:

```bash
php artisan test:email-notification
```

موقع الأمر: `app/Console/Commands/TestEmailNotification.php`

---

## 🛠️ إعدادات البريد الإلكتروني

تأكد من إعداد البريد الإلكتروني في ملف `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 📊 جدول قاعدة البيانات

**جدول `notifications`:**
```
- id
- title (عنوان الإشعار)
- message (نص الإشعار)
- type (info, success, warning, danger)
- user_id (المستخدم المستلم)
- sender_id (المرسل - اختياري)
- model_type (نوع النموذج المرتبط)
- model_id (معرف النموذج المرتبط)
- data (بيانات إضافية - JSON)
- is_read (هل تمت القراءة)
- read_at (وقت القراءة)
- sent_at (وقت إرسال البريد الإلكتروني) ✉️
- created_at
- updated_at
```

---

## 📝 كيفية إضافة إشعار جديد بإرسال بريد إلكتروني

```php
use App\Services\NotificationService;

// في الـ Controller
public function __construct(
    private NotificationService $notificationService
) {}

// إرسال إشعار مع بريد إلكتروني
$this->notificationService->sendToUser(
    $user,
    'عنوان الإشعار',
    'نص الإشعار',
    'info', // النوع: info, success, warning, danger
    [
        'send_email' => true, // ✉️ لإرسال بريد إلكتروني
        'model_type' => YourModel::class,
        'model_id' => $model->id,
    ]
);
```

---

## 🎯 الخلاصة

يتم إرسال الإشعارات عبر البريد الإلكتروني في **7 حالات رئيسية** تهم **الخريجين بشكل أساسي**، وذلك لضمان وصول المعلومات المهمة إليهم حتى لو لم يكونوا متصلين بالنظام.

للمزيد من المعلومات أو لاختبار النظام، راجع:
- الملف: `app/Services/NotificationService.php`
- الأمر: `php artisan test:email-notification`
- الدليل: `.agent/workflows/test-email-notifications.md`
