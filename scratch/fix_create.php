<?php
$registerFile = 'c:/Users/Sohib/graduate_training_system/resources/views/auth/graduate-register.blade.php';
$registerContent = file_get_contents($registerFile);

preg_match('/<style>(.*?)<\/style>/s', $registerContent, $stylesMatch);
$css = $stylesMatch[1] ?? '';

preg_match('/<div class="registration-card">(.*?)<!-- Footer -->/s', $registerContent, $cardMatch);
$html = $cardMatch[1] ?? '';

// 1. Remove password sections
$html = preg_replace('/<div class="col-md-6">\s*<label for="password".*?<\/div>\s*<\/div>/s', '', $html);
$html = preg_replace('/<div class="col-md-6">\s*<label for="password_confirmation".*?<\/div>\s*<\/div>/s', '', $html);

// 2. Change action to store route and add enctype
$html = preg_replace('/<form method="POST" action="[^"]*" id="registrationForm">/', '<form method="POST" action="{{ route(\'career-guidance.graduates.store\') }}" id="registrationForm" enctype="multipart/form-data">', $html);

// 3. Add employment_status before experiences
$employmentStatusHtml = '
                            <div class="col-12">
                                <label for="employment_status" class="form-label">
                                    حالة التوظيف <span class="required">*</span>
                                </label>
                                <select class="form-select @error(\'employment_status\') is-invalid @enderror"
                                    id="employment_status" name="employment_status" required>
                                    <option value="">اختر حالة التوظيف</option>
                                    <option value="employed" {{ old(\'employment_status\') == \'employed\' ? \'selected\' : \'\' }}>موظف</option>
                                    <option value="unemployed" {{ old(\'employment_status\') == \'unemployed\' ? \'selected\' : \'\' }}>غير موظف</option>
                                    <option value="seeking_opportunities" {{ old(\'employment_status\') == \'seeking_opportunities\' ? \'selected\' : \'\' }}>باحث عن عمل</option>
                                    <option value="further_study" {{ old(\'employment_status\') == \'further_study\' ? \'selected\' : \'\' }}>مستكمل للدراسة</option>
                                </select>
                                @error(\'employment_status\')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>';
                            
$html = str_replace('<div class="col-12">
                                <label for="experiences"', $employmentStatusHtml . '

                            <div class="col-12">
                                <label for="experiences"', $html);
                                
// 4. Add CV after languages
$cvHtml = '
                            <div class="col-md-12">
                                <label for="cv" class="form-label">السيرة الذاتية (PDF)</label>
                                <input type="file" class="form-control @error(\'cv\') is-invalid @enderror" id="cv" name="cv" accept=".pdf">
                                @error(\'cv\')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>';
                            
$html = preg_replace('/(@error\(\'languages\'\).*?<\/div>\s*@enderror\s*<\/div>)/s', "$1\n$cvHtml", $html);

$newCreate = "@extends('layouts.app')\n\n@section('title', 'إضافة خريج جديد')\n\n@section('styles')\n<style>\n$css\n</style>\n@endsection\n\n@section('content')\n<div class=\"container-fluid py-4\">\n<div class=\"registration-container\">\n<div class=\"registration-card\">\n$html\n</div>\n</div>\n</div>\n@endsection\n\n@section('scripts')\n<script src=\"{{ asset('js/university-data.js') }}\"></script>\n<script>\nconst form = document.getElementById('registrationForm');\nconst submitBtn = document.getElementById('submitBtn');\nif(form){\nform.addEventListener('submit', function () {\nsubmitBtn.classList.add('loading');\nsubmitBtn.disabled = true;\n});\n}\n</script>\n@endsection";

file_put_contents('c:/Users/Sohib/graduate_training_system/resources/views/career-guidance/graduates/create.blade.php', $newCreate);
echo "File created successfully.";
