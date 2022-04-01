<?php

use App\Services\Uploader\FileDataProvider;

return [
    'defaults' => [
        // 默认上传器
        'scene' => env('UPLOADER_DRIVER', 'image'),

        // cdn
        'cdn' => '',

        // 默认驱动器
        'disk' => 'qiniu',

        // 数据提供者
        'provider' => FileDataProvider::class,
    ],

    // 场景
    'scenes' => [
        'image' => [
            'driver' => 'qiniu',
            'disk' => 'qiniu',
            'size' => 2 * 1024,
            'extensions' => ['png', 'jpeg', 'jpg', 'gif', 'bmp'],
            'mimes' => 'image/*',
        ],

        'file' => [
            'driver' => 'default',
            'disk' => 'qiniu',
            'size' => 10 * 1024,
            'extensions' => ['zip',],
            'mimes' => '*/*',
        ],

        'doc' => [
            'driver' => 'default',
            'disk' => 'qiniu',
            'size' => 10 * 1024,
            'extensions' => ['doc', 'docx', 'xls', 'xlsx', 'pdf'],
            'mimes' => '*/*',
        ],

        'audio' => [
            'driver' => 'default',
            'disk' => 'qiniu',
            'size' => 10 * 1024,
            'extensions' => ['mp3',],
            'mimes' => 'audio/*',
        ],

        'video' => [
            'driver' => 'default',
            'size' => 20 * 1024,
            'extensions' => ['mp4',],
            'mimes' => 'video/*',
        ],
    ],

    // 场景别名
    'aliases' => [
        'img' => 'image',
    ],
];