<?php
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'INVENTARIOS CARLOS FLORES 07-06-25 MATRIZ GENERAL DE CONTROL DE ALMACEN ENERO-JUNIO 2025  30-06-2025.xlsx';

if (!file_exists($path)) {
    echo "FILE_NOT_FOUND\n";
    exit(1);
}

echo "File exists. Size: " . round(filesize($path)/1024/1024,2) . " MB\n";

try {
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    echo "Extension: $ext\n";

    // Create a read filter to limit columns to those we need
    class MyReadFilter implements IReadFilter {
        private $columns;
        private $startRow;
        private $endRow;
        public function __construct(array $columns, int $startRow = 1, int $endRow = 100000)
        {
            $this->columns = $columns;
            $this->startRow = $startRow;
            $this->endRow = $endRow;
        }
        public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
        {
            if ($row < $this->startRow || $row > $this->endRow) return false;
            return in_array($columnAddress, $this->columns, true);
        }
    }

    $columnsNeeded = ['B','C','D','J','K','L','M','N','O','S','AU'];

    $reader = IOFactory::createReaderForFile($path);
    echo "Reader: " . get_class($reader) . "\n";

    $reader->setReadDataOnly(true);
    $reader->setReadFilter(new MyReadFilter($columnsNeeded, 2, 20000));

    ini_set('memory_limit', '1536M');
    set_time_limit(600);

    $spreadsheet = $reader->load($path);
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    $highestCol = $sheet->getHighestColumn();
    echo "Loaded. Highest row: $highestRow, highest column: $highestCol\n";

    $cells = ['B2','C2','D2','J2','K2','L2','M2','N2','O2','S2','AU2'];
    foreach ($cells as $cell) {
        $val = $sheet->getCell($cell)->getValue();
        echo "$cell => ".(is_null($val)?'NULL':(string)$val)."\n";
    }

    echo "SUCCESS\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(2);
}
