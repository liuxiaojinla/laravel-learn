<?php

namespace App\Services;

use Illuminate\Support\Arr;


trait WithConfig
{
    /**
     * 获取配置
     * @param string $key
     * @param mixed $default
     * @return array|\ArrayAccess|mixed
     */
    public function getConfig(string $key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * 设置配置
     * @param array|string $key
     * @param mixed $value
     */
    public function setConfig($key, $value = null)
    {
        if (is_array($key)) {
            $this->config = array_merge_recursive($this->config, $key);
        } else {
            Arr::set($this->config, $key, $value);
        }
    }
}