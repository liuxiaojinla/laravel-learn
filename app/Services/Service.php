<?php

namespace App\Services;

use Illuminate\Support\Traits\Macroable;

abstract class Service
{
    use Macroable, WithConfig, WithContainer;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var static
     */
    protected static $defaultInstance = null;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge_recursive($this->config, $config);
    }

    /**
     * 获取单例
     * @return static|null
     */
    public static function getInstance()
    {
        if (static::$defaultInstance == null) {
            static::$defaultInstance = static::makeInstance();
        }

        return static::$defaultInstance;
    }

    /**
     * 生成实例
     * @return static
     */
    public static function makeInstance()
    {
        throw new \RuntimeException('Interface not implemented!');
    }
}
