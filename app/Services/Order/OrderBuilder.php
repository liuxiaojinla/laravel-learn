<?php

namespace App\Services\Order;

use App\Models\Order\Order;
use App\Services\Middleware\MiddlewareManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderBuilder
{
    /**
     * @var MiddlewareManager
     */
    protected $middlewareManager;

    /**
     * @var bool
     */
    protected $preview = false;

    /**
     * @var mixed
     */
    protected $user;

    /**
     * @var Collection
     */
    protected $orderGoodsList;

    /**
     * @var array
     */
    protected $order = [];

    /**
     * @var int
     */
    protected $goodsId;

    /**
     * @var int
     */
    protected $goodsSkuId;

    /**
     * @var array
     */
    protected $cartIds;

    /**
     *
     */
    public function __construct()
    {
        $this->middlewareManager = new MiddlewareManager();
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int
     */
    public function goodsId()
    {
        return $this->goodsId;
    }

    /**
     * @return int
     */
    public function goodsSkuId()
    {
        return $this->goodsSkuId;
    }

    /**
     * @param int $goodsId
     * @param int $goodsSkuId
     * @return OrderBuilder
     */
    public function setGoodsId($goodsId, $goodsSkuId = null)
    {
        $this->goodsId = $goodsId;
        $this->goodsSkuId = $goodsSkuId;

        return $this;
    }

    /**
     * @return array
     */
    public function cartIds()
    {
        return $this->cartIds;
    }

    /**
     * @param array $cartIds
     * @return $this
     */
    public function setCartIds(array $cartIds)
    {
        $this->cartIds = $cartIds;

        return $this;
    }

    /**
     * @return Collection
     */
    public function orderGoodsList()
    {
        return $this->orderGoodsList;
    }

    /**
     * @param Collection $orderGoodsList
     * @return $this
     */
    public function setOrderGoodsList(Collection $orderGoodsList)
    {
        $this->orderGoodsList = $orderGoodsList;

        return $this;
    }

    /**
     * @return float
     */
    public function goodsTotalPrice()
    {
        return $this->orderGoodsList()->reduce(function ($result, $item) {
            return $result + $item['total_price'];
        }, 0);
    }

    /**
     * @return array
     */
    public function order()
    {
        if (!$this->order) {
            $order = new Order();
            $order->user_id = $this->user['id'];
            $order->total_price = $this->goodsTotalPrice();
            $order->pay_price = $this->goodsTotalPrice();
            $this->order = $order;
        }

        return $this->order;
    }

    /**
     * @return bool
     */
    public function isPreview()
    {
        return $this->preview;
    }

    /**
     * @param bool $preview
     * @return $this
     */
    public function preview($preview = true)
    {
        $this->preview = $preview;

        return $this;
    }

    /**
     * @param callable $handler
     * @return $this
     */
    public function addHandler($handler)
    {
        $this->middlewareManager->push($handler);

        return $this;
    }

    /**
     * @param array $handlers
     * @return $this
     */
    public function addHandlers(array $handlers)
    {
        foreach ($handlers as $handler) {
            if (!is_callable($handler)) {
                $handler = new $handler($this);
            }

            $this->addHandler($handler);
        }

        return $this;
    }

    /**
     * @param Order $order
     * @return Order
     */
    protected function optimize(Order $order)
    {
        if ($order['total_price'] < 0) {
            $order['total_price'] = 0;
        }

        if ($order['pay_price'] < 0) {
            $order['pay_price'] = 0;
        }

        return $order;
    }

    /**
     * @param array|null $payload
     * @return Order
     */
    public function build($payload = null)
    {
        $this->validate($payload);

        if ($this->preview) {
            return $this->middlewareManager->then($payload, function () {
                $order = $this->optimize($this->order());
                $order->order_goods_list = $this->orderGoodsList;

                return $order;
            });
        }

        $order = DB::transaction(function () use ($payload) {
            return $this->middlewareManager->then($payload, function () {
                $order = $this->optimize($this->order());

                $order->order_no = Str::uuid()->toString();
                $order->save();

                $order->orderGoodsList()->saveMany($this->orderGoodsList);
                $order->order_goods_list = $this->orderGoodsList;

                event('order.creating', $order);

                return $order;
            });
        });

        return tap($order, function ($order) {
            event('order.create', $order);
        });
    }

    /**
     * @param array $payload
     * @return void
     */
    public function validate($payload)
    {
        foreach ($this->middlewareManager->get('global') as $handler) {
            if ($handler instanceof OrderValidate || method_exists($handler, 'validate')) {
                $handler->validate($payload);
            }
        }
    }
}
