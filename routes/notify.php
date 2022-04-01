<?php
use App\Http\Controllers\Notify\WechatOfficialController;
use App\Http\Controllers\Notify\WechatOpenWorkController;
use App\Http\Controllers\Notify\WechatWorkController;
use Illuminate\Support\Facades\Route;

Route::any('/official', WechatOfficialController::class);
Route::any('/work', WechatWorkController::class);
Route::any('/work/{corpId}/{agentId}', WechatWorkController::class);
Route::any('/openwork', WechatOpenWorkController::class);