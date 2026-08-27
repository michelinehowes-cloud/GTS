<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/training-coordinator/trainings/create.blade.php';
$c = file_get_contents($f);

// Find the form
if (preg_match('/<form action="{{ route\(\'training-coordinator\.trainings\.store\'\) }}" method="POST">/', $c, $matches)) {
    // Remove the old container/card
    $c = preg_replace('/<div class="container-fluid">\s*<div class="row">\s*<div class="col-12">\s*<div class="card">\s*<div class="card-header">\s*<h3>(.*?)<\/h3>\s*<\/div>\s*<div class="card-body">/s', '', $c);
    
    // Inject the component
    $c = str_replace($matches[0], '<x-bento-form title="إضافة دورة تدريبية جديدة" subtitle="أدخل تفاصيل الدورة" icon="fa-chalkboard-teacher">
' . $matches[0], $c);
    
    // Replace the submit button
    $c = preg_replace('/<button type="submit" class="btn btn-primary">.*?<\/button>/s', '<div class="text-center mt-5"><button type="submit" class="btn-register"><i class="fas fa-save me-2"></i> حفظ الدورة</button></div>', $c);
    $c = preg_replace('/<a href="{{ route\(\'training-coordinator\.trainings\.index\'\) }}" class="btn btn-secondary">.*?<\/a>/s', '', $c);
    
    // Remove trailing card tags
    $c = preg_replace('/<\/form>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s', "</form>\n</x-bento-form>", $c);
    
    file_put_contents($f, $c);
    echo "Done training create\n";
}
