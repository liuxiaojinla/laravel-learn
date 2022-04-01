<?php

namespace App\Models\Wechat\Officials;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class SubscriptionMessage extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $casts = [

    ];

    /**
     * 获取允许更新的字段列表
     * @param array $data
     * @return array
     */
    public static function getAllowFields($data)
    {
        return Arr::only($data, [
            'template_id', 'title', 'type', 'content', 'example',
        ]);
    }
}
