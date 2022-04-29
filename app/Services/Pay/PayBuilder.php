<?php

namespace App\Services\Pay;

use App\Services\Pay\Concerns\HasBuildPayOrderProvider;

class PayBuilder
{
    use HasBuildPayOrderProvider;

    /**
     * @return \App\Services\Pay\PayService
     */
    public function build()
    {
        $payOrderProvider = $this->resolverPayOrderProvider();

        return new PayService($payOrderProvider);
    }
}