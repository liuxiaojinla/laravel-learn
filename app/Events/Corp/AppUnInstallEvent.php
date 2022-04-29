<?php

namespace App\Events\Corp;

class AppUnInstallEvent
{
    /**
     * @var string
     */
    public $corpId;

    /**
     * @param string $corpId
     */
    public function __construct(string $corpId)
    {
        $this->corpId = $corpId;
    }
}
