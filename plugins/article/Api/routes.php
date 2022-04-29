<?php

use Illuminate\Support\Facades\Route;
use Plugins\article\Api\Controllers\IndexController;

Route::get('', [IndexController::class, 'index']);
