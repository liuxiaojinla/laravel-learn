<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Services\Wechat\Work\Events\UserChange::class => [
            \App\Listeners\Wechat\Work\SyncUser::class,
        ],
        \App\Services\Wechat\Work\Events\PartyChange::class => [
            \App\Listeners\Wechat\Work\SyncUser::class,
        ],
        \App\Services\Wechat\Work\Events\ExternalContactChange::class => [
            \App\Listeners\Wechat\Work\PushContactBehaviorLog::class,
            \App\Listeners\Wechat\Work\SyncContact::class,
        ],
        \App\Services\Wechat\Work\Events\ExternalTagChange::class => [
            \App\Listeners\Wechat\Work\PushContactBehaviorLog::class,
            \App\Listeners\Wechat\Work\SyncContactTag::class,
        ],
        \App\Services\Wechat\Work\Events\ExternalGroupChatChange::class => [
            \App\Listeners\Wechat\Work\PushGroupChatRecordLog::class,
            \App\Listeners\Wechat\Work\SyncContactGroupChat::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
