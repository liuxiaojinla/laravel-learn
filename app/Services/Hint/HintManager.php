<?php

namespace App\Services\Hint;

use App\Contracts\Hint\Factory as HintFactory;
use App\Services\Manager;

/**
 * @mixin \App\Contracts\Hint\Hint
 */
class HintManager extends Manager implements HintFactory
{
    /**
     * @var array
     */
    protected $config = [
        'defaults' => [
            'hint' => 'api',
        ],

        'hints' => [
            'api' => [
                'driver' => 'api',
            ],

            'web' => [
                'driver' => 'web',
            ],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function hint($name = null)
    {
        return $this->driver($name);
    }

    /**
     * @inheritDoc
     */
    public function shouldUse($name)
    {
        $name = $name ?: $this->getDefaultDriver();

        $this->setDefaultDriver($name);
    }

    /**
     * 创建API提示器
     * @param string $name
     * @param array $config
     * @return ApiHint
     */
    protected function createApiDriver($name, array $config)
    {
        return new ApiHint($config);
    }

    /**
     * @inerhitDoc
     */
    protected function getDefaultDriver()
    {
        return $this->getConfig('defaults.hint', 'api');
    }

    /**
     * @inerhitDoc
     */
    protected function setDefaultDriver($name)
    {
        $this->setConfig('defaults.hint', $name);
    }

    /**
     * @inerhitDoc
     */
    public function getDriverConfig($name)
    {
        $key = 'hints';

        return $this->getConfig($name ? "{$key}.{$name}" : $key);
    }
}
