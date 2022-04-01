<?php

namespace App\Services\ConfigCenter;

use GuzzleHttp\Client;

class EtcdRepository extends Repository
{
    use HasHttpRequests {
        httpRequest as performRequest;
    }

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $httpClientConfig = $config['http_client'] ?? [];
        if (isset($httpClientConfig['base_uri'])) {
            $httpClientConfig['base_uri'] = rtrim($httpClientConfig['base_uri'], '/') . '/';
        }

        $this->setHttpClient(new Client($httpClientConfig));
    }

    /**
     * @inheritDoc
     */
    protected function httpRequest($uri, $method = 'GET', $options = [])
    {
        $response = $this->performRequest($uri, $method, $options);

        return json_decode($response->getBody()->getContents(), true);
    }


    public function has($key)
    {
        // TODO: Implement has() method.
    }

    public function get($key, $default = null)
    {
        $result = $this->httpPostJson('v3/kv/range', [
            'key' => base64_encode($key),
        ]);

        return $this->formatKVs($result['kvs'] ?? []);
    }

    public function set($key, $value, $isMerge = true)
    {
        return $this->httpPostJson('v3/kv/range', $this->encode([
            'key' => $key,
            'value' => $value,
        ]));
    }

    protected function formatKVs($kvs)
    {
        $data = [];

        foreach ($kvs as $item) {
            $key = base64_decode($item['key']);
            $value = base64_decode($item['value']);

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * string类型key用base64编码
     *
     * @return array
     */
    protected function encode(array $data)
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = base64_encode($value);
            }
        }

        return $data;
    }

    /**
     * @param string $key
     * @return bool|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function forget($key)
    {
        return $this->httpPostJson('v3/kv/deleterange', $this->encode([
            'key' => $key,
        ]));
    }

    public function all()
    {
        // TODO: Implement all() method.
    }
}