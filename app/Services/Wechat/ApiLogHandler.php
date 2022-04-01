<?php

namespace App\Services\Wechat;

use App\Models\Wechat\WechatApiLog;
use EasyWeChat\Kernel\ServiceContainer;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger as Monolog;

class ApiLogHandler extends AbstractProcessingHandler
{
    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param ServiceContainer $app
     * @param array $config
     * @param int $level
     * @param bool $bubble
     */
    public function __construct($app, $config, $level = Monolog::DEBUG, bool $bubble = true)
    {
        $this->app = $app;
        $this->config = $config;
        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(array $record): void
    {
        if (strpos($record['message'], '"errcode":0') || strpos($record['message'], 'Server response created:') !== false || strpos($record['message'], 'Request received:') !== false) {
            return;
        }

        $wechatConfig = $this->app->config;
        $message = $record['message'];
        $result = substr($message, strrpos($message, "\r\n") + 2, -14);
        WechatApiLog::query()->create([
            'app_id' => $wechatConfig['app_id'] ?? $wechatConfig['corp_id'] ?? '',
            'result' => $result ?? '',
            'data' => $record['message'] ?? '',
        ]);
    }
}
