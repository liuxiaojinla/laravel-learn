<?php

namespace App\Services\Excel\Events;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AfterSheet extends Event
{
    /**
     * @var Worksheet
     */
    public $sheet;

    /**
     * @var object
     */
    private $exportable;

    /**
     * @param Worksheet $sheet
     * @param object $exportable
     */
    public function __construct(Worksheet $sheet, $exportable)
    {
        $this->sheet = $sheet;
        $this->exportable = $exportable;
    }

    /**
     * @return Worksheet
     */
    public function getSheet()
    {
        return $this->sheet;
    }

    /**
     * @return object
     */
    public function getConcernable()
    {
        return $this->exportable;
    }

    /**
     * @return mixed
     */
    public function getDelegate()
    {
        return $this->sheet;
    }
}
