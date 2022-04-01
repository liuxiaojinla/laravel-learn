<?php

namespace App\Listeners\Wechat\Official;

use App\Services\Wechat\Official\Events\UserSubscribe;
use App\Services\Wechat\Official\UserService;
use App\Services\Wechat\Work\Events\UserChange;

class SyncUser extends AbstractListener
{
    /**
     * @var UserService
     */
    protected $service;

    /**
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * 处理事件
     *
     * @param UserSubscribe $event
     * @return void
     */
    public function handle(UserSubscribe $event)
    {
        if ($event->isCreateUser() || $event->isUpdateUser()) {
            $this->syncUser($event);
        } elseif ($event->isDeleteUser()) {
            $this->deleteUser($event);
        }
    }

    /**
     * 同步用户
     * @param UserSubscribe $event
     */
    protected function syncUser(UserSubscribe $event)
    {
        $this->service->syncByOpenid($event->getOpenid());
    }

    /**
     * 删除用户
     * @param UserChange $event
     */
    protected function deleteUser(UserChange $event)
    {
        $this->service->deleteByUseridOnlyLocal($event->getUserID());
    }
}
