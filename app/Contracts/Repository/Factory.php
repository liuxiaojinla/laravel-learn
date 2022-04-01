<?php

namespace App\Contracts\Repository;

interface Factory
{
    /**
     * 获取一个仓库
     * @param string $name
     * @return Repository
     */
    public function repository($name);

    /**
     * 注册与绑定仓库生成器
     * @param string $name
     * @param \Closure|string|null $concrete
     * @return void
     */
    public function register($name, $concrete);

    /**
     * 移除一个仓库
     * @param string $name
     * @return void
     */
    public function forget($name, $unbind = false);
}
