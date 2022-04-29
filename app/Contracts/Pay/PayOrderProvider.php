<?php

namespace App\Contracts\Pay;

interface PayOrderProvider
{
    /**
     * @param array $attributes
     * @return mixed
     */
    public function make(array $attributes);

    public function retrieveByOutTradeNo($outTradeNo);
}