<?php

namespace App\Http\Controllers\Testing;

use App\Http\Controllers\Controller;
use App\Http\OrderHandlers\AutoCloseHandler;
use App\Http\OrderHandlers\CouponHandler;
use App\Models\Order\OrderGoods;
use App\Models\User;
use App\Services\Order\OrderBuilder;
use Faker\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use function config;

class OrderController extends Controller
{
    /**
     * @var string[]
     */
    protected $handlers = [
        CouponHandler::class,
        AutoCloseHandler::class,
    ];

    /**
     * @param Request $request
     * @return \App\Models\Order\Order
     */
    public function fromGoods(Request $request)
    {
        $faker = Factory::create(config('app.faker_locale'));
        $orderGoodsList = new Collection();
        foreach (range(0, rand(2, 10)) as $v) {
            $orderGoodsList->push(new OrderGoods([
                'goods_title' => $faker->paragraph(1),
                'total_price' => rand(5, 50),
                'goods_price' => rand(5, 10),
                'goods_num' => rand(1, 10),
            ]), );
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->build($orderGoodsList);
        }

        return $this->build($orderGoodsList);
    }

    /**
     * @param Collection $orderGoodsList
     * @return \App\Models\Order\Order
     */
    protected function build(Collection $orderGoodsList)
    {
        $builder = $this->makeOrderBuilder($orderGoodsList);

        return $builder->build();
    }

    /**
     * @param Collection $orderGoodsList
     * @return \App\Models\Order\Order
     */
    protected function preview(Collection $orderGoodsList)
    {
        $service = $this->makeOrderBuilder($orderGoodsList);

        return $service->preview()->build();
    }

    /**
     * @param Collection $orderGoodsList
     * @return OrderBuilder
     */
    protected function makeOrderBuilder(Collection $orderGoodsList)
    {
        $builder = new OrderBuilder();
        $builder->setOrderGoodsList($orderGoodsList);
        $builder->setUser($this->request->user() ?: User::query()->first());
        $builder->addHandlers($this->handlers);

        return $builder;
    }
}
