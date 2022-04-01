<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

/**
 * @property Collection $user
 */
class WechatWorkDepartment extends Model
{
    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(WechatWorkUser::class, 'department_id', 'main_department');
    }

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'department_id', 'name', 'order',
        ]);
    }
}
