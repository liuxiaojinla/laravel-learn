<?php

use App\Http\Controllers\Auth\WechatWorkController;
use App\Http\Controllers\Transfer\WechatWorkController as TransferWechatWorkController;
use Illuminate\Support\Facades\Route;

Route::get('/transfer/wechat_work', [TransferWechatWorkController::class, 'go']);
Route::get('/transfer/wechat_work/{authCode}', [TransferWechatWorkController::class, 'redirect']);

Route::get('/wechat_work', WechatWorkController::class);