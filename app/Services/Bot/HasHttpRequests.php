<?php

namespace App\Services\Bot;

use Illuminate\Support\Facades\Http;

trait HasHttpRequests
{
    /**
     * 发送HttpPost请求
     * @param string $url
     * @param array $data
     * @param array $options
     * @return \Illuminate\Http\Client\Response
     */
    public function httpPostJson(string $url, array $data = [], array $options = []): \Illuminate\Http\Client\Response
    {
        return Http::post($url, $data);
    }
}