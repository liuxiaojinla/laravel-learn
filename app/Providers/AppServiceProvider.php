<?php

namespace App\Providers;

use App\Contracts\Repository\Factory as RepositoryFactory;
use App\Services\Repository\RepositoryManager;
use Faker\Generator;
use Faker\Provider\zh_TW\Text;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Xin\Filesystem\Adapter\Qiniu\Qiniu;
use Xin\Filesystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RepositoryFactory::class, RepositoryManager::class);
        $this->app->singleton(RepositoryManager::class, RepositoryManager::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        //设置默认字符串长度
        Schema::defaultStringLength(191);

        if (class_exists(Generator::class)) {
            /** @var Generator $generator */
            $generator = $this->app->make(Generator::class);
            $generator->addProvider(new Text($generator));
        }

        $this->defineFilesystemAdapters();
    }

    /**
     * @return void
     */
    protected function defineFilesystemAdapters()
    {
        app('filesystem')->extend('qiniu', function ($app, $config) {
            $adapter = new Qiniu($config);

            return new FilesystemAdapter(new Filesystem($adapter, $config));
        });
    }
}
