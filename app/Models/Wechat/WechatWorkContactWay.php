<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Support\Arr;

class WechatWorkContactWay extends Model
{
    /**
     * @var array
     */
    protected $casts = [

    ];

    /**
     * 用户userID列表（获取器）
     * @param string $value
     * @return array|false|string[]
     */
    protected function getUserAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * 部门id列表（获取器）
     * @param string $value
     * @return array|false|string[]
     */
    protected function getPartyAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * 用户userID列表（设置器）
     * @param string $value
     */
    protected function setUserAttribute($value)
    {
        $this->attributes['user'] = Util::transformStrList($value);
    }

    /**
     * 部门id列表（设置器）
     * @param string $value
     */
    protected function setPartyAttribute($value)
    {
        $this->attributes['party'] = Util::transformStrList($value);
    }

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'config_id', 'type', 'scene', 'is_temp', 'remark', 'skip_verify', 'state', 'style', 'qr_code', 'user',
            'party', 'expires_in', 'chat_expires_in', 'unionid', 'conclusions',
        ]);
    }
}
