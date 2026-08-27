<?php
$f = 'c:/Users/Sohib/graduate_training_system/resources/views/company/profile/edit.blade.php';
$c = file_get_contents($f);

// $c is a UTF-8 string containing wrong characters like ظ…
// We encode it back to CP1252 to get the raw bytes
$bytes = mb_convert_encoding($c, 'Windows-1252', 'UTF-8');

// Now $bytes contains the original UTF-8 bytes of the Arabic text!
// But since PHP strings are just byte arrays, $bytes IS the correct UTF-8 string!
file_put_contents('c:/Users/Sohib/graduate_training_system/resources/views/company/profile/edit.blade.php', $bytes);
echo "Fixed!";
