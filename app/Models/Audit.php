<?php

namespace App\Models;

class Audit extends Model
{
    // 初始化
    const STATUS_INITIAL = 0;

    // 已提交
    const STATUS_SUBMITTED = 1;

    // 已通过
    const STATUS_PASSED = 2;

    // 已拒绝
    const STATUS_REFUSED = 3;

    /**
     * 提交审核
     * @param array $attributes
     * @return bool
     */
    public function submit($attributes = [])
    {


        return $this->setStatus(static::STATUS_SUBMITTED, $attributes);
    }

    /**
     * 撤销审核
     * @param array $attributes
     * @return bool
     */
    public function revoke($attributes = [])
    {
        return $this->setStatus(static::STATUS_INITIAL, $attributes);
    }

    /**
     * 通过审核
     * @param array $attributes
     * @return bool
     */
    public function agree($attributes = [])
    {
        return $this->setStatus(static::STATUS_PASSED, $attributes);
    }

    /**
     * 审核不通过
     * @param array $attributes
     * @return bool
     */
    public function refuse($refuseMsg, $attributes = [])
    {
        $attributes['refuse_msg'] = $refuseMsg;

        return $this->setStatus(static::STATUS_REFUSED, $attributes);
    }

    /**
     * 更新状态
     * @param int $status
     * @param array $attributes
     * @return bool
     */
    protected function setStatus($status, $attributes = [])
    {
        $attributes = array_merge($attributes, [
            'status' => $status,
        ]);

        return $this->fill($attributes)->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * @return Model
     */
    public function extract()
    {
        $content = $this->getAttribute('content');

        /** @var Model $info */
        $info = $this->getAttribute('auditable');
        if (method_exists($info, 'applyAuditInfo')) {
            $info->applyAuditInfo($content);
        } else {
            $info->fill($content);
        }

        return $info;
    }
}
