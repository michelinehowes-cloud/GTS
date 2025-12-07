# دليل المكونات القابلة لإعادة الاستخدام

## نظرة عامة
هذا الدليل يوضح كيفية استخدام المكونات القابلة لإعادة الاستخدام في التطبيق.

## المكونات المتاحة

### 1. بطاقة الإحصائيات (Stat Card)

**الموقع:** `resources/views/components/stat-card.blade.php`

**الاستخدام:**
```blade
@include('components.stat-card', [
    'title' => 'إجمالي الخريجين',
    'value' => '1,234',
    'icon' => 'fas fa-user-graduate',
    'color' => 'primary',
    'trend' => '+12%',
    'trendDirection' => 'up',
    'link' => route('graduates.index')
])
```

**المعاملات:**
- `title` (مطلوب): عنوان البطاقة
- `value` (مطلوب): القيمة المعروضة
- `icon` (اختياري): أيقونة Font Awesome (افتراضي: `fas fa-chart-line`)
- `color` (اختياري): اللون (primary, success, warning, danger, info) (افتراضي: `primary`)
- `trend` (اختياري): نسبة التغيير (مثال: `+10%`)
- `trendDirection` (اختياري): اتجاه التغيير (up أو down)
- `link` (اختياري): رابط للانتقال عند النقر

---

### 2. التنقل الهرمي (Breadcrumbs)

**الموقع:** `resources/views/components/breadcrumbs.blade.php`

**الاستخدام:**
```blade
@include('components.breadcrumbs', [
    'items' => [
        ['title' => 'الرئيسية', 'url' => route('home')],
        ['title' => 'لوحة التحكم', 'url' => route('dashboard')],
        ['title' => 'الخريجين', 'url' => route('graduates.index')],
        ['title' => 'تفاصيل الخريج']
    ]
])
```

**المعاملات:**
- `items` (مطلوب): مصفوفة من العناصر، كل عنصر يحتوي على:
  - `title` (مطلوب): عنوان العنصر
  - `url` (اختياري): الرابط (إذا لم يتم تحديده، يعتبر العنصر نشطاً)

---

### 3. التنبيهات (Alert)

**الموقع:** `resources/views/components/alert.blade.php`

**الاستخدام:**
```blade
@include('components.alert', [
    'type' => 'success',
    'title' => 'تم بنجاح',
    'message' => 'تم حفظ البيانات بنجاح',
    'dismissible' => true
])
```

**المعاملات:**
- `type` (اختياري): نوع التنبيه (success, warning, danger, info) (افتراضي: `info`)
- `title` (اختياري): عنوان التنبيه
- `message` (مطلوب): نص التنبيه
- `dismissible` (اختياري): إمكانية إغلاق التنبيه (افتراضي: `false`)

---

### 4. حالة عدم وجود بيانات (Empty State)

**الموقع:** `resources/views/components/empty-state.blade.php`

**الاستخدام:**
```blade
@include('components.empty-state', [
    'icon' => 'fas fa-user-graduate',
    'title' => 'لا يوجد خريجين',
    'message' => 'لم يتم العثور على أي خريجين في النظام',
    'actionText' => 'إضافة خريج جديد',
    'actionUrl' => route('graduates.create')
])
```

**المعاملات:**
- `icon` (اختياري): أيقونة Font Awesome (افتراضي: `fas fa-inbox`)
- `title` (اختياري): عنوان الحالة (افتراضي: `لا توجد بيانات`)
- `message` (اختياري): رسالة توضيحية (افتراضي: `لم يتم العثور على أي سجلات`)
- `actionText` (اختياري): نص زر الإجراء
- `actionUrl` (اختياري): رابط زر الإجراء

---

### 5. التحميل (Loading)

**الموقع:** `resources/views/components/loading.blade.php`

**الاستخدام:**
```blade
{{-- Loading with overlay --}}
@include('components.loading', [
    'message' => 'جاري تحميل البيانات...',
    'overlay' => true
])

{{-- Simple loading --}}
@include('components.loading', [
    'message' => 'جاري التحميل...'
])
```

**المعاملات:**
- `message` (اختياري): رسالة التحميل (افتراضي: `جاري التحميل...`)
- `overlay` (اختياري): إنشاء overlay بملء الشاشة (افتراضي: `false`)

---

## الأنماط (Styles)

