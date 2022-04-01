<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Config;
use Laravel\Sanctum\Sanctum;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->initAuthentication();

        $this->registerPolicies();
    }


    /**
     * @return void
     */
    protected function initAuthentication()
    {
        $this->app->booted(function () {
            Config::set('sanctum.guard', \request()->module());
        });

        // Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
