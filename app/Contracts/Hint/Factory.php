<?php

namespace App\Contracts\Hint;

interface Factory
{

    /**
     * @param string|null $name
     * @return Hint
     */
    public function hint($name = null);

    /**
     * @param string $name
     */
    public function shouldUse($name);
}