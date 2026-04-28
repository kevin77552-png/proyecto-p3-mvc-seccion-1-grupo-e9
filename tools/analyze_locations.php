<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
if (!file_exists($file)) {
    echo "Archivo no encontrado: $file\n";
    exit(1);
}

$reader = IOFactory::createReaderForFile($file);
$reader->setReadDataOnly(true);
$spreadsheet = $reader->load($file);

foreach ($spreadsheet->getSheetNames() as $index => $name) {
    $sheet = $spreadsheet->getSheet($index);
    echo "Sheet #".($index+1)." - $name\n";
    $highestRow = $sheet->getHighestRow();
    $highestCol = $sheet->getHighestColumn();
    $highestColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestCol);
    for ($r = 1; $r <= $highestRow; $r++) {
        for ($c = 1; $c <= $highestColIndex; $c++) {
            $coord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $r;
            $cell = $sheet->getCell($coord);
            $val = $cell->getValue();
            if ($val !== null && $val !== '') {
                echo "$coord => ";
                if (is_string($val) && strlen($val) && $val[0] === '=') {
                    echo "(formula) " . $val;
                } else {
                    echo json_encode($val, JSON_UNESCAPED_UNICODE);
                }
                echo "\n";
            }
        }
    }
    echo "\n";
}
