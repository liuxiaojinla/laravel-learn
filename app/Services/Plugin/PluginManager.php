<?php

namespace App\Services\Plugin;

use App\Services\WithConfig;
use App\Services\WithContainer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class PluginManager
{
    use Macroable, WithConfig, WithContainer;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $currentPlugin;

    /**
     * @var string
     */
    protected $currentPluginRequestPath;

    /**
     * @var string
     */
    protected $requestPath;

    /**
     * @var PluginInfo|null
     */
    protected $current;

    /**
     * @var \Illuminate\Support\Collection|null
     */
    protected $loadedPlugins = null;

    /**
     * Create a new PluginManager instance.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = array_merge_recursive($this->config, $config);
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->getConfig('namespace', 'Plugins\\');
    }

    /**
     * @param string $path
     * @return \App\Services\Plugin\PluginInfo|null
     */
    public function parse(string $path): ?PluginInfo
    {
        $this->requestPath = $path;

        [$plugin, $path] = $this->parsePath($path);
        $plugin = Str::studly($plugin);

        $this->currentPlugin = $plugin;
        $this->currentPluginRequestPath = $path;

        $this->current = $this->isExist($plugin) ?
            new PluginInfo($plugin, $this->rootPath($plugin), $this) : null;

        return $this->current;
    }

    /**
     * @param string $path
     * @return array
     */
    protected function parsePath(string $path): array
    {
        $plugin = '';

        $prefix = $this->getRoutePrefix();
        if (str_starts_with($path, $prefix . '/')) {
            $info = explode('/', $path, 3);
            $plugin = $info[1] ?? '';
            $path = $info[2] ?? '';
        }

        return [$plugin, $path];
    }

    /**
     * @param string $pluginName
     * @return bool
     */
    public function isExist(string $pluginName): bool
    {
        $pluginName = Str::studly($pluginName);

        return file_exists($this->rootPath($pluginName . DIRECTORY_SEPARATOR . 'manifest.php'));
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getPlugins(): Collection
    {
        return $this->getLocalPlugins();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getLocalPlugins(): Collection
    {
        if ($this->loadedPlugins) {
            return $this->loadedPlugins;
        }

        $directoryIterator = new \FilesystemIterator($this->rootPath());

        $result = [];
        /** @var \SplFileInfo $directory */
        foreach ($directoryIterator as $directory) {
            if (!$directory->isDir()) {
                continue;
            }

            $result[] = new PluginInfo($directory->getFilename(), $directory->getRealPath(), $this);
        }

        return $this->loadedPlugins = new Collection($result);
    }

    /**
     * @return string
     */
    public function getRoutePrefix(): string
    {
        return $this->getConfig('route.prefix', 'plugin');
    }

    /**
     * @return \App\Services\Plugin\PluginInfo|null
     */
    public function current(): ?PluginInfo
    {
        return $this->current;
    }

    /**
     * @return string
     */
    public function getCurrentName(): string
    {
        return $this->currentPlugin;
    }

    /**
     * @return string
     */
    public function getCurrentRequestPath(): string
    {
        return $this->currentPluginRequestPath;
    }

    /**
     * @return string
     */
    public function getRequestRootPath(): string
    {
        return $this->requestPath;
    }

    /**
     * @param string|null $path
     * @return string
     */
    public function rootPath(string $path = null): string
    {
        return base_path('plugins') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
