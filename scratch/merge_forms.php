<?php
$registerFile = 'c:/Users/Sohib/graduate_training_system/resources/views/auth/graduate-register.blade.php';
$createFile = 'c:/Users/Sohib/graduate_training_system/resources/views/career-guidance/graduates/create.blade.php';

$registerContent = file_get_contents($registerFile);
$createContent = file_get_contents($createFile);

preg_match('/<style>(.*?)<\/style>/s', $registerContent, $stylesMatch);
$css = $stylesMatch[1] ?? '';

preg_match('/<div class="registration-card">(.*?)<!-- Footer -->/s', $registerContent, $cardMatch);
$html = $cardMatch[1] ?? '';

// Modify HTML
// 1. Remove password sections
$html = preg_replace('/<div class="col-md-6">\s*<label for="password".*?<\/div>\s*<\/div>/s', '', $html);
$html = preg_replace('/<div class="col-md-6">\s*<label for="password_confirmation".*?<\/div>\s*<\/div>/s', '', $html);

// 2. Change action to store route
$html = preg_replace('/action="\{\{ route\(\'register\'\) \}\}"/', 'action="{{ route(\'career-guidance.graduates.store\') }}"', $html);

// 3. We need to make sure the fields match what Career Guidance uses.
// For example, in the old create.blade.php we had `employment_status`, `notes`.
// We will just replace the inner contents of create.blade.php with this new HTML + CSS.

$newCreate = "@extends('layouts.app')\n\n@section('title', 'إضافة خريج جديد')\n\n@section('styles')\n<style>\n$css\n</style>\n@endsection\n\n@section('content')\n<div class=\"container-fluid py-4\">\n<div class=\"registration-container\">\n<div class=\"registration-card\">\n$html\n</div>\n</div>\n</div>\n@endsection\n\n@section('scripts')\n<script src=\"{{ asset('js/university-data.js') }}\"></script>\n<script>\nconst form = document.getElementById('registrationForm');\nconst submitBtn = document.getElementById('submitBtn');\nif(form){\nform.addEventListener('submit', function () {\nsubmitBtn.classList.add('loading');\nsubmitBtn.disabled = true;\n});\n}\n</script>\n@endsection";

file_put_contents('scratch/new_create.blade.php', $newCreate);
echo "File processed successfully.";
