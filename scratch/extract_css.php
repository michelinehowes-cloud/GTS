<?php
$createFile = 'c:/Users/Sohib/graduate_training_system/resources/views/career-guidance/graduates/create.blade.php';
$editFile = 'c:/Users/Sohib/graduate_training_system/resources/views/career-guidance/graduates/edit.blade.php';
$cssFile = 'c:/Users/Sohib/graduate_training_system/public/css/premium-forms.css';

$createContent = file_get_contents($createFile);

// Extract styles
if (preg_match('/@push\(\'styles\'\)\s*<style>(.*?)<\/style>\s*@endpush/s', $createContent, $matches)) {
    $css = trim($matches[1]);
    file_put_contents($cssFile, $css);
    echo "Extracted CSS to premium-forms.css\n";
    
    // Remove from create
    $newCreateContent = str_replace($matches[0], '', $createContent);
    file_put_contents($createFile, ltrim($newCreateContent));
    echo "Removed inline CSS from create.blade.php\n";
}

$editContent = file_get_contents($editFile);
if (preg_match('/@push\(\'styles\'\)\s*<style>(.*?)<\/style>\s*@endpush/s', $editContent, $matches)) {
    $newEditContent = str_replace($matches[0], '', $editContent);
    file_put_contents($editFile, ltrim($newEditContent));
    echo "Removed inline CSS from edit.blade.php\n";
}
