<?php
require __DIR__.'/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class MyFilter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        return in_array($row, [5,7]);
    }
}

$file = 'C:\\Users\\kevin\\OneDrive\\Escritorio\\prueba importar.xlsx';
$reader = IOFactory::createReaderForFile($file);
$reader->setReadDataOnly(true);
$reader->setLoadSheetsOnly('JUNIO 2025 (2)');
$reader->setReadFilter(new MyFilter());
$spreadsheet = $reader->load($file);
$sheet = $spreadsheet->getSheet(0);

$maxCol = $sheet->getHighestColumn();
$maxIdx = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($maxCol);

foreach ([5,7] as $r) {
    echo "Row {$r}:\n";
    for ($c=1; $c <= $maxIdx; $c++) {
        $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
        $cellAddr = $letter . $r;
        $val = $sheet->getCell($cellAddr)->getValue();
        echo "  {$letter} (idx".($c-1)."): ";
        var_export($val);
        echo "\n";
    }
    echo "\n";
}
