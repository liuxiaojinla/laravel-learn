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

use Illuminate\Support\Facades\Route;

//首页
Route::get('/', function(){
	return view('welcome');
});
Route::get('login', 'Auth\\LoginController@login')->name('login');
Route::get('register', 'Auth\\RegisterController@register')->name('register');

//简单的get请求的路由
Route::get('hello', function(){
	return 'hello world';
});

//匹配多个请求的路由
Route::match(['get', 'post'], 'haha', function(){
	return "我支持get 和 post请求 ， 不信的话，你可以试试";
});

//支持所有请求的路由
Route::any('world', function(){
	return "hello,Where are you?";
});

//视图路由
Route::view('view', 'welcome');

//路由301跳转
Route::redirect('welcome', 'view');

//路由参数
Route::get('user/{id}', function($id){
	return 'hello '.$id;
});

//命名路由
Route::get('user/profile', function(){
	// 通过路由名称生成 URL
	return 'my url: '.route('profile');
})->name('profile');
Route::get('user/profile', 'UserController@showProfile')->name('profile');

//路由分组
Route::prefix('ucenter')
	 ->middleware([])
	 ->namespace('Ucenter')
	 ->group(function(){
		 Route::get('/', function(){
			 return "个人中心";
		 });
		 Route::get('/profile', function(){
			 return "个人资料";
		 });
	 });

//子域名路由
Route::domain('{account}.blog.dev')->group(function(){
	Route::get('user/{id}', function($account, $id){
		return 'This is '.$account.' page of User '.$id;
	});
});