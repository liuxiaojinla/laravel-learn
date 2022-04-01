<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property Collection follow_users
 */
class WechatWorkContact extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $casts = [
        'external_profile' => 'json',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followUsers()
    {
        return $this->hasMany(WechatWorkContactFollowUser::class, 'external_userid', 'external_userid');
    }

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'external_userid', 'name', 'avatar', 'type', 'gender', 'unionid',
            'position', 'corp_name', 'corp_full_name', 'external_profile',
        ]);
    }
}
