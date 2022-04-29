<?php

namespace App\Contracts\Pay;

interface PayRefundProvider
{

    public function make(array $attributes);
}