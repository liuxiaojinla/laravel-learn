<?php

use Illuminate\Support\Facades\Route;
use Plugins\article\Web\Controllers\IndexController;

Route::get('/', [IndexController::class, 'index']);
Route::get('/detail/{id}', [IndexController::class, 'detail']);
