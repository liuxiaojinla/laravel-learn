<?php

namespace App\Services\Excel;

interface Exportable
{
    /**
     * @return Column[]
     */
    public function columns();

    /**
     * @return array|iterable
     */
    public function data($page = 1);

    /**
     * @return int
     */
    public function chunkSize();
}
