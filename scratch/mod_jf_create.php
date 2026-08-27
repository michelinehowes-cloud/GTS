<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/job-fair/admin/create.blade.php';
$c = file_get_contents($f);

// Convert inner cards to simple sections
$c = str_replace('<div class="card border-0 shadow-sm rounded-4 mb-4">', '<div class="form-section mb-4">', $c);
$c = str_replace('<div class="card-header border-0 bg-transparent p-4 pb-0">', '<h5 class="section-title">', $c);
$c = preg_replace('/<\/h5>\s*<\/div>/', '</h5>', $c);
$c = str_replace('<div class="card-body p-4">', '<div class="p-4">', $c);

file_put_contents($f, $c);
echo 'Inner cards removed.';
