<?php

namespace App\Models\Wechat\Officials;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property-read string $unionid
 */
class User extends Model
{
    use SoftDeletes;

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'openid', 'unionid', 'nickname', 'headimgurl', 'sex', 'city', 'province', 'country', 'language', 'remark',
            'groupid', 'tagid_list', 'subscribe', 'subscribe_time', 'subscribe_scene', 'qr_scene', 'qr_scene_str',
        ]);
    }
}
