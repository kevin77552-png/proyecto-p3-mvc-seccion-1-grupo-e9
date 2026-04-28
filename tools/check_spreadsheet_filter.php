<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class RowReadFilter implements IReadFilter
{
    private $startRow;
    private $endRow;
    private $columns;

    public function __construct(int $startRow = 8, int $endRow = null, array $columns = [])
    {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        $this->columns = $columns;
    }

    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool
    {
        if ($row < $this->startRow) {
            return false;
        }
        if ($this->endRow !== null && $row > $this->endRow) {
            return false;
        }
        if (empty($this->columns)) {
            return true;
        }
        return in_array($columnAddress, $this->columns, true);
    }
}

if ($argc < 2) {
    echo "Usage: php tools/check_spreadsheet_filter.php path/to/file.xlsx [startRow]\n";
    exit(2);
}

$path = $argv[1];
$startRow = isset($argv[2]) ? (int)$argv[2] : 8;

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
    if (strtolower($identified) === 'xlsx' || strtolower($identified) === 'ods') {
        $filter = new RowReadFilter($startRow, null, ['C','D','J','K','L','M','O','S']);
        $reader->setReadFilter($filter);
    }
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($path);
    echo "Loaded OK with ReadFilter. Sheet count: " . $spreadsheet->getSheetCount() . "\n";
    $sheet = $spreadsheet->getActiveSheet();
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();
    echo "Highest row in filtered load: $highestRow, highest column: $highestColumn\n";
    // Show first few rows of filtered columns
    $limit = $startRow + 4;
    for ($r = $startRow; $r <= $limit; $r++) {
        $vals = [];
        foreach (['C','D','J','K','L','M','O','S'] as $col) {
            $vals[] = $sheet->getCell($col.$r)->getValue();
        }
        echo "Row $r: " . implode(' | ', array_map(function($v){ return is_null($v)?'<null>':(string)$v; }, $vals)) . "\n";
    }
    exit(0);

} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    exit(4);
}
