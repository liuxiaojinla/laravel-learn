<?php

namespace App\Providers;

use App\Contracts\Hint\Factory as HintFactory;
use App\Services\Hint\HintManager;
use Illuminate\Support\ServiceProvider;

class HintServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HintManager::class, function ($app) {
            return new HintManager(config('hint') ?: []);
        });
        $this->app->alias(HintManager::class, 'hint');
        $this->app->alias(HintManager::class, HintFactory::class);

        $this->app->singleton('hint.driver', function ($app) {
            return $app['hint']->hint();
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
}
