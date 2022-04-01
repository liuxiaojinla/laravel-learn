<?php

namespace App\Listeners\Wechat\Work;

use App\Models\Wechat\WechatWorkGroupChatRecord;
use App\Services\Wechat\Work\Events\ExternalGroupChatChange;
use App\Services\Wechat\Work\Events\ExternalGroupChatUpdate;

class PushGroupChatRecordLog extends AbstractListener
{

    /**
     * 处理事件
     *
     * @param ExternalGroupChatChange $event
     * @return void
     */
    public function handle(ExternalGroupChatChange $event)
    {
        if (!$event->isUpdate()) {
            return;
        }

        $event = ExternalGroupChatUpdate::ofEvent($event);

        WechatWorkGroupChatRecord::query()->create([
            'corp_id' => $event->getCorpId(),
            'chat_id' => $event->getChatId(),
            'event_time' => $event->getTimeStamp(),
            'action' => $event->getUpdateDetail(),
            'join_scene' => $event->getJoinScene(),
            'quit_scene' => $event->getQuitScene(),
        ]);
    }
}
