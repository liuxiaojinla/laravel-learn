<?php

namespace App\Http\OrderHandlers;

use Illuminate\Support\Arr;

class CouponHandler extends AbstractHandler
{
    protected function handle()
    {
        $couponId = Arr::get($this->payload, 'coupon_id', 0);
        if (!$couponId) {
            // return;
        }

        $couponDiscount = bcdiv(rand(70, 100), 100, 2);
        $goodsTotalPrice = $this->builder->goodsTotalPrice();

        $couponPrice = bcmul($goodsTotalPrice, $couponDiscount, 2);
        $couponPrice = bcsub($goodsTotalPrice, $couponPrice, 2);

        $order = $this->builder->order();
        $order['coupon_price'] = $couponPrice;
        $order['pay_price'] = bcsub($order['pay_price'], $couponPrice);
    }

    protected function response()
    {
    }
}
