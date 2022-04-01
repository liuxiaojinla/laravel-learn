<?php

namespace App\Events\Orders;

use App\Models\Order\Order;

class Created
{
    /**
     * @var \App\Models\Order\Order
     */
    public Order $order;

    /**
     * @param \App\Models\Order\Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}
