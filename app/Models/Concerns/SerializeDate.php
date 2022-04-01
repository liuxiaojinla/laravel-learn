<?php

namespace App\Models\Concerns;

use DateTimeInterface;

trait SerializeDate
{
    /**
     * @inheritDoc
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
