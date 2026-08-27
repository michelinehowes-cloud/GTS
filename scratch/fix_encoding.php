<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/company/profile/edit.blade.php';
$c = file_get_contents($f);

$c2 = mb_convert_encoding($c, 'Windows-1256', 'UTF-8');
file_put_contents('c:/Users/Sohib/graduate_training_system/scratch/fixed1.blade.php', $c2);

$c3 = mb_convert_encoding($c, 'Windows-1252', 'UTF-8');
file_put_contents('c:/Users/Sohib/graduate_training_system/scratch/fixed2.blade.php', $c3);
