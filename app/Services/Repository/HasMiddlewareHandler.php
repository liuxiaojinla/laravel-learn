<?php

namespace App\Services\Repository;

trait HasMiddlewareHandler
{
    /**
     * @param string $class
     * @return void
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function setupHandler($class)
    {
        $reflect = new \ReflectionClass($class);
        $instance = $reflect->newInstance();

        if ($reflect->hasMethod('filterable')) {
            $this->filterable($reflect->getMethod('filterable')->getClosure($instance));
        }

        if ($reflect->hasMethod('detailable')) {
            $this->detailable($reflect->getMethod('detailable')->getClosure($instance));
        }

        if ($reflect->hasMethod('showable')) {
            $this->showable($reflect->getMethod('showable')->getClosure($instance));
        }

        if ($reflect->hasMethod('validateable')) {
            $this->validateable($reflect->getMethod('validateable')->getClosure($instance));
        }

        if ($reflect->hasMethod('storeable')) {
            $this->storeable($reflect->getMethod('storeable')->getClosure($instance));
        }

        if ($reflect->hasMethod('updateable')) {
            $this->updateable($reflect->getMethod('updateable')->getClosure($instance));
        }

        if ($reflect->hasMethod('deleteable')) {
            $this->deleteable($reflect->getMethod('deleteable')->getClosure($instance));
        }

        if ($reflect->hasMethod('recoveryable')) {
            $this->recoveryable($reflect->getMethod('recoveryable')->getClosure($instance));
        }

        if ($reflect->hasMethod('restoreable')) {
            $this->restoreable($reflect->getMethod('restoreable')->getClosure($instance));
        }
    }

    /**
     * @param string $class
     * @return void
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function setupGlobalHandler($class)
    {
        $reflect = new \ReflectionClass($class);
        $instance = $reflect->newInstance();

        if ($reflect->hasMethod('filterable')) {
            static::globalFilterable($reflect->getMethod('filterable')->getClosure($instance));
        }

        if ($reflect->hasMethod('detailable')) {
            static::globalDetailable($reflect->getMethod('detailable')->getClosure($instance));
        }

        if ($reflect->hasMethod('showable')) {
            static::globalShowable($reflect->getMethod('showable')->getClosure($instance));
        }

        if ($reflect->hasMethod('validateable')) {
            static::globalValidateable($reflect->getMethod('validateable')->getClosure($instance));
        }

        if ($reflect->hasMethod('storeable')) {
            static::globalStoreable($reflect->getMethod('storeable')->getClosure($instance));
        }

        if ($reflect->hasMethod('updateable')) {
            static::globalUpdateable($reflect->getMethod('updateable')->getClosure($instance));
        }

        if ($reflect->hasMethod('deleteable')) {
            static::globalDeleteable($reflect->getMethod('deleteable')->getClosure($instance));
        }

        if ($reflect->hasMethod('recoveryable')) {
            static::globalRecoveryable($reflect->getMethod('recoveryable')->getClosure($instance));
        }

        if ($reflect->hasMethod('restoreable')) {
            static::globalRestoreable($reflect->getMethod('restoreable')->getClosure($instance));
        }
    }
}
