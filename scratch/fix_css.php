<?php
$f = 'c:/Users/Sohib/graduate_training_system/public/css/premium-forms.css';
$c = file_get_contents($f);

// More specific selectors
$c = str_replace('.card-header', '.registration-card .card-header', $c);
$c = str_replace('.card-body', '.registration-card .card-body', $c);
$c = str_replace('.form-control', '.registration-card .form-control', $c);
$c = str_replace('.form-select', '.registration-card .form-select', $c);
$c = str_replace('.form-label', '.registration-card .form-label', $c);
$c = preg_replace('/h2\s*{/', '.registration-card h2 {', $c);
$c = preg_replace('/p\s*{/', '.registration-card p {', $c);

// Specifically handle the CSS conflict with Bootstrap by adding !important to background if needed,
// but the parent selector should be enough.
$c = str_replace('background: #1e3a8a;', 'background: #1e3a8a !important;', $c);

file_put_contents($f, $c);
echo 'CSS made specific.';
