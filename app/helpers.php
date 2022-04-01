<?php

use App\Services\Module\ModuleManager;
use App\Services\Plugin\PluginManager;

if (!function_exists('plugin_url')) {
    function plugin_url($path = null, $parameters = [], $secure = null)
    {
        if ($path) {
            $plugin = request()->plugin();
            $prefix = app(PluginManager::class)->getRoutePrefix() . "/{$plugin}/";

            $module = request()->module();
            if ($module != app(ModuleManager::class)->getDefaultModule()) {
                $prefix = "{$module}/" . $prefix;
            }

            $path = $prefix . $path;
        }

        return url($path, $parameters, $secure);
    }
}
