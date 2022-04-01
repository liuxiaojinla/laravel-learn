<?php

namespace App\Services\Excel;

class TableImport implements Importable
{

    /**
     * @var Column[]
     */
    protected $columns;

    /**
     * @var callable
     */
    protected $eachCallback;

    /**
     * @var int
     */
    protected $startRow = 1;

    /**
     * @var int
     */
    protected $limit = 5000;

    /**
     * @var string
     */
    protected $endColumn = null;

    /**
     * @var bool
     */
    protected $isSkipsEmptyRows = true;

    /**
     * @param Column[] $columns
     * @param callable $eachCallback
     */
    public function __construct(array $columns, callable $eachCallback)
    {
        $this->columns = $columns;
        $this->eachCallback = $eachCallback;
    }

    /**
     * @inheritDoc
     */
    public function onRow(Row $row)
    {
        if ($this->eachCallback) {
            call_user_func($this->eachCallback, $row);
        }
    }

    /**
     * @inheritDoc
     */
    public function startRow()
    {
        return $this->startRow;
    }

    /**
     * @inheritDoc
     */
    public function limit()
    {
        return $this->limit;
    }

    /**
     * @inheritDoc
     */
    public function isSkipsEmptyRows()
    {
        return $this->isSkipsEmptyRows;
    }

    /**
     * @inheritDoc
     */
    public function endColumn()
    {
        return $this->endColumn;
    }
}
