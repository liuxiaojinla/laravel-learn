<?php

namespace App\Services\Plugin;

use App\Contracts\Plugin\PluginInfo as PluginInfoContract;
use Illuminate\Support\Str;

class PluginInfo implements PluginInfoContract
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $info = [];

    /**
     * @var PluginManager
     */
    protected $manager;

    /**
     * @param string $name
     * @param string $path
     * @param PluginManager $manager
     */
    public function __construct(string $name, string $path, PluginManager $manager)
    {
        $this->name = $name;
        $this->path = $path;
        $this->manager = $manager;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $path
     * @return string
     */
    public function rootPath(string $path = null): string
    {
        return $this->path . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @param string $module
     * @param string|null $path
     * @return string
     */
    public function modulePath(string $module, string $path = null): string
    {
        return $this->rootPath(Str::ucfirst($module)) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * @param string|null $module
     * @return array
     */
    public function getRouteFiles(string $module = null): array
    {
        $routes = [];

        $rootRouteFile = $this->rootPath('routes.php');
        if (file_exists($rootRouteFile)) {
            $routes[] = $rootRouteFile;
        }

        if ($module) {
            $moduleRouteFile = $this->modulePath($module, 'routes.php');
            if (file_exists($moduleRouteFile)) {
                $routes[] = $moduleRouteFile;
            }
        }

        return $routes;
    }

    /**
     * @return void
     */
    protected function loadInfo()
    {
        if ($this->info) {
            return;
        }

        $this->info = require $this->rootPath('manifest.php');
    }

    /**
     * @param string|null $name
     * @param mixed|null $default
     * @return array
     */
    public function getInfo($name = null, $default = null)
    {
        $this->loadInfo();

        return $name ? ($this->info[$name] ?? $default) : $this->info;
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->getInfo('commands', []);
    }

    /**
     * @return array
     */
    public function getTasks(): array
    {
        return $this->getInfo('tasks', []);
    }

    /**
     * @return array
     */
    public function getListeners(): array
    {
        return $this->getInfo('listeners', []);
    }
}
