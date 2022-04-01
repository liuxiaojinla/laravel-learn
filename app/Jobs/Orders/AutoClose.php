<?php

namespace App\Jobs\Orders;

use App\Models\Order\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoClose implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    protected $orderId;

    /**
     * @var Order
     */
    protected $order;

    /**
     * @param int $orderId
     */
    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->loadOrder()) {
            return;
        }

        $this->order->update([
            'status' => 1,
        ]);
    }

    protected function loadOrder()
    {
        $this->order = Order::query()->where('id', $this->orderId)->first();

        return $this->order != null;
    }
}
