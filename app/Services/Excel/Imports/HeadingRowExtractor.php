<?php

namespace App\Services\Excel\Imports;

use App\Services\Excel\Importable;
use App\Services\Excel\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HeadingRowExtractor
{
    /**
     * @const int
     */
    const DEFAULT_HEADING_ROW = 1;

    /**
     * @param Importable $importable
     *
     * @return int
     */
    public static function headingRow($importable): int
    {
        return method_exists($importable, 'headingRow')
            ? $importable->headingRow()
            : self::DEFAULT_HEADING_ROW;
    }


    /**
     * @param Worksheet $worksheet
     * @param Importable $importable
     *
     * @return array
     */
    public static function extract(Worksheet $worksheet, $importable): array
    {
        $headingRowNumber = self::headingRow($importable);
        $rows = iterator_to_array($worksheet->getRowIterator($headingRowNumber, $headingRowNumber));
        $headingRow = reset($rows);
        $endColumn = $importable->endColumn();

        return HeadingRowFormatter::format((new Row($headingRow))->toArray(null, false, false, $endColumn));
    }
}
