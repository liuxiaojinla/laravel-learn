<?php

namespace App\Services\Repository;

use App\Services\Middleware\MiddlewareManager;

trait HasMiddleware
{
    use HasMiddlewareHandler;

    /**
     * @var MiddlewareManager
     */
    protected static $globalMiddlewareManager = null;

    /**
     * @var MiddlewareManager
     */
    protected $middlewareManager;

    /**
     * 调用中间件
     * @param mixed $input
     * @param callable $destination
     * @param string $name
     * @return mixed
     */
    protected function middleware($input, callable $destination, $name)
    {
        return static::globalMiddlewareManager()->then($input, function ($input) use ($destination, $name) {
            return $this->middlewareManager->then($input, $destination, $name);
        }, $name);
    }

    /**
     * @param string $name
     * @param \Closure $closure
     * @return HasMiddleware
     */
    public function registerMiddleware($name, \Closure $closure)
    {
        $this->middlewareManager->push($closure, $name);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function filterable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_FILTER, $closure);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function detailable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_DETAIL, $closure);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function showable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_SHOW, $closure);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function validateable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_VALIDATE, $closure);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function storeable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_STORE, $closure);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function updateable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_UPDATE, $closure);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function deleteable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_DELETE, $closure);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function recoveryable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_RECOVERY, $closure);

        return $this;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function restoreable(\Closure $closure)
    {
        $this->registerMiddleware(static::SCENE_RESTORE, $closure);

        return $this;
    }

    /**
     * @param string $name
     * @param \Closure $closure
     */
    public static function registerGlobalMiddleware($name, \Closure $closure)
    {
        static::globalMiddlewareManager()->push($closure, $name);
    }

    /**
     * @return MiddlewareManager
     */
    public static function globalMiddlewareManager()
    {
        if (static::$globalMiddlewareManager === null) {
            static::$globalMiddlewareManager = new MiddlewareManager();
        }

        return static::$globalMiddlewareManager;
    }

    /**
     * @param \Closure $closure
     */
    public static function globalFilterable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_FILTER, $closure);
    }

    /**
     * @param \Closure $closure
     */
    public static function globalDetailable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_DETAIL, $closure);
    }

    /**
     * @param \Closure $closure
     */
    public static function globalShowable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_SHOW, $closure);
    }

    /**
     * @param \Closure $closure
     */
    public static function globalValidateable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_VALIDATE, $closure);
    }

    /**
     * @param \Closure $closure
     */
    public static function globalStoreable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_STORE, $closure);
    }

    /**
     * @param \Closure $closure
     */
    public static function globalUpdateable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_UPDATE, $closure);
    }

    /**
     * @param \Closure $closure
     */
    public static function globalDeleteable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_DELETE, $closure);
    }

    /**
     * @param \Closure $closure
     */
    public static function globalRecoveryable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_RECOVERY, $closure);
    }

    /**
     * @param \Closure $closure
     */
    public static function globalRestoreable(\Closure $closure)
    {
        static::registerGlobalMiddleware(static::SCENE_RESTORE, $closure);
    }
}
