<?php

namespace App\Services\Middleware;

use League\Pipeline\Pipeline;

class MiddlewareManager
{
    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * 获取中间件
     * @param string $name
     * @return array
     */
    public function get($name = null)
    {
        if ($name) {
            return $this->middlewares[$name] ?? [];
        }

        return $this->middlewares;
    }

    /**
     * 添加中间件
     * @param callable $handler
     * @param string $name
     * @return $this
     */
    public function push(callable $handler, $name = 'global')
    {
        $this->init($name);

        $this->middlewares[$name][] = $handler;

        return $this;
    }

    /**
     * 添加一组中间件
     * @param callable[] $handlers
     * @param string $name
     * @return $this
     */
    public function pushMany(array $handlers, $name = 'global')
    {
        foreach ($handlers as $handler) {
            $this->push($name, $handler);
        }

        return $this;
    }

    /**
     * 插入中间件
     * @param callable $handler
     * @param int $index
     * @param string $name
     * @return $this
     */
    public function insert(callable $handler, $index = 0, $name = 'global')
    {
        $this->init($name);

        array_splice($this->middlewares[$name], $index, 0, [$handler]);

        return $this;
    }

    /**
     * 插入一组中间件
     * @param string $name
     * @param callable[] $handlers
     * @param int $index
     * @return $this
     */
    public function insertMany(array $handlers, $index = 0, $name = 'global')
    {
        $this->init($name);

        array_splice($this->middlewares[$name], $index, 0, [$handlers]);

        return $this;
    }

    /**
     * 初始化中间件组
     * @param string $name
     * @return void
     */
    protected function init($name)
    {
        if (!isset($this->middlewares[$name])) {
            $this->middlewares[$name] = [];
        }
    }

    /**
     * 移除中间件
     * @param callable $handler
     * @param string $name
     * @return $this
     */
    public function remove($handler, $name = 'global')
    {
        if (!isset($this->middlewares[$name])) {
            return $this;
        }

        $middlewares = &$this->middlewares[$name];
        foreach ($middlewares as $key => $middleware) {
            if ($middleware === $handler) {
                unset($middlewares[$key]);
            }
        }

        unset($middlewares);

        return $this;
    }

    /**
     * 清除中间件
     * @param string $name
     * @return $this
     */
    public function clear($name = null)
    {
        if ($name) {
            if (isset($this->middlewares[$name])) {
                $this->middlewares[$name] = [];
            }
        } else {
            $this->middlewares = [];
        }

        return $this;
    }

    /**
     * 调度中间件
     * @param mixed $input
     * @param callable $destination
     * @param string $name
     * @return mixed
     */
    public function then($input, callable $destination, $name = 'global')
    {
        return (new Pipeline(
            new Processor($destination),
            ...$this->get($name)
        ))->process($input);
    }
}
