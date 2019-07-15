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

Route::resource('posts', 'PostsController');

Route::prefix('manager')
	->name('manager.')
	->namespace('\App\Http\Controllers\Managers')
	->group(function(){
		Route::resource('categorys', 'CategorysController');
	});
