<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    dump('make url:' . plugin_url('index/index'));

    return 'Hello Plugin Admin';
});

Route::get('path', function (Request $request) {
    dump('module path:' . $request->modulePath());
    dump('plugin path:' . $request->pluginPath());
});
