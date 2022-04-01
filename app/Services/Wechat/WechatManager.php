<?php

namespace App\Services\Wechat;

use App\Contracts\Wechat\Factory as WechatFactory;
use App\Services\Wechat\Work\Client\ContactWayClient;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\ServiceContainer;
use Illuminate\Support\Arr;
use Monolog\Logger as Monolog;

class WechatManager implements WechatFactory
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @param array|null $config
     */
    public function __construct(array $config = null)
    {
        $this->config = $config === null ? config('wechat') : $config;
    }

    /**
     * @inheritDoc
     * @throws WechatInvalidConfigException
     */
    public function miniProgram(): \EasyWeChat\MiniProgram\Application
    {
        $application = Factory::miniProgram($this->getConfig('mini_program'));

        return $this->initApplication($application);
    }

    /**
     * @inheritDoc
     */
    public function hasMiniProgram(): bool
    {
        return isset($this->config['mini_program']);
    }

    /**
     * @inheritDoc
     * @throws WechatInvalidConfigException
     */
    public function official(): \EasyWeChat\OfficialAccount\Application
    {
        $application = Factory::officialAccount($this->getConfig('official'));

        return $this->initApplication($application);
    }

    /**
     * @inheritDoc
     */
    public function hasOfficial(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function openPlatform(): \EasyWeChat\OpenPlatform\Application
    {
        $application = Factory::openPlatform($this->getConfig('open_platform'));

        return $this->initApplication($application);
    }

    /**
     * @inheritDoc
     */
    public function hasOpenPlatform(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     * @throws WechatInvalidConfigException
     */
    public function work($name = null): \EasyWeChat\Work\Application
    {
        $name = $name ?: $this->getDefault();
        $config = $this->getConfig("works.{$name}");

        $app = Factory::work($config);
        $app['contact_way'] = function ($app) {
            return new ContactWayClient($app);
        };

        $app = $this->initApplication($app);

        return $app;
    }

    /**
     * @inheritDoc
     */
    public function hasWork($name = null): bool
    {
        $name = $name ?: $this->getDefault();

        return Arr::has($this->config, "works.{$name}");
    }

    /**
     * @inheritDoc
     * @throws WechatInvalidConfigException
     */
    public function openWork(): \EasyWeChat\OpenWork\Application
    {
        $app = Factory::openWork($this->getConfig('open_work'));

        $app['contact_way'] = function ($app) {
            return new ContactWayClient($app);
        };

        $app = $this->initApplication($app);

        return $app;
    }

    /**
     * @inheritDoc
     */
    public function hasOpenWork(): bool
    {
        return isset($this->config['open_work']);
    }

    /**
     * @param string $name
     * @param bool $failException
     * @return array
     * @throws WechatInvalidConfigException
     */
    public function getConfig(string $name, $failException = true)
    {
        $config = Arr::get($this->config, $name);
        if (empty($config) && $failException) {
            throw new WechatInvalidConfigException($name);
        }

        $config['log'] = $this->getLogConfig();

        return $config;
    }

    /**
     * @return array
     */
    protected function getLogConfig()
    {
        return [
            'default' => 'stack',
            'channels' => [
                'stack' => [
                    'driver' => 'stack',
                    'channels' => ['single', 'api_log'],
                    'ignore_exceptions' => false,
                ],
                'daily' => [
                    'driver' => 'daily',
                    'path' => storage_path('logs/laravel.log'),
                    'level' => 'debug',
                    'days' => 30,
                ],
                'api_log' => [
                    'driver' => 'api_log',
                    'level' => 'info',
                ],
            ],
        ];
    }

    /**
     * @param string $name
     * @param array $config
     * @return array
     */
    public function setConfig(string $name, array $config)
    {
        return Arr::set($this->config, $name, $config);
    }

    /**
     * @param ServiceContainer|mixed $app
     * @return mixed
     */
    protected function initApplication($app)
    {
        $app->logger->extend('api_log', function ($app, $config) {
            return new Monolog('WechatApiLog', [
                new ApiLogHandler($app, $config),
            ]);
        });

        return $app;
    }

    /**
     * @return string
     */
    protected function getDefault()
    {
        return 'default';
    }
}
