<?php

namespace App\Models\Wechat;

use App\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * @property string $corp_id
 * @property string $agentid
 * @property string $secret
 * @property string $permanent_code
 * @property string $token
 * @property string $aes_key
 */
class WechatWorkAgent extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $casts = [
        'privilege' => 'json',
    ];

    // 自建应用授权
    const AUTH_TYPE_DEFAULT = 0;

    // 三方应用授权
    const AUTH_TYPE_SUITE = 1;

    // 应用代开发模式授权
    const AUTH_TYPE_CUSTOM_APP = 2;

    /**
     * @return bool
     */
    public function isDefaultAuthType()
    {
        return $this->getAttribute('auth_type') == static::AUTH_TYPE_DEFAULT;
    }

    /**
     * @return bool
     */
    public function isSiteAuthType()
    {
        return $this->getAttribute('auth_type') == static::AUTH_TYPE_SUITE;
    }

    /**
     * @return bool
     */
    public function isCustomAppAuthType()
    {
        return $this->getAttribute('auth_type') == static::AUTH_TYPE_CUSTOM_APP;
    }

    /**
     * 用户userID列表（获取器）
     * @param string $value
     * @return array|string[]
     */
    protected function getAllowUserinfosAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * 用户Tags列表（获取器）
     * @param string $value
     * @return array|string[]
     */
    protected function getAllowTagsAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * 部门id列表（获取器）
     * @param string $value
     * @return array|string[]
     */
    protected function getAllowPartysAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    /**
     * 用户userID列表（设置器）
     * @param string $value
     */
    protected function setAllowUserinfosAttribute($value)
    {
        $this->attributes['allow_userinfos'] = Util::transformStrList($value);
    }

    /**
     * 用户标签列表（设置器）
     * @param string $value
     */
    protected function setAllowTagsAttribute($value)
    {
        $this->attributes['allow_tags'] = Util::transformStrList($value);
    }

    /**
     * 部门id列表（设置器）
     * @param string $value
     */
    protected function setAllowPartysAttribute($value)
    {
        $this->attributes['allow_partys'] = Util::transformStrList($value);
    }

    /**
     * 获取允许更新的字段列表
     * @param array $result
     * @return array
     */
    public static function getAllowFields(array $result)
    {
        return Arr::only($result, [
            'corp_id', 'agentid', 'secret',
            'name', 'square_logo_url', 'description',
            'privilege', 'level',
            'close', 'permanent_code',
            'is_customized_app', 'customized_publish_status',
            'redirect_domain', 'report_location_flag', 'isreportenter', 'home_url',
            'permanent_code',
        ]);
    }
}
