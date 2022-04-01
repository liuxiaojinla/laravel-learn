<?php

namespace App\Listeners\Wechat\Work;

use App\Models\Wechat\WechatWorkContactBehavior;
use App\Services\Wechat\Work\Events\ExternalContactChange;

class PushContactBehaviorLog extends AbstractListener
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ExternalContactChange $event)
    {
        WechatWorkContactBehavior::query()->create([
            'corp_id' => $event->getCorpId(),
            'external_user_id' => $event->getExternalUserID(),
            'action' => $event->getChangeType(),
            'action_time' => $event->getCreateTime(),
            'relate_user_id' => $event->getUserID(),
        ]);
    }
}
