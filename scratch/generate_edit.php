<?php
$createFile = 'c:/Users/Sohib/graduate_training_system/resources/views/career-guidance/graduates/create.blade.php';
$editFile = 'c:/Users/Sohib/graduate_training_system/resources/views/career-guidance/graduates/edit.blade.php';

$content = file_get_contents($createFile);

// 1. Change Titles
$content = str_replace("@section('title', 'إضافة خريج جديد')", "@section('title', 'تعديل بيانات الخريج')", $content);
$content = str_replace('<h2><i class="fas fa-user-graduate me-2"></i> إنشاء حساب خريج</h2>', '<h2><i class="fas fa-user-edit me-2"></i> تعديل بيانات الخريج</h2>', $content);
$content = str_replace('<p>الرجاء تعبئة المعلومات أدناه لإنشاء حساب الخريج بنجاح</p>', '<p>الرجاء تحديث المعلومات أدناه وتأكيد التغييرات</p>', $content);
$content = str_replace('تسجيل حساب جديد', 'حفظ التعديلات', $content);
$content = str_replace('fa-user-plus', 'fa-save', $content);

// 2. Change Form Action and add PUT method
$oldFormTag = '<form method="POST" action="{{ route(\'career-guidance.graduates.store\') }}" id="registrationForm" enctype="multipart/form-data">
                    @csrf';
$newFormTag = '<form method="POST" action="{{ route(\'career-guidance.graduates.update\', $graduate->id) }}" id="registrationForm" enctype="multipart/form-data">
                    @csrf
                    @method(\'PUT\')';
$content = str_replace($oldFormTag, $newFormTag, $content);

// 3. Pre-fill fields
// Input text/number/email/date fields
$fields = [
    'name', 'email', 'phone', 'national_id', 'birth_date', 'address', 
    'university', 'graduation_year', 'gpa', 'skills', 'languages', 'experiences'
];

foreach ($fields as $field) {
    // For text inputs and textareas
    // <input ... value="{{ old('field') }}"
    $content = str_replace("value=\"{{ old('$field') }}\"", "value=\"{{ old('$field', \$graduate->$field) }}\"", $content);
    // <textarea ...>{{ old('field') }}</textarea>
    $content = str_replace("{{ old('$field') }}</textarea>", "{{ old('$field', \$graduate->$field) }}</textarea>", $content);
}

// Select fields
$selects = [
    'gender', 'sector', 'qualification'
];
foreach ($selects as $sel) {
    // old('gender') == 'male' ? 'selected' : '' 
    // -> old('gender', $graduate->gender) == 'male' ? 'selected' : ''
    $content = preg_replace("/old\('$sel'\) ==/", "old('$sel', \$graduate->$sel) ==", $content);
}

// Special cases: employment_status
$content = preg_replace("/old\('employment_status'\) ==/", "old('employment_status', \$graduate->employment_status) ==", $content);

// Special cases: faculty, major (these are dynamically populated by JS but have pre-filled options in old edit)
// For edit, we need to pass the old specialization and faculty to JS
$content = str_replace('<input type="hidden" id="old_faculty" value="{{ old(\'faculty\') }}">', '<input type="hidden" id="old_faculty" value="{{ old(\'faculty\', $graduate->faculty) }}">', $content);
$content = str_replace('<input type="hidden" id="old_specialization" value="{{ old(\'specialization\') }}">', '<input type="hidden" id="old_specialization" value="{{ old(\'specialization\', $graduate->major) }}">', $content);

// Handle CV display if exists
$cvField = '<label for="cv" class="form-label">السيرة الذاتية (PDF)</label>';
$cvReplacement = '<label for="cv" class="form-label">السيرة الذاتية (PDF)</label>
                                @if($graduate->cv_path)
                                    <div class="mb-2">
                                        <a href="{{ Storage::url($graduate->cv_path) }}" target="_blank" class="badge bg-primary text-decoration-none">
                                            <i class="fas fa-file-pdf me-1"></i> عرض السيرة الذاتية الحالية
                                        </a>
                                    </div>
                                @endif';
$content = str_replace($cvField, $cvReplacement, $content);

file_put_contents($editFile, $content);
echo "Edit file generated.";
