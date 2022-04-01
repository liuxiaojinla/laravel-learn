<?php

namespace App\Providers;

use App\Services\Module\ModuleManager;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * @var ModuleManager
     */
    protected $module;

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    public function register()
    {
        parent::register();

        $this->module = new ModuleManager(
            config('module')
        );

        $this->app->instance('module', $this->module);
        $this->app->alias('module', ModuleManager::class);
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->module->parse(
            $this->app['request']->path(),
            $this->app['request']
        );

        $this->routes(function (Request $request) {
            $module = $request->module();

            if ('api' === $module) {
                $this->mapApiRoutes();
            } elseif ('notify' === $module) {
                $this->mapNotifyRoutes();
            } elseif ('web' === $module) {
                $this->mapWebRoutes();
            } elseif ('auth' === $module) {
                $this->mapAuthRoutes();
            }

            if (app()->environment('local') && file_exists(base_path('routes/local.php'))) {
                Route::prefix('local')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/local.php'));
            }
        });
    }

    /**
     * 映射授权相关路由
     * @return void
     */
    protected function mapAuthRoutes()
    {
        Route::prefix('auth')
            ->middleware('oauth')
            ->namespace($this->namespace)
            ->group(base_path('routes/auth.php'));
    }

    /**
     * 映射API模块相关路由
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * 映射PC端相关路由
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * 映射三方平台回调相关路由
     * @return void
     */
    protected function mapNotifyRoutes()
    {
        Route::prefix('notify')
            ->middleware('notify')
            ->namespace($this->namespace)
            ->group(base_path('routes/notify.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
