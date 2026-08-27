<?php
$f = 'c:/Users/Sohib/graduate_training_system/scratch/old_create.blade.php';
$c = file_get_contents($f);

if (preg_match('/@push\(\'styles\'\)\s*<style>(.*?)<\/style>\s*@endpush/s', $c, $matches)) {
    $css = trim($matches[1]);
    
    // Instead of regex replacing randomly, let's prefix the selectors carefully.
    // Or even better, let's NOT prefix them, but just add !important to the key properties
    // like background color, font sizes, etc to overcome Bootstrap.
    
    $css = str_replace('background: #1e3a8a;', 'background: #1e3a8a !important;', $css);
    $css = str_replace('background-color: #f8f9fa;', 'background-color: #f8f9fa !important;', $css);
    $css = str_replace('border-bottom: 4px solid #f59e0b;', 'border-bottom: 4px solid #f59e0b !important;', $css);
    $css = str_replace('color: white;', 'color: white !important;', $css);
    
    // Since Bootstrap targets .card-body with `padding: var(--bs-card-spacer-y) var(--bs-card-spacer-x);`,
    // the original CSS `.card-body { padding: 40px; }` might be overridden.
    $css = str_replace('padding: 40px;', 'padding: 40px !important;', $css);
    
    // And for form controls
    $css = str_replace('border: 2px solid #e2e8f0;', 'border: 2px solid #e2e8f0 !important;', $css);
    
    file_put_contents('c:/Users/Sohib/graduate_training_system/public/css/premium-forms.css', $css);
    echo "CSS restored and important tags added.";
} else {
    echo "Could not find CSS";
}
