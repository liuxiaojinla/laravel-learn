<?php

namespace App\Services\Order;

interface OrderValidate
{

    /**
     * @param mixed $payload
     */
    public function validate($payload);
}