<?php

namespace App\Services\ConfigCenter;

use App\Contracts\Config\Factory;
use App\Contracts\Config\Repository;
use App\Services\Manager;

/**
 * @mixin Repository
 */
class ConfigCenterManager extends Manager implements Factory
{
    /**
     * @inheritDoc
     */
    public function repository($name): Repository
    {
        return $this->driver($name);
    }

    /**
     * @param string $name
     * @param array $config
     * @return RemoteRepository
     */
    protected function createRemoteDriver($name, $config)
    {
        return new RemoteRepository($config);
    }

    /**
     * @param string $name
     * @param array $config
     * @return \App\Services\ConfigCenter\EtcdRepository
     */
    protected function createEtcdV3Driver($name, $config)
    {
        return new EtcdRepository($config);
    }
}
