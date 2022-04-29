<?php

namespace App\Services\Pay;

use App\Services\Pay\Concerns\HasBuildPayOrderProvider;
use App\Services\Pay\Concerns\HasBuildPayRefundProvider;

class RefundBuilder
{
    use HasBuildPayOrderProvider, HasBuildPayRefundProvider;

    /**
     * @return \App\Services\Pay\RefundService
     */
    public function build()
    {
        $payOrderProvider = $this->resolverPayOrderProvider();
        $payRefundProvider = $this->resolverPayRefundProvider();

        return new RefundService($payOrderProvider, $payRefundProvider);
    }
}