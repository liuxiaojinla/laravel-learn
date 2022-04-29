<?php

namespace App\Services\Pay;

use App\Contracts\Pay\PayOrderProvider as PayOrderProviderContract;

class PayOrderProvider implements PayOrderProviderContract
{

    public function make(array $attributes)
    {
        // TODO: Implement make() method.
    }

    public function retrieveByOutTradeNo($outTradeNo)
    {
        // TODO: Implement retrieveByOutTradeNo() method.
    }
}