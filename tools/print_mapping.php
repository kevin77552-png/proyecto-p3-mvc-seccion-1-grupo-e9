<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if (!file_exists($file)) {
    echo "File not found: {$file}\n";
    exit(1);
}

$reader = IOFactory::createReaderForFile($file);
$spreadsheet = $reader->load($file);
$sheet = $spreadsheet->getSheet(0); // first sheet

$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

$header = $sheet->rangeToArray('A7:' . $highestColumn . '7')[0];
echo "Header (row 7): \n";
print_r($header);

echo "\nFirst 20 rows starting at row 8:\n";
for ($r = 8; $r <= $highestRow && $r < 28; $r++) {
    $row = $sheet->rangeToArray('A' . $r . ':' . $highestColumn . $r)[0];
    echo "Row $r: ";
    print_r($row);
}
