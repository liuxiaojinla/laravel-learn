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

Route::get('/', function(){
	return view('welcome', [
		'title' => '欢迎使用Laravel',
		'tips'  => '<strong>Laravel</strong>使用与练习',
		'jobs'  => [
			'task1',
			'task2',
			'task3',
		],
	]);
});

Route::get('users', function(){
	return "user list.";
});

Route::get('users/profile', function(){
	//
	return 'user info';
})->name('profile');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
