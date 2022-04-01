<?php

namespace App\Services\Bot;

use App\Contracts\Bot\Bot;
use App\Contracts\Bot\Factory;
use App\Services\Manager;

/**
 * @mixin \App\Contracts\Bot\Bot
 */
class BotManager extends Manager implements Factory
{

    /**
     * @inheritDoc
     */
    public function bot($name): Bot
    {
        return $this->driver($name);
    }

    /**
     * 创建企微机器人
     * @param string $name
     * @param array $config
     * @return QyWork
     */
    public function createQyworkDriver($name, array $config): QyWork
    {
        return new QyWork($config);
    }

    /**
     * 创建钉钉机器人
     * @param string $name
     * @param array $config
     * @return DingTalk
     */
    public function createDingTalkDriver($name, array $config): DingTalk
    {
        return new DingTalk($config);
    }

    /**
     * @inerhitDoc
     */
    protected function getDefaultDriver()
    {
        return $this->getConfig('defaults.bot', 'default');
    }

    /**
     * @inerhitDoc
     */
    protected function setDefaultDriver($name)
    {
        $this->setConfig('defaults.bot', $name);
    }

    /**
     * @inerhitDoc
     */
    public function getDriverConfig($name)
    {
        $key = 'bots';

        return $this->getConfig($name ? "{$key}.{$name}" : $key);
    }
}