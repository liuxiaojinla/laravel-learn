<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

// 博客文章
Route::get('posts', 'PostController@index');
Route::get('posts/{id}', 'PostController@show');

// 博客分类
Route::get('categories', 'CategoryController@index');
Route::get('categories/{id}', 'CategoryController@show');

// 个人中心
Route::name('user.')
    ->prefix('user')
    //    ->namespace('\App\Http\Controllers\User')
    //    ->group(base_path('routes/web/user.php'))
    ->group(function(){
        Route::get('index', 'UserController@show');
        Route::get('collect/categories', 'CategoryController@myCollect');
        Route::get('collect/posts', 'PostController@myCollect');
    });
