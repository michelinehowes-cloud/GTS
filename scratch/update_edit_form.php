<?php
$createFile = 'c:\\Users\\Sohib\\graduate_training_system\\resources\\views\\career-guidance\\graduates\\create.blade.php';
$editFile = 'c:\\Users\\Sohib\\graduate_training_system\\resources\\views\\career-guidance\\graduates\\edit.blade.php';

$createContent = file_get_contents($createFile);
$editContent = file_get_contents($editFile);

// Extract the CV and LinkedIn inputs from the edit file so we can include them
preg_match('/<div class="form-group">\s*<label for="linkedin_url".*?<\/div>/s', $editContent, $linkedinMatch);
preg_match('/<div class="form-group">\s*<label for="cv".*?<\/div>\s*<button type="submit"/s', $editContent, $cvMatch);

$linkedinHtml = "";
$cvHtml = "";

if ($linkedinMatch) {
    // Convert to col-md-6 mb-3 modern layout
    $linkedinHtml = '
                                <div class="col-md-6 mb-3">
                                    <label for="linkedin_url" class="form-label">رابط لينكد إن</label>
                                    <input type="url" class="form-control @error(\'linkedin_url\') is-invalid @enderror" id="linkedin_url"
                                        name="linkedin_url" value="{{ old(\'linkedin_url\', $graduate->linkedin_url) }}">
                                    @error(\'linkedin_url\')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>';
}

if ($cvMatch) {
    // Convert to modern layout
    $cvHtml = '
                                <div class="col-md-6 mb-3">
                                    <label for="cv" class="form-label">السيرة الذاتية (PDF)</label>
                                    @if($graduate->cv_path)
                                        <div class="mb-2">
                                            <span class="text-success"><i class="fas fa-check-circle"></i> ملف مرفق: </span>
                                            <a href="{{ Storage::url($graduate->cv_path) }}" target="_blank" class="fw-bold text-primary text-decoration-none">
                                                {{ basename($graduate->cv_path) }}
                                            </a>
                                        </div>
                                    @endif
                                    <input type="file" class="form-control @error(\'cv\') is-invalid @enderror" id="cv" name="cv" accept=".pdf">
                                    <small class="form-text text-muted">يرجى رفع سيرتك الذاتية في حال رغبت بتحديثها. (الحد الأقصى: 5 ميغابايت)</small>
                                    @error(\'cv\')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>';
}

$degreeHtml = '
                                <div class="col-md-6 mb-3">
                                    <label for="degree" class="form-label">الدرجة العلمية *</label>
                                    <select class="form-select @error(\'degree\') is-invalid @enderror" id="degree" name="degree" required>
                                        <option value="">اختر الدرجة العلمية</option>
                                        @foreach($degrees as $degree)
                                            <option value="{{ $degree }}" {{ old(\'degree\', $graduate->degree) == $degree ? \'selected\' : \'\' }}>
                                                {{ $degree }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error(\'degree\')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>';

// Modify create template to fit edit
$newEditContent = str_replace(
    'action="{{ route(\'career-guidance.graduates.store\') }}" method="POST"', 
    'action="{{ route(\'career-guidance.graduates.update\', $graduate->id) }}" method="POST" enctype="multipart/form-data"', 
    $createContent
);
$newEditContent = str_replace(
    '@csrf', 
    "@csrf\n                            @method('PUT')", 
    $newEditContent
);

// Replace values
$replacements = [
    'old(\'name\')' => 'old(\'name\', $graduate->name)',
    'old(\'email\')' => 'old(\'email\', $graduate->email)',
    'old(\'phone\')' => 'old(\'phone\', $graduate->phone)',
    'old(\'national_id\')' => 'old(\'national_id\', $graduate->national_id)',
    'old(\'gpa\')' => 'old(\'gpa\', $graduate->gpa)',
    'old(\'skills\')' => 'old(\'skills\', is_array($graduate->skills) ? implode(\',\', $graduate->skills) : $graduate->skills)',
    'old(\'languages\')' => 'old(\'languages\', is_array($graduate->languages) ? implode(\',\', $graduate->languages) : $graduate->languages)',
    'old(\'work_experience\')' => 'old(\'work_experience\', $graduate->work_experience)',
    'old(\'address\')' => 'old(\'address\', $graduate->address)',
    'old(\'notes\')' => 'old(\'notes\', $graduate->notes)',
    
    // Selects and hidden fields
    'old(\'university\')' => 'old(\'university\', $graduate->university)',
    'old(\'sector\')' => 'old(\'sector\', $graduate->sector)',
    'old(\'faculty\')' => 'old(\'faculty\', $graduate->faculty)',
    'old(\'major\')' => 'old(\'major\', $graduate->major)',
    'old(\'graduation_year\')' => 'old(\'graduation_year\', $graduate->graduation_year)',
    
    'إضافة خريج جديد' => 'تعديل بيانات الخريج',
    'إضافة خريج' => 'تحديث البيانات',
    'fa-user-plus' => 'fa-save',
];

foreach ($replacements as $search => $replace) {
    $newEditContent = str_replace($search, $replace, $newEditContent);
}

// Inject LinkedIn and CV before the last elements (e.g. before "ملاحظات إضافية")
$notesDiv = '<div class="col-12 mb-3">
                                    <label for="notes" class="form-label">ملاحظات إضافية</label>';

$newEditContent = str_replace($notesDiv, $linkedinHtml . "\n" . $cvHtml . "\n" . $notesDiv, $newEditContent);

// Inject Degree after graduation year
$graduationYearEnd = '@enderror
                                </div>';
// we find the exact block for graduation_year and inject after it
$newEditContent = preg_replace('/(<label for="graduation_year".*?<\/div>)/s', "$1\n" . $degreeHtml, $newEditContent);

// Fix Old select fields logic
$newEditContent = preg_replace('/{{ old\(\'university\', \$graduate->university\) == (.*?) \? \'selected\' : \'\' }}/', '{{ old(\'university\', $graduate->university) == $1 ? \'selected\' : \'\' }}', $newEditContent);
$newEditContent = preg_replace('/{{ old\(\'graduation_year\', \$graduate->graduation_year\) == \$year \? \'selected\' : \'\' }}/', '{{ old(\'graduation_year\', $graduate->graduation_year) == $year ? \'selected\' : \'\' }}', $newEditContent);
$newEditContent = preg_replace('/old\(\'employment_status\'\)/', 'old(\'employment_status\', $graduate->employment_status)', $newEditContent);

file_put_contents($editFile, $newEditContent);
echo "Edit form updated successfully.";
