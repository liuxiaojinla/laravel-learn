<?php

namespace App\Providers;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Contracts\View\Factory;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * @var Factory
     */
    protected $view;

    /**
     * @inheritDoc
     */
    public function boot()
    {
        $this->view = $this->app['view'];
    }
}
