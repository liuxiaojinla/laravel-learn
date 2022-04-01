<?php
/**
 * Talents come from diligence, and knowledge is gained by accumulation.
 *
 * @author: 晋<657306123@qq.com>
 */

namespace App\Contracts\Config;

interface Repository extends \ArrayAccess
{

    /**
     * 检测指定的 key 是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * 获取配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * 设置配置
     *
     * @param string $key
     * @param mixed $value
     * @param bool $isMerge
     * @return mixed
     */
    public function set($key, $value, $isMerge = true);

    /**
     * 移除配置
     * @param string $key
     * @return bool
     */
    public function forget($key);

    /**
     * 获取所有的配置
     *
     * @return array
     */
    public function all();

}
