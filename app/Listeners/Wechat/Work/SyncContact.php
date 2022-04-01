<?php

namespace App\Listeners\Wechat\Work;

use App\Services\Wechat\Work\ContactService;
use App\Services\Wechat\Work\Events\ExternalContactChange;

class SyncContact extends AbstractListener
{
    /**
     * @var ContactService
     */
    protected $service;

    /**
     * @param ContactService $service
     */
    public function __construct(ContactService $service)
    {
        $this->service = $service;
    }

    /**
     * 处理事件
     *
     * @param ExternalContactChange $event
     * @return void
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function handle(ExternalContactChange $event)
    {
        if ($event->isAddExternalContact() || $event->isAddHalfExternalContact() || $event->isEditExternalContact()) {
            $this->syncContact($event);
        } elseif ($event->isDelExternalContact()) {
            $this->deleteContact($event);
        } elseif ($event->isDelFollowUser()) {
            $this->deleteFollowUser($event);
        }
    }

    /**
     * 同步客户
     * @param ExternalContactChange $event
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    protected function syncContact(ExternalContactChange $event)
    {
        $this->service->syncByExternalUserid($event->getExternalUserID());
    }

    /**
     * 删除客户
     * @param ExternalContactChange $event
     */
    protected function deleteContact(ExternalContactChange $event)
    {
        $this->service->deleteByExternalUserIdOnlyLocal($event->getExternalUserID());
    }

    /**
     * 删除好友关系
     * @param ExternalContactChange $event
     */
    protected function deleteFollowUser(ExternalContactChange $event)
    {
        $this->service->deleteFollowUserOnlyLocal($event->getExternalUserID(), $event->getUserID());
    }
}
