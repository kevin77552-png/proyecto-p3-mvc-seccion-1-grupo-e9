<?php
// bootstrap the Laravel app so facades work
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\InventoryImport;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\ProgressBar;
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if (! file_exists($file)) {
    echo "File not found: {$file}\n";
    exit(1);
}

// determine approximate number of rows so the progress bar can be initialised
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$totalRows = (int) $sheet->getHighestDataRow() - 7; // startRow() skips first 7
if ($totalRows < 0) {
    $totalRows = 0;
}

$output = new ConsoleOutput();
$bar = new ProgressBar($output, $totalRows);
$bar->setFormat(" %current%/%max% rows [%bar%] %percent:3s%%");
$bar->start();

// optional cancel file: touch it while the import is running to stop
$cancelPath = storage_path('app/stop_import');
if (file_exists($cancelPath)) {
    // ensure it's not left over from a previous run
    @unlink($cancelPath);
}

// let user know how to abort
$output->writeln("\nPress Ctrl+C or create the file '".$cancelPath."' to cancel import.\n");

// pass the progress bar (and cancel path) to the importer; the class will advance/check it
$importer = new InventoryImport(true, $bar, $cancelPath); // dry run = true to avoid DB writes while testing

try {
    Excel::import($importer, $file);
    $bar->finish();
    $count = $importer->imported ?? 0;
    echo "\nImport finished. Rows processed (dry run): {$count}\n";
} catch (Throwable $e) {
    // if cancellation occurred the exception message will say so
    $bar->clear();
    echo "Import failed: " . $e->getMessage() . "\n";
    exit(1);
}
