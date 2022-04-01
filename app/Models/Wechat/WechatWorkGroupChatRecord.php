<?php

namespace App\Models\Wechat;

use App\Models\Model;

class WechatWorkGroupChatRecord extends Model
{

    // 去除更新时间
    public const UPDATED_AT = null;

    /**
     * @var array
     */
    protected $guarded = [];
}
