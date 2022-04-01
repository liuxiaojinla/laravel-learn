<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Support\Arr;

/**
 * @property WechatWorkGroupChat $groupChat
 */
class WechatWorkGroupChatMember extends Model
{
    /**
     * @var array
     */
    protected $casts = [
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChat()
    {
        return $this->belongsTo(WechatWorkGroupChat::class, 'chat_id', 'chat_id');
    }

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'chat_id', 'name', 'group_nickname', 'userid', 'invitor_userid', 'type', 'unionid', 'join_time', 'join_scene',
        ]);
    }
}
