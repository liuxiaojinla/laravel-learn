<?php

namespace App\Services\Module;

use App\Services\Service;
use Illuminate\Http\Request;

class ModuleManager extends Service
{
    /**
     * @var false|string
     */
    protected $module;

    /**
     * @var false|mixed|string
     */
    protected $path;

    /**
     * 解析模块
     * @param string $requestPath
     * @return array
     */
    public function parse(string $requestPath)
    {
        $module = $this->getDefaultModule();
        $modulePath = $requestPath;

        if ($index = strpos($requestPath, '/')) {
            $module = substr($requestPath, 0, $index);
            if (in_array($module, $this->getWhiteModules())) {
                $modulePath = substr($requestPath, $index + 1);
            } else {
                $module = $this->getDefaultModule();
            }
        } else {
            if (in_array($requestPath, $this->getWhiteModules())) {
                $module = $requestPath;
                $modulePath = '';
            }
        }

        $this->module = $module;
        $this->path = $modulePath;

        $this->registerRequestMacros($module, $modulePath);

        return [$module, $modulePath];
    }

    /**
     * 注册请求器相关宏操作
     * @return void
     */
    protected function registerRequestMacros($module, $modulePath)
    {
        Request::macro('setPathInfo', function ($pathInfo) {
            /** @var Request $this */
            $this->pathInfo = $pathInfo;
        });

        Request::macro('module', function () use ($module) {
            return $module;
        });

        Request::macro('modulePath', function () use ($modulePath) {
            return $modulePath;
        });
    }

    /**
     * @return string
     */
    public function getDefaultModule()
    {
        return $this->getConfig('defaults.module', 'web');
    }

    /**
     * @return array
     */
    public function getWhiteModules()
    {
        return $this->getConfig('white_list', []);
    }
}
