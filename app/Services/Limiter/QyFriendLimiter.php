<?php

namespace App\Services\Limiter;

use App\Models\Wechat\WechatWorkContact;

class QyFriendLimiter extends AbstractLimiter
{
    /**
     * @inheritDoc
     */
    protected function check($data)
    {
        $unionid = $data['unionid'] ?? '';
        $contact = WechatWorkContact::query()->where('unionid', $unionid)->first();
        if (empty($contact)) {
            throw new LimitException('contact not exist.');
        }
    }
}
