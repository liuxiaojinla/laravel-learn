<?php

namespace App\Services\Pay\Concerns;

use App\Contracts\Pay\PayRefundProvider as PayRefundProviderContract;
use App\Services\Pay\PayRefundProvider;

trait HasBuildPayRefundProvider
{
    /**
     * @var PayRefundProviderContract
     */
    protected $payRefundProvider;

    /**
     * @return PayRefundProviderContract
     */
    protected function resolverPayRefundProvider()
    {
        return $this->payRefundProvider ?: new PayRefundProvider();
    }

    /**
     * @return PayRefundProviderContract
     */
    public function getPayRefundProvider()
    {
        return $this->payRefundProvider ?: $this->resolverPayRefundProvider();
    }

    /**
     * @param PayRefundProviderContract $payRefundProvider
     * @return $this
     */
    public function setPayRefundProvider(PayRefundProviderContract $payRefundProvider)
    {
        $this->payRefundProvider = $payRefundProvider;

        return $this;
    }
}