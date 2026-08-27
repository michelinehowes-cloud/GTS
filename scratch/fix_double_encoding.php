<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/company/profile/edit.blade.php';
$c = file_get_contents($f);

// Convert back the double-encoded UTF-8
// ISO-8859-1 bytes were interpreted as UTF-8
$fixed = mb_convert_encoding($c, 'ISO-8859-1', 'UTF-8');

file_put_contents('c:/Users/Sohib/graduate_training_system/scratch/fixed_profile.blade.php', $fixed);
echo "Fixed encoding.";
