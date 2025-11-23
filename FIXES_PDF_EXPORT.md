# حل مشكلة تصدير تقارير الإرشاد المهني إلى PDF

## المشكلة
كان هناك خطأ عند محاولة تصدير التقارير:
```
fopen(C:\Users\Sohib\graduate_training_system\storage\fonts/font_awesome_6_brands_normal_...): Failed to open stream: No such file or directory
```

## الحلول المطبقة

### 1. إنشاء مجلد الخطوط
تم إنشاء المجلد `storage/fonts` لتخزين ملفات الخطوط التي تحتاجها مكتبة dompdf.

```powershell
New-Item -ItemType Directory -Force -Path "storage\fonts"
```

### 2. تحديث ملف PDF Template
تم تحديث ملف `resources/views/career-guidance/reports-pdf.blade.php`:
- إزالة الاعتماد على Font Awesome (التي تسبب المشكلة)
- استخدام خطوط آمنة مثل DejaVu Sans
- إزالة الروابط الخارجية (CDN)
- استخدام HTML/CSS بسيط ومباشر

### 3. إنشاء ملف إعدادات dompdf
تم إنشاء `config/dompdf.php` مع الإعدادات المناسبة:
- تحديد مسار مجلد الخطوط
- تعطيل الخطوط الخارجية
- تفعيل HTML5 parser

### 4. تحديث Controller
تم تحديث `CareerGuidanceController.php`:
- إضافة `use Barryvdh\DomPDF\Facade\Pdf as PDF;`
- تحسين معالجة الأخطاء
- إضافة إعدادات PDF مناسبة

## كيفية الاستخدام

1. انتقل إلى: **إدارة التقييم والمتابعة** → **تقارير الإرشاد المهني**
2. اضغط على زر **تصدير التقارير**
3. سيتم تنزيل ملف PDF بنجاح

## ملاحظات مهمة

- تأكد من وجود بيانات خريجين قبل التصدير
- المجلد `storage/fonts` يجب أن يكون قابلاً للكتابة
- في حالة ظهور أخطاء، تحقق من ملف `storage/logs/laravel.log`

## الملفات المعدلة

1. `storage/fonts/` - مجلد جديد
2. `config/dompdf.php` - ملف جديد
3. `resources/views/career-guidance/reports-pdf.blade.php` - محدث
4. `app/Http/Controllers/CareerGuidanceController.php` - محدث
