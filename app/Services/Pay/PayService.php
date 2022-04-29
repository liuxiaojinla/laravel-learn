<?php

namespace App\Services\Pay;

use App\Contracts\Pay\PayOrderProvider;
use App\Services\Pay\Gateways\WechatGateway;

class PayService
{
    /**
     * @var \App\Contracts\Pay\PayOrderProvider
     */
    protected $payOrderProvider;

    /**
     * @param \App\Contracts\Pay\PayOrderProvider $payOrderProvider
     */
    public function __construct(PayOrderProvider $payOrderProvider)
    {
        $this->payOrderProvider = $payOrderProvider;
    }

    public function pay($userId, $amount, $outTradeNo, $attributes = [])
    {
        $payOrder = $this->payOrderProvider->make(array_replace_recursive(
            [
                'user_id' => $userId,
                'amount' => $amount,
                'out_trade_no' => $outTradeNo,
            ], $attributes
        ));

        $result = $this->gateway()->pay($attributes);
    }

    private function gateway()
    {
        return new WechatGateway();
    }


}