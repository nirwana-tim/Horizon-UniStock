<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ib = \App\Models\ImportBatch::latest()->first();
if ($ib) {
    echo "ID: " . $ib->id . "\n";
    echo "Type: " . $ib->import_type . "\n";
    echo "File Name: " . $ib->file_name . "\n";
    echo "Status: " . $ib->status . "\n";
    echo "Failed Rows: " . $ib->failed_rows . "\n";
    echo "Error Log Summary:\n";
    print_r($ib->error_log);
} else {
    echo "No import batches found.\n";
}
