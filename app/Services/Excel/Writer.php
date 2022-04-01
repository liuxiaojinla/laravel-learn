<?php

namespace App\Services\Excel;

use App\Services\Excel\Cache\CacheManager;
use App\Services\Excel\Concerns\WithCustomValueBinder;
use App\Services\Excel\Concerns\WithDefaultStyles;
use App\Services\Excel\Concerns\WithProperties;
use App\Services\Excel\Factories\WriterFactory;
use App\Services\Excels\DefaultValueBinder;
use Illuminate\Support\Arr;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Cell as SpreadsheetCell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Writer
{
    use HasEventBus;

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
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    protected $spreadsheet;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    protected $worksheet;

    /**
     * @var Exportable
     */
    protected $exportable;

    /**
     * @var Column[]
     */
    protected $columns;

    /**
     * @var int
     */
    protected $exportCount = 0;

    /**
     * @var int
     */
    protected $exportErrorCount = 0;

    /**
     * @var \Throwable
     */
    protected $lastExportError = null;

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = array_merge_recursive($this->config, $config);

        $this->setDefaultValueBinder();
    }

    /**
     * @return Writer
     */
    public function setDefaultValueBinder()
    {
        $valueBinder = $this->config['value_binder'] ?? DefaultValueBinder::class;
        Cell::setValueBinder(app($valueBinder));

        return $this;
    }

    /**
     * 写入到文件
     * @param Exportable $export
     * @param string $filePath
     * @param string $writerType
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function write($export, $filePath, $writerType = null)
    {
        $this->open($export);

        $this->initColumns();

        $this->handleData();

        return $this->save($filePath, $writerType);
    }

    /**
     * @param Exportable $export
     */
    protected function open($export)
    {
        $this->exportable = $export;
        $this->spreadsheet = new Spreadsheet;
        $this->spreadsheet->disconnectWorksheets();

        if ($export instanceof WithCustomValueBinder) {
            SpreadsheetCell::setValueBinder($export);
        }

        // $this->worksheet = $this->spreadsheet->getActiveSheet();
        $this->worksheet = $this->spreadsheet->createSheet();

        $this->columns = $this->exportable->columns();
    }

    /**
     * 初始化栏目
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function initColumns()
    {
        $startRow = 1;

        $columnIndex = 1;
        foreach ($this->columns as $column) {
            $columnDimension = $this->worksheet->getColumnDimensionByColumn($columnIndex);
            $columnDimension->setWidth($column->getWidth())->setAutoSize($column->isAutoSize());

            $colCoordinate = Coordinate::stringFromColumnIndex($columnIndex);
            $columnCellStyle = $this->worksheet->getStyle($colCoordinate . ':' . $colCoordinate);

            if ($column->getType()) {
                $columnCellStyle->getNumberFormat()->setFormatCode(static::getFormatCode($column->getType()));
            }

            $columnCellStyle->applyFromArray($column->getStyles());

            $columnIndex++;
        }

        $headingCellStyle = $this->worksheet->getStyle("$startRow:$startRow");
        $headingCellStyle->getFont()->setBold(true);

        $this->appendRow(array_map(function (Column $column) {
            return $column->getTitle();
        }, $this->columns), 'A1');

        $this->worksheet->getRowDimension(1)->setRowHeight(24);
        $this->worksheet->freezePane('A2');

        // $this->worksheet->setSelectedCellByColumnAndRow(0, 0);
    }

    /**
     * 数据导出处理
     */
    protected function handleData()
    {
        $currentPage = 1;
        $chunkSize = $this->exportable->chunkSize();

        if ($chunkSize) { // 需要分块处理
            while (true) {
                $data = $this->exportable->data($currentPage);

                $flag = $this->handleDataItems($data);

                if (count($data) < $chunkSize || !$flag) {
                    break;
                }

                $currentPage += 1;
            }
        } else { // 不需要分块处理
            $data = $this->exportable->data();

            $this->handleDataItems($data);
        }
    }

    /**
     * 处理一组数据
     * @param array $data
     * @return bool
     */
    protected function handleDataItems($data)
    {
        foreach ($data as $index => $row) {
            $this->exportCount++;

            try {
                // 控制中断后续导出
                if ($row === false) {
                    return false;
                } elseif ($row === null) { // 跳过本次数据导出
                    continue;
                }

                $values = [];
                foreach ($this->columns as $column) {
                    if ($column->hasValueResolver()) {
                        $callback = $column->getValueResolver();
                        $value = call_user_func($callback, $row, $index, $data);
                    } else {
                        $name = $column->getKey();
                        $value = Arr::get($row, $name);
                    }
                    $values[] = $value;
                }

                $this->appendRow($values);
            } catch (\Throwable $e) {
                $this->exportErrorCount++;
                $this->lastExportError = $e;
            }
        }

        return true;
    }

    /**
     * 处理文档描述信息
     */
    protected function handleDocumentProperties()
    {
        $properties = $this->config['properties'];

        if ($this->exportable instanceof WithProperties) {
            $properties = array_merge($properties, $this->exportable->properties());
        }

        $props = $this->spreadsheet->getProperties();

        foreach (array_filter($properties) as $property => $value) {
            switch ($property) {
                case 'title':
                    $props->setTitle($value);

                    break;
                case 'description':
                    $props->setDescription($value);

                    break;
                case 'creator':
                    $props->setCreator($value);

                    break;
                case 'lastModifiedBy':
                    $props->setLastModifiedBy($value);

                    break;
                case 'subject':
                    $props->setSubject($value);

                    break;
                case 'keywords':
                    $props->setKeywords($value);

                    break;
                case 'category':
                    $props->setCategory($value);

                    break;
                case 'manager':
                    $props->setManager($value);

                    break;
                case 'company':
                    $props->setCompany($value);

                    break;
            }
        }
    }

    /**
     * 文档默认样式处理
     * @param mixed $exportObj
     * @param Spreadsheet $spreadsheet
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    protected function handleDocumentStyles($exportObj, $spreadsheet)
    {
        $defaultStyles = $this->config['styles'];

        if ($exportObj instanceof WithDefaultStyles) {
            $defaultStyles = array_merge($defaultStyles, $exportObj->defaultStyles());
        }

        $spreadsheet->getDefaultStyle()->applyFromArray($defaultStyles);
    }

    /**
     * 追加数据
     * @param array $row
     * @param null $startCell
     */
    protected function appendRow($row, $startCell = null)
    {
        return $this->appendRows([$row], $startCell);
    }

    /**
     * 追加数据（一组）
     * @param array[] $rows
     * @param null $startCell
     * @return Worksheet
     */
    protected function appendRows($rows, $startCell = null)
    {
        if (!$startCell) {
            $startCell = 'A' . ($this->worksheet->getHighestRow() + 1);
        }

        return $this->worksheet->fromArray($rows, null, $startCell, true);
    }

    /**
     * 写入到文件
     * @param string $filePath
     * @param string $writerType
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    protected function save($filePath, $writerType)
    {
        $writer = WriterFactory::make(
            $writerType,
            $this->spreadsheet,
            $this->exportable
        );

        $writer->save($filePath);

        $this->clearListeners();
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
        app(CacheManager::class)->flush();

        return $filePath;
    }

    /**
     * 获取单元格格式
     * @param string $type
     * @return string
     */
    public static function getFormatCode($type)
    {
        $codes = [
            'number' => NumberFormat::FORMAT_NUMBER,
            'integer' => NumberFormat::FORMAT_NUMBER,
            'int' => NumberFormat::FORMAT_NUMBER,
            'float' => NumberFormat::FORMAT_NUMBER_00,
            'double' => NumberFormat::FORMAT_NUMBER_00,
            'percentage' => NumberFormat::FORMAT_PERCENTAGE,
            'percentage_float' => NumberFormat::FORMAT_PERCENTAGE_00,
            'price' => '￥' . NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'RMB' => '￥' . NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'USD' => '$' . NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'date' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'datetime' => NumberFormat::FORMAT_DATE_YYYYMMDD . ' h:mm:ss',
        ];

        return $codes[$type] ?? NumberFormat::FORMAT_TEXT;
    }
}
