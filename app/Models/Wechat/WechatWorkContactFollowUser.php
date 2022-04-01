<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property WechatWorkContact $contact
 */
class WechatWorkContactFollowUser extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $casts = [
        'remark_mobiles' => 'array',
        'tags' => 'json',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contact()
    {
        return $this->belongsTo(WechatWorkContact::class, 'external_userid', 'external_userid');
    }

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'external_userid', 'userid', 'unionid', 'remark', 'description', 'oper_userid',
            'remark_corp_name', 'remark_mobiles', 'add_way', 'state', 'tags',
        ]);
    }
}
