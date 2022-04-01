<?php

namespace App\Services\Plugin;

use App\Contracts\Plugin\Plugin;

abstract class AbstractPlugin implements Plugin
{
    protected $info = [];

    /**
     * @inheritDoc
     */
    public function boot()
    {
    }

    /**
     * @inheritDoc
     */
    public function upgrade()
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    public function getInfo()
    {
        return $this->info;
    }
}