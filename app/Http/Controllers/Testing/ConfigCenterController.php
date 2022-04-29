<?php

namespace App\Http\Controllers\Testing;

use App\Services\ConfigCenter\ConfigCenterManager;

class ConfigCenterController
{
    public function index()
    {
        $manager = new ConfigCenterManager([
            'defaults' => [
                'driver' => 'etcdv3',
            ],
            'drivers' => [
                'remote' => [
                    'driver' => 'remote',
                    'http_client' => [
                        'base_uri' => 'http://laravel8.test.com/api/config',
                        'http_errors' => true,
                    ],
                ],
                'etcdv3' => [
                    'driver' => 'etcdv3',
                    'http_client' => [
                        'base_uri' => 'http://127.0.0.1:2379',
                        'http_errors' => false,
                    ],
                ],
            ],
        ]);


        // dump($manager->all());
        dump($manager->set('foo1', 'world' . time()));
        dump($manager->get('foo1'));
        // dump($manager->has('hello'));
        dump($manager->forget('foo1'));
    }
}