<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($argc < 2) {
    echo "Usage: php tools/simulate_verify.php path/to/file.xlsx\n";
    exit(2);
}

$path = $argv[1];
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit(3);
}


// Use the actual Migration component's verify() logic to replicate behaviour.
require __DIR__ . '/../vendor/autoload.php';

try {
    $m = new \App\Livewire\Admin\Migration();
    // create uploaded file wrapper (third parameter true marks test)
    $m->file = new \Illuminate\Http\UploadedFile($path, basename($path), null, null, true);
    $m->verify();
    echo "Simulation complete. verified={$m->verified}, message=\"{$m->verifyMessage}\"\n";
} catch (\Throwable $e) {
    echo "Exception during simulation: " . $e->getMessage() . "\n";
}
exit(0);
