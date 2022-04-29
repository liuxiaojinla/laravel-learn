<?php

namespace App\Providers;

use App\Contracts\Bot\Factory as BotFactory;
use App\Services\Bot\BotManager;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BotServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BotManager::class, function ($app) {
            return new BotManager(config('bot') ?: []);
        });

        $this->app->alias(
            BotManager::class,
            BotFactory::class
        );

        $this->app->alias(
            BotManager::class,
            'bot'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'bot',
            BotManager::class,
            BotFactory::class,
        ];
    }
}
