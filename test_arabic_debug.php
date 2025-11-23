<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Helpers\Arabic;

$text = "تجربة";
$reshaped = Arabic::reshape($text);

echo "Original: $text\n";
echo "Reshaped: $reshaped\n";

echo "Original Hex: " . bin2hex($text) . "\n";
echo "Reshaped Hex: " . bin2hex($reshaped) . "\n";

// Check if Reshaped contains presentation forms (e.g. fe97 for Ta Initial)
// Ta (ت) is d8aa in UTF-8.
// Presentation form for Ta Initial is fe97 (ef ba 97 in UTF-8).

if (strpos(bin2hex($reshaped), 'efba97') !== false) {
    echo "SUCCESS: Found Ta Initial form.\n";
} else {
    echo "FAILURE: Did not find Ta Initial form.\n";
}
