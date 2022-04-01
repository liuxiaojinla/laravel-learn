<?php

namespace App\Services\Limiter;

use League\Pipeline\StageInterface;

abstract class AbstractLimiter implements StageInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var mixed
     */
    protected $payload;

    /**
     * 初始化数据
     */
    public function __construct($config)
    {
        $this->config = array_merge_recursive($this->getDefaultConfig(), $config);

        if (empty($this->name)) {
            $this->name = substr(class_basename(get_class()), 0, -7);
        }
    }

    /**
     * @inheritDoc
     */
    public function __invoke($payload)
    {
        $this->payload = $payload;

        if (isset($this->config['status']) && $this->config['status'] == 0) {
            return $payload;
        }

        $this->check($payload['data']);

        return $payload;
    }

    /**
     * 检查
     * @param array $data
     * @return mixed
     */
    abstract protected function check($data);

    /**
     * 读取限制配置
     * @param string $key
     * @param array $default
     * @return array
     */
    protected function getConfig($key = null, $default = null)
    {
        return $key ? $this->config[$key] ?? $default : $this->config;
    }

    /**
     * 获取默认限制配置
     * @return array
     */
    protected function getDefaultConfig()
    {
        return [];
    }
}
