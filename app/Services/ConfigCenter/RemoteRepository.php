<?php

namespace App\Services\ConfigCenter;

use GuzzleHttp\Client;

class RemoteRepository extends Repository
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

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        $response = $this->httpGet('has', [
            'key' => $key,
        ]);

        return $response['exists'];
    }

    /**
     * @inheritDoc
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($key, $default = null)
    {
        return $this->httpGet('get', [
            'key' => $key,
        ]);
    }

    /**
     * @inheritDoc
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function set($key, $value, $isMerge = true)
    {
        return $this->httpPostJson('set', [
            'key' => $key,
            'value' => $value,
            'merge' => $isMerge,
        ]);
    }

    /**
     * @inheritDoc
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function all()
    {
        return $this->httpGet('all');
    }

    /**
     * @inheritDoc
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function forget($key)
    {
        $response = $this->httpPostJson('remove', [
            'key' => $key,
        ]);

        return $response != null;
    }
}
