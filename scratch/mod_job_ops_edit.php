<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/job-opportunities/edit.blade.php';
$c = file_get_contents($f);

// Find the form
if (preg_match('/<form action="{{ route\(\'job-opportunities\.update\', \$opportunity->id\) }}" method="POST">/', $c, $matches) || preg_match('/<form action="{{ route\(\'job-opportunities\.update\', \$opportunity\) }}" method="POST">/', $c, $matches)) {
    // Remove the old container/card
    $c = preg_replace('/<div class="container-fluid">\s*<div class="row">\s*<div class="col-12">\s*<div class="card">\s*<div class="card-header d-flex justify-content-between align-items-center">\s*<h3>.*?<\/h3>\s*<div class="d-flex gap-2">.*?<\/div>\s*<\/div>\s*<div class="card-body">/s', '', $c);
    
    // Inject the component
    $c = str_replace($matches[0], '<x-bento-form title="تعديل فرصة عمل" subtitle="تحديث تفاصيل الفرصة الوظيفية" icon="fa-edit">
' . $matches[0], $c);
    
    // Replace the submit button
    $c = preg_replace('/<div class="mt-4">\s*<button type="submit" class="btn btn-primary">\s*<i class="fas fa-save me-2"><\/i>حفظ التعديلات\s*<\/button>\s*<a.*?<\/a>\s*<\/div>/s', '<div class="text-center mt-5"><button type="submit" class="btn-register"><i class="fas fa-save me-2"></i> حفظ التعديلات</button></div>', $c);
    
    // Remove trailing card tags
    $c = preg_replace('/<\/form>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s', "</form>\n</x-bento-form>", $c);
    
    file_put_contents($f, $c);
    echo "Done job ops edit\n";
} else {
    echo "Form tag not found.";
}
