<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/auth/graduate-register.blade.php';
$c = file_get_contents($f);

if (preg_match('/<style>(.*?)<\/style>/s', $c, $matches)) {
    $css = trim($matches[1]);
    
    // Add !important to prevent Bootstrap from overriding
    $css = str_replace('background: #1e3a8a;', 'background: #1e3a8a !important;', $css);
    $css = preg_replace('/background-color:\s*#f8f9fa;/', 'background-color: #f8f9fa !important;', $css);
    $css = preg_replace('/border-bottom:\s*4px solid #f59e0b;/', 'border-bottom: 4px solid #f59e0b !important;', $css);
    $css = preg_replace('/color:\s*white;/', 'color: white !important;', $css);
    $css = preg_replace('/padding:\s*40px;/', 'padding: 40px !important;', $css);
    $css = preg_replace('/border:\s*2px solid #e2e8f0;/', 'border: 2px solid #e2e8f0 !important;', $css);
    
    // I also need to make sure the CSS doesn't break.
    // By using !important, it will definitely override Bootstrap's `.card-header`
    // which is what ruined the header in the company panel.
    
    file_put_contents('c:/Users/Sohib/graduate_training_system/public/css/premium-forms.css', $css);
    echo "CSS restored from graduate-register and important tags added.";
} else {
    echo "Could not find CSS";
}
