<?php

namespace App\Listeners\Wechat\Work;

use App\Services\Wechat\Work\Events\ExternalGroupChatChange;
use App\Services\Wechat\Work\Events\ExternalGroupChatUpdate;
use App\Services\Wechat\Work\GroupChatService;

class SyncContactGroupChat extends AbstractListener
{
    /**
     * @var GroupChatService
     */
    protected $service;

    /**
     * @param GroupChatService $service
     */
    public function __construct(GroupChatService $service)
    {
        $this->service = $service;
    }

    /**
     * 处理事件
     *
     * @param ExternalGroupChatChange $event
     * @return void
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(ExternalGroupChatChange $event)
    {
        if ($event->isCreate()) {
            $this->syncGroupChat($event);
        } elseif ($event->isUpdate()) {
            $this->dispatchUpdate($event);
        } elseif ($event->isDismiss()) {
            $this->deleteGroupChat($event);
        }
    }

    /**
     * 更新客户群
     * @param ExternalGroupChatChange $event
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function dispatchUpdate(ExternalGroupChatChange $event)
    {
        $this->syncGroupChat($event);

        $event = ExternalGroupChatUpdate::ofEvent($event);
        switch ($event->getUpdateDetail()) {
            case ExternalGroupChatUpdate::ADD_MEMBER:
                break;
            case ExternalGroupChatUpdate::DEL_MEMBER:
                break;
        }
    }

    /**
     * 同步客户群
     * @param ExternalGroupChatChange $event
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function syncGroupChat(ExternalGroupChatChange $event)
    {
        $this->service->syncByChatId($event->getChatId());
    }

    /**
     * 删除客户群
     * @param ExternalGroupChatChange $event
     */
    protected function deleteGroupChat(ExternalGroupChatChange $event)
    {
        $this->service->deleteByChatIdOnlyLocal($event->getChatId());
    }
}
