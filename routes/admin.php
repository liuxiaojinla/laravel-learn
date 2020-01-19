<?php
/**
 * The following code, none of which has BUG.
 *
 * @author: BD<657306123@qq.com>
 * @date: 2020/1/18 15:14
 */
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('', 'IndexController@index')->name('admin');

Auth::routes([
    'register' => false,
]);

Route::fallback('IndexController@index');
