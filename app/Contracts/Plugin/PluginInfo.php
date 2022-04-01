<?php

namespace App\Contracts\Plugin;

interface PluginInfo
{
    /**
     * @return string
     */
    public function name();

    /**
     * @param string|null $name
     * @param mixed|null $default
     * @return array
     */
    public function getInfo($name = null, $default = null);

    /**
     * @return array
     */
    public function getCommands();

    /**
     * @return array
     */
    public function getTasks();

    /**
     * @return array
     */
    public function getListeners();
}