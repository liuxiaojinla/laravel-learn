<?php

namespace App\Services\Excel;

interface Importable
{

    /**
     * @param Row $row
     */
    public function onRow(Row $row);

    /**
     * @return int
     */
    public function startRow();

    /**
     * @return int
     */
    public function limit();

    /**
     * @return bool
     */
    public function isSkipsEmptyRows();

    /**
     * @return string
     */
    public function endColumn();
}
