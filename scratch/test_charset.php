<?php
$str = "ظ…ظ„ظپ ط§ظ„ط´ط±ظƒط©";
echo mb_convert_encoding($str, 'Windows-1252', 'UTF-8');
