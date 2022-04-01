<?php

namespace App\Jobs\Wechat;

use App\Models\Promise;

trait Taskable
{
    /**
     * 在超时之前任务可以运行的秒数
     *
     * @var int
     */
    public $timeout = 600;

    /**
     * 确定任务应该超时的时间
     *
     * @return \DateTime
     */
    public function retryUntil()
    {
        return now()->addMinutes(10)->addSeconds(10);
    }

    /**
     * 任务是否进行中
     * @param array $where
     */
    public static function isPending($where = [])
    {
        /** @var Promise $promise */
        $promise = Promise::query()->where($where)->where('taskable_type', static::TASK_TYPE)->orderByDesc('id')->first();
        if (empty($promise)) {
            return false;
        }

        if (now()->subMinutes(10)->gt($promise->created_at)) {
            $promise->setFailedStatus('超时自动完成');

            return false;
        }

        return $promise->isWaiting() || $promise->isPending();
    }

    /**
     * 检查任务并执行任务
     * @param array $checkOfWhere
     * @param ...$arguments
     * @return null
     */
    public static function checkForDispatch($checkOfWhere = [], ...$arguments)
    {
        if (self::isPending($checkOfWhere)) {
            return null;
        }

        return static::dispatch(Promise::make(static::TASK_TYPE, 0, $checkOfWhere), ...$arguments);
    }
}
