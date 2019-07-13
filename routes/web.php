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

Route::resource('/posts', 'PostsController');

//Route::get('/', function(){
//	return view('welcome', [
//		'title' => '欢迎使用Laravel',
//		'tips'  => '<strong>Laravel</strong>使用与练习',
//		'jobs'  => [
//			'task1',
//			'task2',
//			'task3',
//		],
//	]);
//});
