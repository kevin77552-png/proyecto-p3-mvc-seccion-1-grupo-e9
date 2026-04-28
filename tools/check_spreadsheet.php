<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if ($argc < 2) {
    echo "Usage: php tools/check_spreadsheet.php path/to/file.xlsx\n";
    exit(2);
}

$path = $argv[1];
echo "Path: $path\n";
if (!file_exists($path)) {
    echo "File not found\n";
    exit(3);
}

$size = filesize($path);
$mime = function_exists('mime_content_type') ? mime_content_type($path) : 'unknown';
echo "Size: $size bytes\n";
echo "Mime: $mime\n";

try {
    $identified = IOFactory::identify($path);
    echo "Identified type: $identified\n";
    $reader = IOFactory::createReader($identified);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($path);
    echo "Loaded OK. Sheet count: " . $spreadsheet->getSheetCount() . "\n";
    exit(0);
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    echo "PhpSpreadsheet Reader Exception: " . $e->getMessage() . "\n";
    $candidates = ['Xlsx','Xls','Csv','Xml','Ods'];
    foreach ($candidates as $c) {
        echo "Trying reader: $c ...\n";
        try {
            $r = IOFactory::createReader($c);
            $r->setReadDataOnly(true);
            $r->load($path);
            echo "Loaded with $c\n";
            exit(0);
        } catch (\Exception $e2) {
            echo "  Failed $c: " . $e2->getMessage() . "\n";
        }
    }
    exit(4);
} catch (\Exception $e) {
    echo "General Exception: " . $e->getMessage() . "\n";
    exit(5);
}
