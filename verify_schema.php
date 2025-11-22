<?php

use Illuminate\Support\Facades\Schema;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = Schema::getColumnListing('evaluations');
echo "Columns in evaluations table: " . implode(', ', $columns) . "\n";

if (in_array('status', $columns)) {
    echo "SUCCESS: 'status' column exists.\n";
} else {
    echo "ERROR: 'status' column MISSING.\n";
}
