<?php

namespace App\Services\ConfigCenter;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;

trait HasHttpRequests
{
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $httpMiddlewares = [];

    /**
     * @var \GuzzleHttp\HandlerStack
     */
    protected $httpHandlerStack;

    /**
     * @var array
     */
    protected static $httpClientDefaults = [
        'curl' => [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ],
    ];

    /**
     * Set guzzle default settings.
     *
     * @param array $defaults
     */
    public static function setHttpClientDefaultOptions($defaults = [])
    {
        self::$httpClientDefaults = $defaults;
    }

    /**
     * Return current guzzle default settings.
     */
    public static function getHttpClientDefaultOptions()
    {
        return self::$httpClientDefaults;
    }

    /**
     * @return ClientInterface
     */
    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @param ClientInterface $httpClient
     */
    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Add a middleware.
     *
     * @param callable $middleware
     * @param string|null $name
     *
     * @return $this
     */
    public function pushHttpMiddleware(callable $middleware, $name = null)
    {
        if (!is_null($name)) {
            $this->httpMiddlewares[$name] = $middleware;
        } else {
            $this->httpMiddlewares[] = $middleware;
        }

        return $this;
    }

    /**
     * Return all middlewares.
     */
    public function getHttpMiddlewares()
    {
        return $this->httpMiddlewares;
    }

    /**
     * @return $this
     */
    public function setHttpHandlerStack(HandlerStack $httpHandlerStack)
    {
        $this->httpHandlerStack = $httpHandlerStack;

        return $this;
    }

    /**
     * Build a handler stack.
     */
    public function getHttpHandlerStack()
    {
        if ($this->httpHandlerStack) {
            return $this->httpHandlerStack;
        }

        $this->httpHandlerStack = HandlerStack::create($this->getGuzzleHandler());

        foreach ($this->httpMiddlewares as $name => $middleware) {
            $this->httpHandlerStack->push($middleware, $name);
        }

        return $this->httpHandlerStack;
    }

    /**
     * Get guzzle handler.
     *
     * @return callable
     */
    protected function getGuzzleHandler()
    {
        if (property_exists($this, 'app') && isset($this->app['guzzle_handler'])) {
            return is_string($handler = $this->app->get('guzzle_handler'))
                ? new $handler()
                : $handler;
        }

        return Utils::chooseHandler();
    }

    /**
     * 优化请求参数
     * @param array $options
     * @return array
     */
    protected function optimizeHttpRequestOptions(array $options)
    {
        if (isset($options['json']) && is_array($options['json'])) {
            $options['headers'] = array_merge($options['headers'] ?? [], ['Content-Type' => 'application/json']);

            if (empty($options['json'])) {
                $options['body'] = json_encode($options['json'], JSON_FORCE_OBJECT);
            } else {
                $options['body'] = json_encode($options['json'], JSON_UNESCAPED_UNICODE);
            }

            unset($options['json']);
        }

        return $options;
    }

    /**
     * 发起请求
     *
     * @param string $uri
     * @param string $method
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpRequest($uri, $method = 'GET', $options = [])
    {
        $method = strtoupper($method);

        $options = array_merge(self::$httpClientDefaults, $options, ['handler' => $this->getHttpHandlerStack()]);

        $options = $this->optimizeHttpRequestOptions($options);

        if (property_exists($this, 'baseUri') && !is_null($this->baseUri)) {
            $options['base_uri'] = $this->baseUri;
        }

        $response = $this->getHttpClient()->request($method, $uri, $options);
        $response->getBody()->rewind();

        return $response;
    }

    /**
     * 发起 Get 请求
     * @param string $uri
     * @param array $query
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpGet($uri, array $query = [], $options = [])
    {
        return $this->httpRequest($uri, 'GET', array_merge_recursive($options, [
            'query' => $query,
        ]));
    }

    /**
     * 发起 Post 请求
     * @param string $uri
     * @param array $data
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpPost($uri, array $data = [], $options = [])
    {
        return $this->httpRequest($uri, 'POST', array_merge_recursive($options, [
            'form_params' => $data,
        ]));
    }

    /**
     * 发起 Post Json 请求
     * @param string $uri
     * @param array $data
     * @param array $query
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpPostJson($uri, array $data = [], array $query = [], $options = [])
    {
        return $this->httpRequest($uri, 'POST', array_merge_recursive($options, [
            'query' => $query,
            'json' => $data,
        ]));
    }

    /**
     * 上传文件
     * @param string $uri
     * @param array $files
     * @param array $form
     * @param array $query
     * @param array $options
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpUpload($uri, array $files = [], array $form = [], array $query = [], $options = [])
    {
        $multipart = [];

        foreach ($files as $name => $path) {
            $multipart[] = is_array($path)
                ? array_merge($path, [
                    'name' => $name,
                ])
                : [
                    'name' => $name,
                    'contents' => fopen($path, 'r'),
                ];
        }

        foreach ($form as $name => $contents) {
            $multipart[] = compact('name', 'contents');
        }

        return $this->httpRequest($uri, 'POST', array_merge_recursive([
            'connect_timeout' => 30,
            'timeout' => 30,
            'read_timeout' => 30,
        ], $options, [
            'query' => $query,
            'multipart' => $multipart,
        ]));
    }
}
