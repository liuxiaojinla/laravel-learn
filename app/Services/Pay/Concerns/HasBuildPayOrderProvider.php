<?php

namespace App\Services\Pay\Concerns;

use App\Contracts\Pay\PayOrderProvider as PayOrderProviderContract;
use App\Services\Pay\PayOrderProvider;
use App\Services\Pay\PayService;

trait HasBuildPayOrderProvider
{
    /**
     * @var PayOrderProviderContract
     */
    protected $payOrderProvider;



    /**
     * @return \App\Services\Pay\PayOrderProvider
     */
    protected function resolverPayOrderProvider()
    {
        return $this->payOrderProvider ?: new PayOrderProvider();
    }

    /**
     * @return \App\Contracts\Pay\PayOrderProvider
     */
    public function getPayOrderProvider()
    {
        return $this->payOrderProvider ?: $this->resolverPayOrderProvider();
    }

    /**
     * @param \App\Contracts\Pay\PayOrderProvider $payOrderProvider
     * @return $this
     */
    public function setPayOrderProvider(PayOrderProviderContract $payOrderProvider)
    {
        $this->payOrderProvider = $payOrderProvider;

        return $this;
    }
}