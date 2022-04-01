<?php

use App\Exceptions\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('all', function (Request $request) {
    return Redis::hgetall('config');
});

Route::get('get', function (Request $request) {
    $key = $request->get('key');
    if (empty($key)) {
        throw ValidationException::withMessage('key is invalid.');
    }
    $value = Redis::hget('config', $key);

    return [
        'key' => $key,
        'value' => $value,
    ];
});

Route::get('gets', function (Request $request) {
    $keys = $request->get('keys');
    if (empty($keys)) {
        throw ValidationException::withMessage('key is invalid.');
    }

    $keys = explode(',', $keys);
    $values = Redis::hmget('config', $keys);

    return [
        'keys' => $keys,
        'values' => $values,
    ];
});

Route::get('has', function (Request $request) {
    $key = $request->get('key');
    if (empty($key)) {
        throw ValidationException::withMessage('key is invalid.');
    }

    $value = Redis::hexists('config', $key);

    return [
        'key' => $key,
        'exists' => $value,
    ];
});

Route::post('set', function (Request $request) {
    $key = $request->post('key');
    $value = $request->post('value');

    Redis::hset('config', $key, $value);

    return [
        'key' => $key,
        'value' => $value,
    ];
});

Route::post('remove', function (Request $request) {
    $key = $request->post('key');

    Redis::hdel('config', $key);

    return [
        'key' => $key,
    ];
});