<?php

namespace App\Models\Wechat\Officials;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class TemplateMessage extends Model
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
        if (isset($data['priTmplId'])) {
            $data['pri_tmpl_id'] = $data['priTmplId'];
        }

        return Arr::only($data, [
            'pri_tmpl_id', 'title', 'primary_industry', 'deputy_industry', 'content', 'example',
        ]);
    }
}
