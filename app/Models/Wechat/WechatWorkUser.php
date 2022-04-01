<?php

namespace App\Models\Wechat;

use App\Models\Concerns\AsJson;
use App\Models\Concerns\SerializeDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property WechatWorkDepartment $mainDepartmentInfo
 * @property-read string $openid
 */
class WechatWorkUser extends Authenticatable
{
    use SoftDeletes, AsJson, SerializeDate;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var array
     */
    protected $casts = [
        'extattr' => 'json',
        'external_profile' => 'json',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mainDepartmentInfo()
    {
        return $this->belongsTo(WechatWorkDepartment::class, 'main_department', 'department_id');
    }

    /**
     * 部门信息（获取器）
     * @param string $value
     * @return array|string[]
     */
    protected function getDepartmentAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * 部门排序信息（获取器）
     * @param string $value
     * @return array|string[]
     */
    protected function getOrderAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * 是否部门管理员信息（获取器）
     * @param string $value
     * @return array|string[]
     */
    protected function getIsLeaderInDeptAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * 部门信息（设置器）
     * @param string $value
     */
    protected function setDepartmentAttribute($value)
    {
        $this->attributes['department'] = Util::transformStrList($value);
    }

    /**
     * 部门排序信息（设置器）
     * @param string $value
     */
    protected function setOrderAttribute($value)
    {
        $this->attributes['order'] = Util::transformStrList($value);
    }

    /**
     * 是否部门管理员信息（设置器）
     * @param string $value
     */
    protected function setIsLeaderInDeptAttribute($value)
    {
        $this->attributes['is_leader_in_dept'] = Util::transformStrList($value);
    }

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'openid', 'userid', 'name', 'mobile', 'gender', 'email', 'open_userid', 'position', 'external_position', 'external_profile',
            'status', 'enable', 'extattr', 'telephone', 'hide_mobile', 'qr_code', 'avatar', 'thumb_avatar', 'alias', 'address',
            'department', 'isleader', 'is_leader_in_dept', 'order', 'main_department',
        ]);
    }
}
