<?php

namespace App\Providers;

use App\Contracts\Plugin\PluginInfo;
use App\Events\Plugin\Booted;
use App\Services\Plugin\PluginManager;
use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PluginManager
     */
    protected $pluginManager;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(PluginManager::class, function () {
            return new PluginManager(config('plugin') ?: []);
        });

        $this->registerFactoryResolvers();
    }

    /**
     * Get the factory name for the given model name.
     *
     * @return void
     */
    public function registerFactoryResolvers()
    {
        Factory::guessFactoryNamesUsing(function ($modelName) {
            $factoryNamespace = 'Database\\Factories\\';
            $appNamespace = $this->app->getNamespace();

            $modelName = Str::startsWith($modelName, $appNamespace . 'Models\\')
                ? Str::after($modelName, $appNamespace . 'Models\\')
                : Str::after($modelName, $appNamespace);

            $class = $factoryNamespace . $modelName . 'Factory';

            if (!class_exists($class) && Str::startsWith($modelName, $this->pluginManager->getNamespace())) {
                $modelClass = Str::after($modelName, substr($modelName, 0, strpos($modelName, 'Models\\') + 7));
                $pluginNamespace = substr($modelName, 0, strpos($modelName, 'Models\\'));
                $class = $pluginNamespace . $factoryNamespace . $modelClass . 'Factory';
            }

            return $class;
        });

        Factory::guessModelNamesUsing(function (Factory $factory) {
            $factoryClassname = get_class($factory);
            if (Str::startsWith($factoryClassname, $this->pluginManager->getNamespace())) {
                return Str::replaceFirst("Database\\Factories\\", "Models\\", Str::replaceLast('Factory', '', $factoryClassname));
            }

            $factoryBasename = Str::replaceLast('Factory', '', class_basename($factory));
            $appNamespace = $this->app->getNamespace();

            return class_exists($appNamespace . 'Models\\' . $factoryBasename)
                ? $appNamespace . 'Models\\' . $factoryBasename
                : $appNamespace . $factoryBasename;
        });
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->request = app('request');
        $this->pluginManager = app(PluginManager::class);

        $this->registerRequestMacros();

        $this->booted(function (Request $request) {
            $this->app->booted(function () {
                // dd(Route::getRoutes());
            });
            if (!$request->plugin()) {
                return;
            }

            // 路由是否缓存
            $plugin = $this->pluginManager->current();
            if ($this->app->routesAreCached()) {
                $this->app->booted(function () use ($request, $plugin) {
                    $this->loadRoutes($request->module(), $plugin);
                });
            } else {
                $this->loadRoutes($request->module(), $plugin);
            }
        });

        $this->initListeners();

        $this->initCommands();

        event(new Booted());
    }

    /**
     * 注册请求器相关宏操作
     * @return void
     */
    protected function registerRequestMacros()
    {
        $plugin = $this->pluginManager->parse($this->request->modulePath());

        Request::macro('plugin', function () use ($plugin) {
            return $plugin ? $plugin->name() : null;
        });

        Request::macro('pluginPath', function () use ($plugin) {
            return $plugin ? $this->pluginManager->getCurrentRequestPath() : null;
        });
    }

    /**
     * @param string $module
     * @param PluginInfo $plugin
     * @return void
     */
    protected function loadRoutes(string $module, PluginInfo $plugin)
    {
        $routeFiles = $plugin->getRouteFiles($module);
        if (empty($routeFiles)) {
            return;
        }

        $prefix = app(PluginManager::class)->getRoutePrefix() . "/" . Str::snake($plugin->name()) . "/";
        $prefix = "{$module}/" . $prefix;

        Route::prefix($prefix)
            ->middleware([
                $module, 'plugin',
            ])
            ->group(function () use ($routeFiles) {
                foreach ($routeFiles as $file) {
                    require_once $file;
                }
            });
    }

    /**
     * 初始化监听器
     * @return void
     */
    public function initListeners()
    {
        $plugins = $this->pluginManager->getPlugins();
        /** @var PluginInfo $plugin */
        foreach ($plugins as $plugin) {
            $events = $plugin->getListeners();
            if (empty($events)) {
                continue;
            }

            foreach ($events as $event => $listeners) {
                foreach (array_unique($listeners) as $listener) {
                    Event::listen($event, $listener);
                }
            }
        }
    }

    /**
     * 加载命令
     * @return void
     */
    public function initCommands()
    {
        $plugins = $this->pluginManager->getPlugins();
        ConsoleApplication::starting(function (ConsoleApplication $artisan) use ($plugins) {
            /** @var PluginInfo $plugin */
            foreach ($plugins as $plugin) {
                $commands = $plugin->getCommands();
                if (!empty($commands)) {
                    $artisan->resolveCommands($plugin->getCommands());
                }

                $tasks = $plugin->getTasks();
                // todo
            }
        });
    }
}
