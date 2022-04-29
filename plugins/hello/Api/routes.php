<?php

use Illuminate\Support\Facades\Route;
use Plugins\hello\Api\Controllers\IndexController;

Route::get('/', [IndexController::class, 'index']);
