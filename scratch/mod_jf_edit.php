<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/job-fair/admin/edit.blade.php';
$c = file_get_contents($f);

// Remove the old header
$c = preg_replace('/<div class="d-flex align-items-center gap-3 mb-4">.*?<\/div>\s*<\/div>/s', '', $c);

// Start the component before the form
$c = str_replace('<form action="{{ route(\'job-fair.admin.update\', $jobFair) }}" method="POST" enctype="multipart/form-data">',
'<x-bento-form title="تعديل معرض توظيف" subtitle="تحديث بيانات المعرض" icon="fa-calendar-check">
<form action="{{ route(\'job-fair.admin.update\', $jobFair) }}" method="POST" enctype="multipart/form-data">', $c);

// Remove the old submit buttons block
$c = preg_replace('/<div class="d-flex justify-content-end gap-2.*?<\/div>/s', '', $c);

// Close the component after the form
$c = str_replace('</form>', '<div class="text-center mt-5"><button type="submit" class="btn-register"><i class="fas fa-save me-2"></i> حفظ التعديلات</button></div></form>
</x-bento-form>', $c);

// Convert inner cards to simple sections
$c = str_replace('<div class="card border-0 shadow-sm rounded-4 mb-4">', '<div class="form-section mb-4">', $c);
$c = str_replace('<div class="card-header border-0 bg-transparent p-4 pb-0">', '<h5 class="section-title">', $c);
$c = preg_replace('/<\/h5>\s*<\/div>/', '</h5>', $c);
$c = str_replace('<div class="card-body p-4">', '<div class="p-4">', $c);

file_put_contents($f, $c);
echo 'Done edit';
