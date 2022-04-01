<?php

namespace App\Contracts\Config;

interface Factory
{
    /**
     * @param string $name
     * @return Repository
     */
    public function repository($name): Repository;
}