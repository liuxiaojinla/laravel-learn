<?php

use App\Http\Controllers\Testing\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('pre_create_info', [OrderController::class, 'fromGoods']);
Route::get('task', function () {
    foreach (range(0, 10100) as $v) {
        app()->call([app(OrderController::class), 'fromGoods']);
    }
});
