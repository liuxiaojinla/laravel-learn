<?php

namespace App\Providers;

use App\Contracts\Wechat\Factory as WechatFactory;
use App\Services\Wechat\WechatManager;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class WechatServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WechatManager::class, function () {
            return new WechatManager(config('wechat') ?: []);
        });

        $this->app->alias(WechatManager::class, 'wechat');

        $this->app->alias(WechatManager::class, WechatFactory::class);

        $this->app->singleton('wechat.miniprogram', function ($app) {
            return $app['wechat']->miniprogram();
        });

        $this->app->singleton('wechat.official', function ($app) {
            return $app['wechat']->official();
        });

        $this->app->singleton('wechat.open', function ($app) {
            return $app['wechat']->official();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'wechat',
            WechatManager::class,
            WechatFactory::class,
            'wechat.miniprogram',
            'wechat.official',
            'wechat.open',
        ];
    }
}
