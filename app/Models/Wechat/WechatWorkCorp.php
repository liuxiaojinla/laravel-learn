<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class WechatWorkCorp extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $casts = [
    ];

    /**
     * @param array $data
     * @return string[]
     */
    public static function getAllowFields($data)
    {
        return Arr::only($data, [
            'corp_id',
            'corp_name',
            'corp_type',
            'corp_round_logo_url',
            'corp_square_logo_url',
            'corp_user_max',
            'corp_wxqrcode',
            'corp_full_name',
            'subject_type',
            'corp_scale',
            'corp_industry',
            'corp_sub_industry',
            'location',
        ]);
    }
}
