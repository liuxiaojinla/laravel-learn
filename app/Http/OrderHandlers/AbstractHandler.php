<?php

namespace App\Http\OrderHandlers;

use App\Contracts\Middleware\Handler;
use App\Models\Order\Order;
use App\Services\Order\OrderBuilder;

abstract class AbstractHandler implements Handler
{
    /**
     * @var OrderBuilder
     */
    protected $builder;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var Order
     */
    protected $response;

    /**
     * @param OrderBuilder $builder
     */
    public function __construct(OrderBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param array $payload
     * @param callable $next
     * @return mixed
     */
    public function __invoke($payload, $next)
    {
        $this->payload = $payload;

        $this->handle();

        $this->response = $next($this->payload);

        $this->response();

        return $this->response;
    }

    /**
     * 数据输入处理
     */
    protected function handle()
    {
    }

    /**
     * 数据响应处理
     */
    protected function response()
    {
    }

    /**
     * @return bool
     */
    protected function isPreview()
    {
        return $this->builder->isPreview();
    }
}
