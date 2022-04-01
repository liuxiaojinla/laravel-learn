<?php

namespace App\Services\Excel;

class TableExport implements Exportable
{
    /**
     * @var Column[]
     */
    protected $columns;

    /**
     * @var \iterable
     */
    protected $data;

    /**
     * @param Column[] $columns
     * @param iterable $data
     */
    public function __construct(array $columns, iterable $data)
    {
        $this->columns = $columns;
        $this->data = $data;
    }

    /**
     * @inerhitDoc
     */
    public function columns()
    {
        return $this->columns;
    }

    /**
     * @inerhitDoc
     */
    public function data($page = 1)
    {
        return $this->data;
    }

    /**
     * @inerhitDoc
     */
    public function chunkSize()
    {
        return 0;
    }
}