### الألوان المتاحة
- `primary`: الأزرق الجامعي (#1e3a8a)
- `success`: الأخضر (#10b981)
- `warning`: البرتقالي (#f59e0b)
- `danger`: الأحمر (#ef4444)
- `info`: الأزرق الفاتح (#3b82f6)

### الأيقونات
يستخدم التطبيق Font Awesome 6.0. يمكنك استخدام أي أيقونة من:
https://fontawesome.com/icons

### الأزرار

```blade
{{-- Primary Button --}}
<button class="btn btn-primary-modern">
    <i class="fas fa-save me-2"></i>
    حفظ
</button>

{{-- Success Button --}}
<button class="btn btn-success-modern">
    <i class="fas fa-check me-2"></i>
    تأكيد
</button>

{{-- Danger Button --}}
<button class="btn btn-danger-modern">
    <i class="fas fa-trash me-2"></i>
    حذف
</button>

{{-- Outline Button --}}
<button class="btn btn-outline-modern">
    <i class="fas fa-times me-2"></i>
    إلغاء
</button>
```

### البطاقات

```blade
{{-- Modern Card --}}
<div class="card-modern">
    <h3>عنوان البطاقة</h3>
    <p>محتوى البطاقة</p>
</div>
```

### النماذج

```blade
<form class="needs-validation" novalidate>
    <div class="form-group-modern">
        <label class="form-label-modern" for="name">الاسم</label>
        <input type="text" class="form-control-modern" id="name" name="name" required>
        <div class="form-feedback invalid">يرجى إدخال الاسم</div>
    </div>
    
    <button type="submit" class="btn btn-primary-modern">
        <i class="fas fa-save me-2"></i>
        حفظ
    </button>
</form>
```

### الجداول

```blade
<table class="table-modern table-sortable">
    <thead>
        <tr>
            <th data-sortable="name">الاسم</th>
            <th data-sortable="email">البريد الإلكتروني</th>
            <th data-sortable="status">الحالة</th>
            <th>الإجراءات</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>محمد أحمد</td>
            <td>mohamed@example.com</td>
            <td><span class="badge-modern badge-success">نشط</span></td>
            <td>
                <button class="btn btn-sm btn-primary-modern">عرض</button>
            </td>
        </tr>
    </tbody>
</table>
```

### الشارات (Badges)

```blade
<span class="badge-modern badge-primary">جديد</span>
<span class="badge-modern badge-success">نشط</span>
<span class="badge-modern badge-warning">قيد المراجعة</span>
<span class="badge-modern badge-danger">ملغي</span>
<span class="badge-modern badge-info">معلومات</span>
```

---

## الوظائف JavaScript

### عرض رسالة تحميل
```javascript
showLoading('جاري تحميل البيانات...');
// ... perform operation
hideLoading();
```

### عرض رسالة Toast
```javascript
showToast('تم الحفظ بنجاح', 'success');
showToast('حدث خطأ', 'danger');
showToast('تحذير', 'warning');
```

### عرض نافذة تأكيد
```javascript
showConfirmationDialog(
    'هل أنت متأكد من حذف هذا العنصر؟',
    'danger',
    function() {
        // Action to perform on confirmation
        console.log('Confirmed!');
    }
);
```

### استخدام التأكيد في HTML
```blade
<a href="{{ route('item.delete', $item) }}" 
   class="btn btn-danger-modern"
   data-confirm="هل أنت متأكد من حذف هذا العنصر؟"
   data-confirm-type="danger">
    <i class="fas fa-trash me-2"></i>
    حذف
</a>
```

---

## أمثلة عملية

### صفحة قائمة مع فلاتر

```blade
@extends('layouts.app')

@section('content')
    {{-- Breadcrumbs --}}
    @include('components.breadcrumbs', [
        'items' => [
            ['title' => 'الرئيسية', 'url' => route('home')],
            ['title' => 'الخريجين']
        ]
    ])
    
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            @include('components.stat-card', [
                'title' => 'إجمالي الخريجين',
                'value' => $totalGraduates,
                'icon' => 'fas fa-user-graduate',
                'color' => 'primary'
            ])
        </div>
        <div class="col-md-3">
            @include('components.stat-card', [
                'title' => 'الموظفون',
                'value' => $employedGraduates,
                'icon' => 'fas fa-briefcase',
                'color' => 'success',
                'trend' => '+15%',
                'trendDirection' => 'up'
            ])
        </div>
    </div>
    
    {{-- Filters Panel --}}
    <div class="filters-panel-modern">
        <div class="filters-header">
            <h5 class="filters-title">
                <i class="fas fa-filter me-2"></i>
                الفلاتر
            </h5>
        </div>
        
        <div class="filter-group" data-filter-group="status">
            <label class="filter-label">الحالة</label>
            <div class="filter-options">
                <button class="filter-chip active" data-filter-value="all">الكل</button>
                <button class="filter-chip" data-filter-value="employed">موظف</button>
                <button class="filter-chip" data-filter-value="unemployed">غير موظف</button>
            </div>
        </div>
    </div>
    
    {{-- Data Table or Empty State --}}
    @if($graduates->count() > 0)
        <table class="table-modern table-sortable">
            <thead>
                <tr>
                    <th data-sortable="name">الاسم</th>
                    <th data-sortable="major">التخصص</th>
                    <th data-sortable="status">الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($graduates as $graduate)
                    <tr>
                        <td>{{ $graduate->name }}</td>
                        <td>{{ $graduate->major }}</td>
                        <td>
                            <span class="badge-modern badge-{{ $graduate->employed ? 'success' : 'warning' }}">
                                {{ $graduate->employed ? 'موظف' : 'غير موظف' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('graduates.show', $graduate) }}" class="btn btn-sm btn-primary-modern">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        @include('components.empty-state', [
            'icon' => 'fas fa-user-graduate',
            'title' => 'لا يوجد خريجين',
            'message' => 'لم يتم العثور على أي خريجين',
            'actionText' => 'إضافة خريج',
            'actionUrl' => route('graduates.create')
        ])
    @endif
@endsection
```

---

## ملاحظات مهمة

1. **الاتساق**: استخدم المكونات المتاحة بدلاً من إنشاء أنماط مخصصة
2. **الأداء**: المكونات محسّنة للأداء وتستخدم CSS Variables
3. **الاستجابة**: جميع المكونات متجاوبة مع جميع أحجام الشاشات
4. **إمكانية الوصول**: المكونات تدعم ARIA labels وقارئات الشاشة
5. **التوثيق**: وثّق أي مكونات جديدة تضيفها

---

## الدعم

للمزيد من المساعدة أو الإبلاغ عن مشاكل، يرجى الرجوع إلى:
- الوثائق الكاملة في `.agent/workflows/`
- ملفات CSS في `resources/css/`
- ملفات JavaScript في `resources/js/`
