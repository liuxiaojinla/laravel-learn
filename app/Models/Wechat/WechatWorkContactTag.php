<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Support\Arr;

class WechatWorkContactTag extends Model
{
    /**
     * @var array
     */
    protected $casts = [];

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'group_id', 'tag_id', 'name', 'order',
        ]);
    }
}
