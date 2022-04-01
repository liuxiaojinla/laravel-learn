<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

/**
 * @property Collection $members
 */
class WechatWorkGroupChat extends Model
{
    /**
     * @var array
     */
    protected $casts = [
        'admin_list' => 'json',
    ];

    /**
     * @inheritDoc
     */
    protected static function boot()
    {
        parent::boot();

        static::deleted(function (self $chat) {
            $chat->members()->delete();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany(WechatWorkGroupChatMember::class, 'chat_id', 'chat_id');
    }

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'chat_id', 'name', 'owner', 'notice', 'admin_list',
        ]);
    }
}
