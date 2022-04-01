<?php

namespace App\Services\Excel\Concerns;

interface WithDefaultStyles
{
    /**
     * 默认样式
     * @return array[]
     */
    public function defaultStyles();
}
