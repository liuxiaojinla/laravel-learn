<?php

namespace App\Services\Excel;

use App\Services\Excel\Concerns\WithCustomValueBinder;
use App\Services\Excel\Concerns\WithEvents;
use App\Services\Excel\Concerns\WithMapping;
use App\Services\Excel\Events\AfterImport;
use App\Services\Excel\Events\BeforeImport;
use App\Services\Excel\Factories\ReaderFactory;
use App\Services\Excel\Imports\HeadingRowExtractor;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\IReader;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Reader
{
    use  HasEventBus;

    /**
     * @var \string[][]
     */
    protected $config = [
        'properties' => [
            'creator' => '',
            'lastModifiedBy' => '',
            'title' => '',
            'description' => '',
            'subject' => '',
            'keywords' => '',
            'category' => '',
            'manager' => '',
            'company' => '',
        ],
        'styles' => [],
    ];

    /**
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var array
     */
    protected $worksheetInfoList;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    protected $worksheet;

    /**
     * @var Importable
     */
    protected $importable;

    /**
     * @var IReader
     */
    protected $reader;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge_recursive($this->config, $config);

        $this->setDefaultValueBinder();
    }

    /**
     * @param object $import
     * @param string $filePath
     * @param string|null $readerType
     * @return $this
     * @throws Exception
     * @throws \Throwable
     */
    protected function open($import, $filePath, $readerType = null)
    {
        $this->importable = $import;

        if ($import instanceof WithEvents) {
            $this->registerListeners($import->registerEvents());
        }

        if ($import instanceof WithCustomValueBinder) {
            Cell::setValueBinder($import);
        }

        $this->reader = ReaderFactory::make(
            $import,
            $readerType ?: IOFactory::identify($filePath)
        );

        $this->spreadsheet = $this->reader->load($filePath);
        $this->worksheetInfoList = $this->reader->listWorksheetInfo($filePath);

        return $this;
    }

    /**
     * @param Importable $import
     * @param string $filePath
     * @param string $readerType
     * @throws Exception
     * @throws \Throwable
     */
    public function read($import, $filePath, $readerType = null, $calculateFormulas = false)
    {
        $this->open($import, $filePath, $readerType);

        foreach ($this->worksheetInfoList as $sheetIndex => $info) {
            $this->worksheet = $this->spreadsheet->getSheet($sheetIndex);

            $startRow = $this->importable->startRow();
            $startRow = $startRow ?? 1;
            $endRow = ($startRow - 1) + $this->importable->limit();

            $headingRow = HeadingRowExtractor::extract($this->worksheet, $this->importable);

            // $endColumn = $this->importable instanceof WithColumnLimit ? $this->importable->endColumn() : null;

            foreach ($this->worksheet->getRowIterator($startRow, $endRow) as $index => $spreadsheetRow) {
                $row = new Row($spreadsheetRow, $headingRow);

                if ($this->importable->isSkipsEmptyRows() && $row->isEmpty($calculateFormulas)) {
                    continue;
                }

                if ($this->importable instanceof WithMapping) {
                    $row = $this->importable->map($row);
                }

                $this->importable->onRow($row);
            }
        }

        $this->afterImport($import);

        $this->clearListeners();
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    /**
     * @return IReader
     */
    public function getPhpSpreadsheetReader(): IReader
    {
        return $this->reader;
    }

    /**
     * @return array
     */
    public function getTotalRows(): array
    {
        $totalRows = [];
        foreach ($this->worksheetInfoList as $worksheet) {
            $totalRows[$worksheet['worksheetName']] = $worksheet['totalRows'];
        }

        return $totalRows;
    }

    /**
     * @return Spreadsheet
     */
    public function getDelegate()
    {
        return $this->spreadsheet;
    }

    /**
     * @param object $import
     */
    public function beforeImport($import)
    {
        $this->raise(new BeforeImport($this, $import));
    }

    /**
     * @param object $import
     */
    public function afterImport($import)
    {
        $this->raise(new AfterImport($this, $import));
    }

    /**
     * @return $this
     */
    public function setDefaultValueBinder()
    {
        $valueBinder = $this->config['value_binder'] ?? DefaultValueBinder::class;
        Cell::setValueBinder(app($valueBinder));

        return $this;
    }
}
