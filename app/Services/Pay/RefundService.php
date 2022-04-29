<?php

namespace App\Services\Pay;

use App\Contracts\Pay\PayOrderProvider as PayOrderProviderContract;
use App\Contracts\Pay\PayRefundProvider as PayRefundProviderContract;

class RefundService
{
    /**
     * @var PayOrderProviderContract
     */
    protected $payOrderProvider;

    /**
     * @var PayRefundProviderContract
     */
    protected $payRefundProvider;

    /**
     * @param PayOrderProviderContract $payOrderProvider
     * @param PayRefundProviderContract $payRefundProvider
     */
    public function __construct(PayOrderProviderContract $payOrderProvider, PayRefundProviderContract $payRefundProvider)
    {
        $this->payOrderProvider = $payOrderProvider;
        $this->payRefundProvider = $payRefundProvider;
    }


    public function refund($outTradeNo, $refundAmount, $attributes = [])
    {
        $payOrder = $this->payOrderProvider->retrieveByOutTradeNo($outTradeNo);

        $payRefund = $this->payRefundProvider->make(array_replace_recursive(
            [
                'user_id' => $userId,
                'amount' => $amount,
                'refund_amount' => $refundAmount,
                'out_trade_no' => $outTradeNo,
            ], $attributes
        ));
    }
}