<?php
return [
    'defaults' => [
        'bot' => 'default',
    ],

    'bots' => [
        'default' => [
            'driver' => 'qywork',
            'key' => 'cbf66d70-1fe8-4c57-b5a8-a7ba9728b3f6',
        ],
        'primary' => [
            'driver' => 'dingtalk',
            'token' => '222222222222222',
        ],
        'danger' => [
            'driver' => 'dingtalk',
            'token' => '222222222222222333333333',
            'mentioned' => ['liuxingwu'],
        ],
    ],
];