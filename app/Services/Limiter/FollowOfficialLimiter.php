<?php

namespace App\Services\Limiter;

use App\Models\Wechat\Officials\User as OfficialUser;

class FollowOfficialLimiter extends AbstractLimiter
{
    /**
     * @inheritDoc
     */
    protected function check($data)
    {
        $exists = isset($data['unionid']) ? $this->existsByUnionid($data['unionid']) : $this->existsByOpenid($data['openid']);
        if (!$exists) {
            throw new LimitException('请先关注公众号');
        }
    }

    /**
     * @param string $openid
     * @return bool
     */
    protected function existsByOpenid($openid)
    {
        return OfficialUser::query()->where([
            'openid' => $openid,
        ])->exists();
    }

    /**
     * @param string $unionid
     * @return bool
     */
    protected function existsByUnionid($unionid)
    {
        return OfficialUser::query()->where([
            'unionid' => $unionid,
        ])->exists();
    }
}
