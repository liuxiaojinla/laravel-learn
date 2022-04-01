<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', [HomeController::class, 'index']);
Route::get('/success', [HomeController::class, 'showSuccess']);
Route::get('/error', [HomeController::class, 'showError']);
Route::get('/repository/{action}', \App\Http\Controllers\Testing\RepositoryController::class);
Route::get('/canvas/{action}', \App\Http\Controllers\Testing\CanvasController::class);

Route::prefix('order')->group(base_path('routes/apis/order.php'));

Route::prefix('user')
    ->middleware('auth')
    ->group(base_path('routes/apis/user.php'));

Route::prefix('config')
    ->group(base_path('routes/apis/config.php'));